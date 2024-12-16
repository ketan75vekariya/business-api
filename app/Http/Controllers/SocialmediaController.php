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
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $socialmedia = new Socialmedia();
            $socialmedia->name = $request->name;
            $socialmedia->link = $request->link;
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
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $socialmedia_data = Socialmedia::find($socialmedia_id);

           $updateSocialmedia = $socialmedia_data->update([
                'name' => $request->name,
                'link' => $request->link,
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
            $socialmedia = Socialmedia::all();
            return response()->json([
                'socialmedias' => $socialmedia
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
                'socialmedia' => $socialmedia_data
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete socialmedia
    public function deleteSocialmedia(Request $request, $socialmedia_id){
        try {
            $socialmedia = Socialmedia::find($socialmedia_id);
            $socialmedia->delete();
            return response()->json([
                'message' => 'socialmedia link deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
