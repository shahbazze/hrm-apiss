<?php

namespace App\Services;



use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\User\UsernameHelper; // Import the UsernameHelper
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     * Handle the deletion of a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser(Request $request)
    {
        // Validate the request
        $request->validate([
            'identifier' => 'required|string', // The identifier can be either username or email
        ]);


        // Get the authenticated user
        $authUser = Auth::user();

        // Find the target user by username or email
        $user = User::where('username', $request->identifier)
            ->orWhere('email', $request->identifier)
            ->first();

        // Handle if user not found
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }


        // Check if the authenticated user can perform the delete task
        if (!canUserPerformTask($authUser, 'delete-users')) {
            return response()->json(['message' => 'Unauthorized hy tu'], 403);
        }


        // Get the role IDs (assuming you have a way to get role IDs)
        $authUserRoleId = $authUser->roles->first()->id;
        $userRoleId = $user->roles->first()->id;


        // Check if the authenticated user has the necessary role level
        if ($authUserRoleId > $userRoleId) {
            return response()->json(['message' => 'Unauthorized to delete this user'], 403);
        }


        // Perform the delete operation
        $this->performDelete($user);


        return response()->json(['message' => 'User deleted successfully'], 200);
    }


    /**
     * Perform the actual deletion of the user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function performDelete(User $user)
    {
        // Delete user's avatar if it exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }


        // Delete the user account
        $user->delete();
    }




    public function addUser(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'pass' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name', // Ensure the role exists
        ]);


        // Get the authenticated user
        $authUser = Auth::user();

        // Find the role being assigned to the new user
        $newUserRole = Role::findByName($request->role, 'api'); // Adjust the guard as needed

        // Get the role of the authenticated user
        $authUserRole = $authUser->roles->first();

        if (!$authUserRole) {
            return response()->json(['message' => 'Authenticated user role not found'], 403);
        }


        // Check the role hierarchy
        $authRoleId = $authUserRole->id;
        $newRoleId = $newUserRole->id;


        if ($authRoleId === 1) {
            // Owners can only add users with roles greater than their own (lower role id)
            if ($newRoleId <= $authRoleId) {
                return response()->json(['message' => 'Owners can only add users with roles lower than their own'], 403);
            }
        } else {
            // Users can add users with the same role or greater role
            if ($newRoleId < $authRoleId) {
                return response()->json(['message' => 'You can only add users with the same role or greater'], 403);
            }
        }


        // Generate a unique username based on the email
        $username = UsernameHelper::generateUsername($request->email);


        // Create a new user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $username,
            'pass' => Hash::make($request->pass),
            'status' => true,
        ]);


        // Assign the specified role to the user
        $user->assignRole($newUserRole);


        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }
}
