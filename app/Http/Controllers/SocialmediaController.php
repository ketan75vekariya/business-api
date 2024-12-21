<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socialmedia;
use Illuminate\Support\Facades\Validator;

class SocialmediaController extends Controller
{
    //Add new social media
    public function addNewSocialmedia(Request $request){
        $validated = Validator::make($request->all(),[
            'name' => 'required|string',
            'link' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $socialmedia = new Socialmedia();
            $socialmedia->name = $request->name;
            $socialmedia->link = $request->link;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images/socialmedia', 'public');
                $socialmedia->image_path = $path; // Store the path in the database
            }
            $socialmedia->save();

             //return
             return response()->json([
                'message' => 'New social media link added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

    //Edit socialmedia
    public function editSocialmedia(Request $request,$socialmedia_id){
        $validated = Validator::make($request->all(),[
            'name' => 'required|string',
            'link' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $socialmedia_data = Socialmedia::find($socialmedia_id);
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($socialmedia_data->image_path && \Storage::disk('public')->exists($socialmedia_data->image_path)) {
                    \Storage::disk('public')->delete($socialmedia_data->image_path);
                }

                // Upload the new image
                $newImagePath = $request->file('image')->store('images/socialmedia', 'public');
                $socialmedia_data->image_path = $newImagePath;
            }
           $updateSocialmedia = $socialmedia_data->update([
                'name' => $request->name,
                'link' => $request->link,
                'image_path' => $newImagePath,
            ]);
             //return
             return response()->json([
                'message' => 'Socialmedia link updated successfully',
                'updated_Socialmedia' => $updateSocialmedia,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //retrieve all socialmedia
    public function getAllSocialmedia(){
        try {
            $socialmedias = Socialmedia::all();
            $formattedsocialmedia = $socialmedias->map(function ($socialmedia) {
                return [
                    'id' => $socialmedia->id,
                    'name' => $socialmedia->name,
                    'link' => $socialmedia->link,
                    'image_url' => $socialmedia->image_path ? url('storage/' . $socialmedia->image_path) : null,
                    'created_at' => $socialmedia->created_at,
                    'updated_at' => $socialmedia->updated_at,
                ];
            });
            return response()->json([
                'socialmedias' => $socialmedias
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }

    //fetch single socialmedia
    public function getSocialmedia($socialmedia_id){
        try {
            $socialmedia = Socialmedia::find($socialmedia_id);
            // $post = Post::with('user','comment','likes')->where('id',$post_id)->first();
            $socialmedia_data = Socialmedia::where('id',$socialmedia_id)->first();
            return response()->json([
                'name' => $socialmedia_data->name,
                'link' => $socialmedia_data->link,
                'image_path'=>$socialmedia_data->image_path ? asset('storage/' . $socialmedia_data->image_path) : null,
                'created_at'=>$socialmedia_data->created_at,
                'updated_at'=>$socialmedia_data->updated_at,
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete socialmedia
    public function deleteSocialmedia(Request $request, $socialmedia_id){
        try {
            $socialmedia = Socialmedia::find($socialmedia_id);
            if (!$socialmedia) {
                return response()->json(['error' => 'Post not found'], 404);
            }
             // Delete the associated image if it exists
             if ($socialmedia->image_path && \Storage::disk('public')->exists($socialmedia->image_path)) {
                \Storage::disk('public')->delete($socialmedia->image_path);
            }
            $socialmedia->delete();
            return response()->json([
                'message' => 'socialmedia link deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
