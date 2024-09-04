<?php

namespace App\Services;

use App\Helpers\User\userIdHelper; // Import the userIdHelper
use App\Models\Team;
use App\Models\User;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeamService
{
 
    public function addTeam(Request $request)
    {
        // Get the authenticated user
        $authUser = Auth::user();

        // Check if the authenticated user can perform the create-team task
        if (!canUserPerformTask($authUser, 'create-team')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'team_name' => 'required|string|max:255|unique:teams,team_name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the new team
        $team = Team::create([
            'team_name' => $request->team_name,
        ]);

        return response()->json(['message' => 'Team created successfully', 'team' => $team], 201);
    }

    public function deleteTeam(Request $request)
{
    // Get the authenticated user
    $authUser = Auth::user();

    // Check if the authenticated user can perform the delete-team task
    if (!canUserPerformTask($authUser, 'delete-team')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Validate the request data
    $validator = Validator::make($request->all(), [
        'team_name' => 'required|string|exists:teams,team_name',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Find the team by name
    $team = Team::where('team_name', $request->team_name)->first();

    if (!$team) {
        return response()->json(['message' => 'Team not found'], 404);
    }

    // Delete the team
    $team->delete();

    return response()->json(['message' => 'Team deleted successfully'], 200);
}

    public function setTeamLead(Request $request)
    {
        // Get the authenticated user
        $authUser = Auth::user();

        // Check if the authenticated user can perform the set-team-lead task
        if (!canUserPerformTask($authUser, 'set-team-lead')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'team_name' => 'required|string',
            'new_team_lead' => 'required|string', // Accept email, username, or ID
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $newTeamLeadId = $request->new_team_lead;

        // Check if the provided new team lead is an ID or not
        if (!is_numeric($newTeamLeadId)) {
            // Use the helper function to find the user ID by email or username
            $result = userIdHelper::findUserIdByEmailOrUsername($newTeamLeadId);

            // Check if the user ID was found
            if (!isset($result['id'])) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $newTeamLeadId = $result['id'];
        }

        // Validate if the user exists if an ID was provided directly
        if (is_numeric($newTeamLeadId) && !User::find($newTeamLeadId)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Find the team by name
        $team = Team::where('team_name', $request->team_name)->first();

        if (!$team) {
            return response()->json(['message' => 'Team not found'], 404);
        }

        // Set the new team lead
        TeamMember::updateOrCreate(
            ['team_id' => $team->id, 'user_id' => $newTeamLeadId],
            ['is_team_lead' => true]
        );

        // Set the previous team lead to false
        TeamMember::where('team_id', $team->id)
            ->where('is_team_lead', true)
            ->where('user_id', '!=', $newTeamLeadId)
            ->update(['is_team_lead' => false]);

        return response()->json(['message' => 'Team lead updated successfully'], 200);
    }

    public function addTeamMembers(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'members' => 'required|array', // Expecting an array of members
            'members.*.team_name' => 'required|string',
            'members.*.email_or_username' => 'required|string', // Handle both email and username
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the authenticated user
        $authUser = Auth::user();

        // Check if the authenticated user can perform the add-team-members task
        if (!canUserPerformTask($authUser, 'add-team-members')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $responseMessages = [];

        // Process each member in the request
        foreach ($request->members as $memberData) {
            // Find the team by name
            $team = Team::where('team_name', $memberData['team_name'])->first();

            if (!$team) {
                $responseMessages[] = ['team' => $memberData['team_name'], 'message' => 'Team not found'];
                continue;
            }

            // Use the helper function to find the user ID by email or username
            $result = userIdHelper::findUserIdByEmailOrUsername($memberData['email_or_username']);

            if (isset($result['id'])) {
                TeamMember::create(
                    ['team_id' => $team->id, 'user_id' => $result['id']]
                );

                $responseMessages[] = [
                    'team' => $memberData['team_name'],
                    'user' => $memberData['email_or_username'],
                    'message' => 'User added to the team successfully',
                ];
            } else {
                $responseMessages[] = [
                    'team' => $memberData['team_name'],
                    'user' => $memberData['email_or_username'],
                    'message' => 'User not found'
                ];
            }
        }

        // Return a consolidated response for all processed members
        return response()->json(['results' => $responseMessages], 200);
    }

    public function removeTeamMember(Request $request)
    {
        // Get the authenticated user
        $authUser = Auth::user();

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'team_name' => 'required|string',
            'email_or_username' => 'required|string', // Handle both email and username
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the team by name
        $team = Team::where('team_name', $request->team_name)->first();

        if (!$team) {
            return response()->json([
                'message' => 'Team not found',
                'team_name' => $request->team_name
            ], 404);
        }

        // Use the helper function to find the user ID by email or username
        $result = userIdHelper::findUserIdByEmailOrUsername($request->email_or_username);

        if (!isset($result['id'])) {
            return response()->json([
                'message' => 'User not found',
                'email_or_username' => $request->email_or_username
            ], 404);
        }

        $userId = $result['id'];

        // Find the team member record
        $teamMember = TeamMember::where('team_id', $team->id)
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return response()->json([
                'message' => 'User is not a member of the team',
                'team_name' => $request->team_name,
                'email_or_username' => $request->email_or_username
            ], 404);
        }

        // Check if the user to be removed is a team lead
        $isTeamLead = $teamMember->is_team_lead;

        // Check permissions based on the user's role
        if ($isTeamLead) {
            // Check if the authenticated user has permission to remove team leads
            if (!canUserPerformTask($authUser, 'remove-team-lead')) {
                return response()->json([
                    'message' => 'Unauthorized to remove team lead',
                    'team_name' => $request->team_name,
                    'email_or_username' => $request->email_or_username
                ], 403);
            }
        } else {
            // Check if the authenticated user has permission to remove team members
            if (!canUserPerformTask($authUser, 'remove-team-member')) {
                return response()->json([
                    'message' => 'Unauthorized to remove team member',
                    'team_name' => $request->team_name,
                    'email_or_username' => $request->email_or_username
                ], 403);
            }
        }

        // Remove the user from the team
        $teamMember->delete();

        return response()->json([
            'message' => 'User removed from the team successfully',
            'team_name' => $request->team_name,
            'email_or_username' => $request->email_or_username
        ], 200);
    }

    public function removeMultipleTeamMembers(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'members' => 'required|array',
            'members.*.team_name' => 'required|string',
            'members.*.email_or_username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $responses = [];
        $errors = [];

        foreach ($request->members as $data) {
            // Prepare a new request for each team member
            $subRequest = new Request([
                'team_name' => $data['team_name'],
                'email_or_username' => $data['email_or_username']
            ]);

            // Call the existing function
            $response = $this->removeTeamMember($subRequest);

            // Collect response or error
            $responseData = json_decode($response->getContent(), true);

            if ($response->status() === 200) {
                $responses[] = $responseData;
            } else {
                $errors[] = $responseData;
            }
        }

        // Return results
        return response()->json([
            'responses' => $responses,
            'errors' => $errors,
        ], 200);
    }
}
