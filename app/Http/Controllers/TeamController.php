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
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $team = new Team();
            $team->employeeName = $request->employeeName;
            $team->employeeDescription = $request->employeeDescription;
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
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $team_data = Team::find($team_id);

           $updateTeam = $team_data->update([
                'employeeName' => $request->employeeName,
                'employeeDescription' => $request->employeeDescription,
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
                'Team Member' => $team_data
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }
    //Delete Team member
    public function deleteTeam(Request $request, $team_id){
        try {
            $team = Team::find($team_id);
            $team->delete();
            return response()->json([
                'message' => 'Team member deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
