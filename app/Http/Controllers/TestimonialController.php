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
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $testimonial = new Testimonial();
            $testimonial->clientName = $request->clientName;
            $testimonial->clientReview = $request->clientReview;
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
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $testimonial_data = Testimonial::find($testimonial_id);

           $updateTestimonial = $testimonial_data->update([
                'clientName' => $request->clientName,
                'clientReview' => $request->clientReview,
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
                'testimonial' => $testimonial_data
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete Testimonial
    public function deleteTestimonial(Request $request, $testimonial_id){
        try {
            $testimonial = Testimonial::find($testimonial_id);
            $testimonial->delete();
            return response()->json([
                'message' => 'Testimonial deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }

}
