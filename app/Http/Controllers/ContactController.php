<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    //Add new contact
    public function addNewContact(Request $request){
        $validated = Validator::make($request->all(),[
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|string',
            
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $contact = new Contact();
            $contact->address = $request->address;
            $contact->phone = $request->phone;
            $contact->email = $request->email;
            $contact->save();

             //return
             return response()->json([
                'message' => 'Contact added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

    //Edit Contact
    public function editContact(Request $request,$contact_id){
        $validated = Validator::make($request->all(),[
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $contact_data = Contact::find($contact_id);

           $updateContact = $contact_data->update([
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
             //return
             return response()->json([
                'message' => 'Contact updated successfully',
                'updated_Contact' => $updateContact,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

     //retrieve all Contact
     public function getAllContacts(){
        try {
            $contacts = Contact::all();
            return response()->json([
                'contacts' => $contacts
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }
    //fetch single Contact
    public function getContact($contact_id){
        try {
            $contact = Contact::find($contact_id);
            // $post = Post::with('user','comment','likes')->where('id',$post_id)->first();
            $contact_data = Contact::where('id',$contact_id)->first();
            return response()->json([
                'contact' => $contact_data
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete Contact
    public function deleteContact(Request $request, $contact_id){
        try {
            $contact = Contact::find($contact_id);
            $contact->delete();
            return response()->json([
                'message' => 'Contact info deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
