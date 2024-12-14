<?php

namespace App\Http\Controllers;
use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function editHome(Request $request,$home_id){
        $validated = Validator::make($request->all(),[
            'homeDescription' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $home_data = Home::find($home_id);

           $updateHomeDescription = $home_data->update([
                'homeDescription' => $request->homeDescription,
            ]);
             //return
             return response()->json([
                'message' => 'Description updated successfully',
                'updated_description' => $updateHomeDescription,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Add new Home Descriptio
    public function addNewHome(Request $request){
        $validated = Validator::make($request->all(),[
            'homeDescription' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $home = Home::create([
                'homeDescription' => $request->homeDescription,
            ]);

             //return
             return response()->json([
                'message' => 'Description added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
}
