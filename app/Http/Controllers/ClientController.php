<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
     //Add new client
     public function addNewClient(Request $request){
        $validated = Validator::make($request->all(),[
            'clientName' => 'required|string',
            'clientDescription' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $client = new Client();
            $client->clientName = $request->clientName;
            $client->clientDescription = $request->clientDescription;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images/client', 'public');
                $client->image_path = $path; // Store the path in the database
            }
            $client->save();

             //return
             return response()->json([
                'message' => 'New client added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

    //Edit client
    public function editClient(Request $request,$client_id){
        $validated = Validator::make($request->all(),[
            'clientName' => 'required|string',
            'clientDescription' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $client_data = Client::find($client_id);
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($client_data->image_path && \Storage::disk('public')->exists($client_data->image_path)) {
                    \Storage::disk('public')->delete($client_data->image_path);
                }

                // Upload the new image
                $newImagePath = $request->file('image')->store('images/client', 'public');
                $client_data->image_path = $newImagePath;
            }
           $updateClient = $client_data->update([
                'clientName' => $request->clientName,
                'clientDescription' => $request->clientDescription,
                'image_path' => $newImagePath,
            ]);
             //return
             return response()->json([
                'message' => 'Client updated successfully',
                'updated_client' => $updateClient,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //retrieve all client
    public function getAllClient(){
        try {
            $clients = Client::all();
            $formattedClients = $clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'clientName' => $client->clientName,
                    'clientDescription' => $client->clientDescription,
                    'image_url' => $client->image_path ? url('storage/' . $client->image_path) : null,
                    'created_at' => $client->created_at,
                    'updated_at' => $client->updated_at,
                ];
            });
            return response()->json([
                'Client' => $formattedClients
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }

    //fetch single Client
    public function getClient($client_id){
        try {
            $client = Client::where('id',$client_id)->first();
                return [
                    'id' => $client->id,
                    'clientName' => $client->clientName,
                    'clientDescription' => $client->clientDescription,
                    'image_url' => $client->image_path ? url('storage/' . $client->image_path) : null,
                    'created_at' => $client->created_at,
                    'updated_at' => $client->updated_at,
                ];
            
            // $post = Post::with('user','comment','likes')->where('id',$post_id)->first();
            //$client_data = Client::where('id',$client_id)->first();
            return response()->json([
                'Client' => $client
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete client
    public function deleteClient(Request $request, $client_id){
        try {
            $client = Client::find($client_id);
            if (!$client) {
                return response()->json(['error' => 'Post not found'], 404);
            }
             // Delete the associated image if it exists
             if ($client->image_path && \Storage::disk('public')->exists($client->image_path)) {
                \Storage::disk('public')->delete($client->image_path);
            }
            $client->delete();
            return response()->json([
                'message' => 'Client deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
