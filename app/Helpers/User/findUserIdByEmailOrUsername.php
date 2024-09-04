<?php

namespace App\Helpers\User;

use App\Models\User;

class userIdHelper
{
    /**
     * Find user ID by email or username.
     *
     * @param  string  $identifier  The email or username of the user.
     * @return array  An array with 'id' of the user if exists, otherwise an error message.
     */
    public static function findUserIdByEmailOrUsername($identifier)
    {
        // Determine if the identifier is an email
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

        // Query the user based on the identifier type
        $query = User::query();
        
        if ($isEmail) {
            $user = $query->where('email', $identifier)->first();
        } else {
            $user = $query->where('username', $identifier)->first();
        }

        // Return the result
        if ($user) {
            return ['id' => $user->id];
        }

        return ['error' => 'User not found'];
    }
}
