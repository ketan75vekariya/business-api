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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $service = new Service();
            $service->serviceTitle = $request->serviceTitle;
            $service->serviceDescription = $request->serviceDescription;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images/service', 'public');
                $service->image_path = $path; // Store the path in the database
            }
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $service_data = Service::findOrFail($service_id);
             // Handle image upload
             if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($service_data->image_path && \Storage::disk('public')->exists($service_data->image_path)) {
                    \Storage::disk('public')->delete($service_data->image_path);
                }

                // Upload the new image
                $newImagePath = $request->file('image')->store('images/service', 'public');
                $service_data->image_path = $newImagePath;
            }
            $updateService = $service_data->update([
                'serviceTitle' => $request->serviceTitle,
                'serviceDescription' => $request->serviceDescription,
                'image_path' => $newImagePath,
            ]);
             //return
             return response()->json([
                'message' => 'Service updated successfully',
                'updated_Service' => $service_data,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //retrieve all service
    public function getAllServices(){
        try {
            $services = Service::all();
            $formattedServices = $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->serviceTitle,
                    'description' => $service->serviceDescription,
                    'image_url' => $service->image_path ? url('storage/' . $service->image_path) : null,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                ];
            });
            return response()->json([
                'services' => $formattedServices
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
                'serviceTitle' => $service_data->serviceTitle,
                'serviceDescription' => $service_data->serviceDescription,
                'image_path'=>$service_data->image_path ? asset('storage/' . $service_data->image_path) : null,
                'created_at'=>$service_data->created_at,
                'updated_at'=>$service_data->updated_at,
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete Service
    public function deleteService(Request $request, $service_id){
        try {
            $service = Service::find($service_id);
            if (!$service) {
                return response()->json(['error' => 'Post not found'], 404);
            }
             // Delete the associated image if it exists
             if ($service->image_path && \Storage::disk('public')->exists($service->image_path)) {
                \Storage::disk('public')->delete($service->image_path);
            }
            $service->delete();
            return response()->json([
                'message' => 'post deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
