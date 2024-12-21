<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    //Add new Team Member
    public function addNewTeam(Request $request){
        $validated = Validator::make($request->all(),[
            'employeeName' => 'required|string',
            'employeeDescription' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $team = new Team();
            $team->employeeName = $request->employeeName;
            $team->employeeDescription = $request->employeeDescription;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images/team', 'public');
                $team->image_path = $path; // Store the path in the database
            }
            $team->save();

             //return
             return response()->json([
                'message' => 'New team member added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

    //Edit Team
    public function editTeam(Request $request,$team_id){
        $validated = Validator::make($request->all(),[
            'employeeName' => 'required|string',
            'employeeDescription' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $team_data = Team::findOrFail($team_id);
            // Handle image upload
            if ($request->hasFile('image')) {
               // Delete the old image if it exists
               if ($team_data->image_path && \Storage::disk('public')->exists($team_data->image_path)) {
                   \Storage::disk('public')->delete($team_data->image_path);
               }

               // Upload the new image
               $newImagePath = $request->file('image')->store('images/team', 'public');
               $team_data->image_path = $newImagePath;
           }

           $updateTeam = $team_data->update([
                'employeeName' => $request->employeeName,
                'employeeDescription' => $request->employeeDescription,
                'image_path' => $newImagePath,
            ]);
             //return
             return response()->json([
                'message' => 'Team Member updated successfully',
                'updated_Member' => $updateTeam,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //retrieve all Team
    public function getAllTeam(){
        try {
            $team = Team::all();
            $formattedTeam = $team->map(function ($team) {
                return [
                    'id' => $team->id,
                    'employeeName' => $team->employeeName,
                    'employeeDescription' => $team->employeeDescription,
                    'image_url' => $team->image_path ? url('storage/' . $team->image_path) : null,
                    'created_at' => $team->created_at,
                    'updated_at' => $team->updated_at,
                ];
            });
            return response()->json([
                'Team' => $team
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }

    //fetch single Team member
    public function getTeam($team_id){
        try {
                $team = Team::find($team_id);
                // $post = Post::with('user','comment','likes')->where('id',$post_id)->first();
                $team_data = Team::where('id',$team_id)->first();
                return response()->json([
                    'team_id'=>$team_data->id,
                    'employeeName' => $team_data->employeeName,
                    'employeeDescription' => $team_data->employeeDescription,
                    'image_path'=>$team_data->image_path ? asset('storage/' . $team_data->image_path) : null,
                    'created_at'=>$team_data->created_at,
                    'updated_at'=>$team_data->updated_at,
                ],200);
            } catch (\Exception $th) {
                return response()->json(['error' => $th->getMessage()],403);
            }
    }
    //Delete Team member
    public function deleteTeam(Request $request, $team_id){
        try {
            $team = Team::find($team_id);
            if (!$team) {
                return response()->json(['error' => 'Post not found'], 404);
            }
             // Delete the associated image if it exists
             if ($team->image_path && \Storage::disk('public')->exists($team->image_path)) {
                \Storage::disk('public')->delete($team->image_path);
            }
            $team->delete();
            return response()->json([
                'message' => 'Team member deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
