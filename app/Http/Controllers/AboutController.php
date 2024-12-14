<?php

namespace App\Http\Controllers;
use App\Models\Abouts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutController extends Controller
{
    public function addNewAbout(Request $request){
        $validated = Validator::make($request->all(),[
            'aboutTitle' => 'required|string',
            'aboutDescription'=>'required|string',
            'whyUs'=>'string',
            'goal'=>'string',
            'mission'=>'string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $about = Abouts::create([
                'aboutTitle' => $request->aboutTitle,
                'aboutDescription' => $request->aboutDescription,
                'whyUs' => $request->whyUs,
                'goal'=>$request->goal,
                'mission'=>$request->mission,

            ]);

             //return
             return response()->json([
                'message' => 'About Data added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
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
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $about_data = Abouts::find($about_id);

           $updateAboutData = $about_data->update([
                'aboutTitle' => $request->aboutTitle,
                'aboutDescription' => $request->aboutDescription,
                'whyUs' => $request->whyUs,
                'goal'=>$request->goal,
                'mission'=>$request->mission,
            ]);
             //return
             return response()->json([
                'message' => 'Description updated successfully',
                'updated_data' => $updateAboutData,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //retrieve about
    public function getAllAbouts(){
        try {
            $abouts = Abouts::all();
            return response()->json([
                'Abouts' => $abouts
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }
}
