<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    //Add new message
    public function addNewMessage(Request $request){
        $validated = Validator::make($request->all(),[
            'name' => 'required|string',
            'email' => 'required|string',
            'message' => 'required|string'
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $message = new Message();
            $message->name = $request->name;
            $message->email = $request->email;
            $message->message = $request->message;
            $message->save();

             //return
             return response()->json([
                'message' => 'New message added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

    //Edit message
    public function editMessage(Request $request,$message_id){
        $validated = Validator::make($request->all(),[
            'name' => 'required|string',
            'email' => 'required|string',
            'message'=>'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $message_data = Message::find($message_id);

           $updateMessage = $message_data->update([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
            ]);
             //return
             return response()->json([
                'message' => 'message updated successfully',
                'updated_message' => $updateMessage,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //retrieve all message
    public function getAllMessages(){
        try {
            $messages = Message::all();
            return response()->json([
                'messages' => $messages
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }

    //fetch single message
    public function getMessage($message_id){
        try {
            $message = Message::find($message_id);
            // $post = Post::with('user','comment','likes')->where('id',$post_id)->first();
            $message_data = Message::where('id',$message_id)->first();
            return response()->json([
                'message' => $message_data
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete message
    public function deleteMessage(Request $request, $message_id){
        try {
            $message = Message::find($message_id);
            $message->delete();
            return response()->json([
                'message' => 'Message deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
