<?php

namespace App\Http\Controllers;
use App\Models\Abouts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutController extends Controller
{
    public function addNewAbout(Request $request)
    {
        // Validation
        $validated = Validator::make($request->all(), [
            'aboutTitle' => 'required|string',
            'aboutDescription' => 'required|string',
            'whyUs' => 'nullable|string',
            'goal' => 'nullable|string',
            'mission' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($validated->fails()) {
            return response()->json($validated->errors(), 403);
        }
    
        try {
            $about = new Abouts();
            $about->aboutTitle = $request->aboutTitle;
            $about->aboutDescription = $request->aboutDescription;
            $about->goal = $request->goal;
            $about->mission = $request->mission;
            $about->aboutTitle = $request->aboutTitle;
            $about->whyUs = $request->whyUs;
             // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images/about', 'public');
                $about->image_path = $path; // Store the path in the database
            }
            $about->save();
    
            
            // Generate full image URL if path exists
            $imageUrl = $path ? url('storage/' . $path) : null;
    
            // Return success response
            return response()->json([
                'message' => 'About Data added successfully',
                'data' => [
                    'id' => $about->id,
                    'aboutTitle' => $about->aboutTitle,
                    'aboutDescription' => $about->aboutDescription,
                    'whyUs' => $about->whyUs,
                    'goal' => $about->goal,
                    'mission' => $about->mission,
                    'image_url' => $imageUrl, // Return full image URL
                ],
            ], 200);
    
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    //update
    public function editAbout(Request $request,$about_id){
        $validated = Validator::make($request->all(),[
            'aboutTitle' => 'required|string',
            'aboutDescription'=>'required|string',
            'whyUs'=>'string',
            'goal'=>'string',
            'mission'=>'string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $about_data = Abouts::findOrFail($about_id);
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($about_data->image_path && \Storage::disk('public')->exists($about_data->image_path)) {
                    \Storage::disk('public')->delete($about_data->image_path);
                }

                // Upload the new image
                $newImagePath = $request->file('image')->store('images/about', 'public');
                $about_data->image_path = $newImagePath;
            }
           $updateAboutData = $about_data->update([
                'aboutTitle' => $request->aboutTitle,
                'aboutDescription' => $request->aboutDescription,
                'whyUs' => $request->whyUs,
                'goal'=>$request->goal,
                'mission'=>$request->mission,
                'image_path' =>$newImagePath,
            ]);
             //return
             return response()->json([
                'message' => 'Description updated successfully',
                'updated_data' => $about_data,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //retrieve about
    public function getAllAbouts(){
        try {
            $abouts = Abouts::all();
            $formattedAbouts = $abouts->map(function ($about) {
                return [
                    'id' => $about->id,
                    'title' => $about->aboutTitle,
                    'description' => $about->aboutDescription,
                    'whyUs' => $about->whyUs,
                    'goal' => $about->goal,
                    'mission' => $about->mission,
                    'image_url' => $about->image_path ? url('storage/' . $about->image_path) : null,
                    'created_at' => $about->created_at,
                    'updated_at' => $about->updated_at,
                ];
            });
            return response()->json([
                'Abouts' => $formattedAbouts
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }
    //Delete About
    public function deleteAbout(Request $request, $about_id){
        try {
            $about = About::find($about_id);
            if (!$about) {
                return response()->json(['error' => 'Post not found'], 404);
            }
             // Delete the associated image if it exists
             if ($about->image_path && \Storage::disk('public')->exists($about->image_path)) {
                \Storage::disk('public')->delete($about->image_path);
            }
            $about->delete();
            return response()->json([
                'message' => 'about deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
