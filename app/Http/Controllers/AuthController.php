<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user and create a token.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // protected application so doe not need this method

    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'first_name' => 'required|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'username' => 'required|string|max:255|unique:users',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     $user = User::create([
    //         'first_name' => $request->first_name,
    //         'last_name' => $request->last_name,
    //         'email' => $request->email,
    //         'username' => $request->username,
    //         'pass' => Hash::make($request->password), // Using 'pass' as the password field
    //     ]);

    //     $tokenResult = $user->createToken('Personal Access Token');
    //     $token = $tokenResult->plainTextToken;

    //     return response()->json([
    //         'accessToken' => $token,
    //         'token_type' => 'Bearer',
    //     ]);
    // }
    /**
     * Login a user and create a token.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'pass' => 'required|string',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->pass, $user->pass)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;
    
        return response()->json([
            'accessToken' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    

    /**
     * Get the authenticated user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout the user (revoke the token).
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }
}
