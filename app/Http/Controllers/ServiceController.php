<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    //Add new post
    public function addNewService(Request $request){
        $validated = Validator::make($request->all(),[
            'serviceTitle' => 'required|string',
            'serviceDescription' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $service = new Service();
            $service->serviceTitle = $request->serviceTitle;
            $service->serviceDescription = $request->serviceDescription;
            $service->save();

             //return
             return response()->json([
                'message' => 'Service added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

    //Edit Service
    public function editService(Request $request,$service_id){
        $validated = Validator::make($request->all(),[
            'serviceTitle' => 'required|string',
            'serviceDescription' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $service_data = Service::find($service_id);

           $updateService = $service_data->update([
                'serviceTitle' => $request->serviceTitle,
                'serviceDescription' => $request->serviceDescription,
            ]);
             //return
             return response()->json([
                'message' => 'Service updated successfully',
                'updated_Service' => $updateService,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //retrieve all service
    public function getAllServices(){
        try {
            $services = Service::all();
            return response()->json([
                'services' => $services
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }

    //fetch single service
    public function getService($service_id){
        try {
            $service = Service::find($service_id);
            // $post = Post::with('user','comment','likes')->where('id',$post_id)->first();
            $service_data = Service::where('id',$service_id)->first();
            return response()->json([
                'service' => $service_data
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete Service
    public function deleteService(Request $request, $service_id){
        try {
            $service = Service::find($service_id);
            $service->delete();
            return response()->json([
                'message' => 'post deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
