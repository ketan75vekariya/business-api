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
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $client = new Client();
            $client->clientName = $request->clientName;
            $client->clientDescription = $request->clientDescription;
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
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $client_data = Client::find($client_id);

           $updateClient = $client_data->update([
                'clientName' => $request->clientName,
                'clientDescription' => $request->clientDescription,
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
            $client = Client::all();
            return response()->json([
                'Client' => $client
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }

    //fetch single Client
    public function getClient($client_id){
        try {
            $client = Client::find($client_id);
            // $post = Post::with('user','comment','likes')->where('id',$post_id)->first();
            $client_data = Client::where('id',$client_id)->first();
            return response()->json([
                'Client' => $client_data
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete client
    public function deleteClient(Request $request, $client_id){
        try {
            $client = Client::find($client_id);
            $client->delete();
            return response()->json([
                'message' => 'Client deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
