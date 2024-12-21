<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
     //Add new Testimonial
     public function addNewTestimonial(Request $request){
        $validated = Validator::make($request->all(),[
            'clientName' => 'required|string',
            'clientReview' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $testimonial = new Testimonial();
            $testimonial->clientName = $request->clientName;
            $testimonial->clientReview = $request->clientReview;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images/testimonial', 'public');
                $testimonial->image_path = $path; // Store the path in the database
            }
            $testimonial->save();

             //return
             return response()->json([
                'message' => 'Testimonial added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

    //Edit Testimonial
    public function editTestimonial(Request $request,$testimonial_id){
        $validated = Validator::make($request->all(),[
            'clientName' => 'required|string',
            'clientReview' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $testimonial_data = Testimonial::findOrFail($testimonial_id);
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($testimonial_data->image_path && \Storage::disk('public')->exists($testimonial_data->image_path)) {
                    \Storage::disk('public')->delete($testimonial_data->image_path);
                }

                // Upload the new image
                $newImagePath = $request->file('image')->store('images/testimonial', 'public');
                $testimonial_data->image_path = $newImagePath;
            }
           $updateTestimonial = $testimonial_data->update([
                'clientName' => $request->clientName,
                'clientReview' => $request->clientReview,
                'image_path' => $newImagePath,
            ]);
             //return
             return response()->json([
                'message' => 'Service updated successfully',
                'updated_testimonial' => $updateTestimonial,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

    //retrieve all testimonial
    public function getAllTestimonial(){
        try {
            $testimonials = Testimonial::all();
            $formattedTestimonial = $testimonials->map(function ($testimonial) {
                return [
                    'id' => $testimonial->id,
                    'clientName' => $testimonial->clientName,
                    'clientReview' => $testimonial->clientReview,
                    'image_url' => $testimonial->image_path ? url('storage/' . $testimonial->image_path) : null,
                    'created_at' => $testimonial->created_at,
                    'updated_at' => $testimonial->updated_at,
                ];
            });
            return response()->json([
                'testimonials' => $testimonials
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }

    //fetch single Testimonial
    public function getTestimonial($testimonial_id){
        try {
            $testimonial = Testimonial::find($testimonial_id);
            // $post = Post::with('user','comment','likes')->where('id',$post_id)->first();
            $testimonial_data = Testimonial::where('id',$testimonial_id)->first();
            return response()->json([
                'clientName' => $testimonial_data->clientName,
                'clientReview' => $testimonial_data->clientReview,
                'image_path'=>$testimonial_data->image_path ? asset('storage/' . $testimonial_data->image_path) : null,
                'created_at'=>$testimonial_data->created_at,
                'updated_at'=>$testimonial_data->updated_at,
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete Testimonial
    public function deleteTestimonial(Request $request, $testimonial_id){
        try {
            $testimonial = Testimonial::find($testimonial_id);
            if (!$testimonial) {
                return response()->json(['error' => 'Post not found'], 404);
            }
             // Delete the associated image if it exists
             if ($testimonial->image_path && \Storage::disk('public')->exists($testimonial->image_path)) {
                \Storage::disk('public')->delete($testimonial->image_path);
            }
            $testimonial->delete();
            return response()->json([
                'message' => 'Testimonial deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }

}
