<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Services\UserService; // Import the UserService


class UserController extends Controller
{
    protected $userService;


    // Inject UserService into the controller
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * Update user account settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAccountSettings(Request $request)
    {
        $user = $request->user(); // Get the authenticated user


        // Validate the incoming request data
        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'avatar' => 'sometimes|file|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_avatar' => 'sometimes|boolean',
        ]);


        // Update user details if present in the request
        if ($request->has('first_name')) {
            $user->first_name = $request->input('first_name');
        }


        if ($request->has('last_name')) {
            $user->last_name = $request->input('last_name');
        }


        if ($request->has('email')) {
            $user->email = $request->input('email');
        }


        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Validate the uploaded file
            $file = $request->file('avatar');
            $avatarPath = $file->store('avatars', 'public');

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = $avatarPath;
        }


        // Handle avatar removal
        if ($request->input('remove_avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
                $user->avatar = null;
            }
        }


        // Save updated user details
        $user->save();


        return response()->json(['message' => 'Account settings updated successfully.']);
    }


    /**
     * Delete the user account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount(Request $request)
    {
        $user = $request->user(); // Get the authenticated user


        // Use UserService to delete the user account
        return $this->userService->deleteUser($request);
    }
    /**
     * Handle the request to add a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUser(Request $request)
    {
        // Call the UserService to handle user creation
        return $this->userService->addUser($request);
    }

    /**
     * Add a new team.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTeam(Request $request)
    {
        // Call the UserService to handle team creation
        return $this->userService->addTeam($request);
    }
}
