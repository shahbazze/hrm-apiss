<?php

namespace App\Helpers\User;

use Illuminate\Support\Facades\DB;

class UsernameHelper
{
    /**
     * Generate a unique username based on the user's email.
     *
     * @param string $email The user's email address.
     * @return string The generated unique username.
     */
    public static function generateUsername(string $email): string
    {
        // Extract the part before the '@' symbol from the email
        $usernameBase = explode('@', $email)[0];

        // Fetch the highest number associated with usernames that match the base
        $highestNumber = DB::table('users')
            ->where('username', 'LIKE', $usernameBase . '%')
            ->where('username', 'regexp', '^' . $usernameBase . '[0-9]*$')
            ->selectRaw('MAX(CAST(SUBSTRING(username, LENGTH(?) + 1) AS UNSIGNED)) AS max_number', [$usernameBase])
            ->pluck('max_number')
            ->first();

        // If no such usernames exist, start with the base username; otherwise, increment the highest number found
        $newNumber = $highestNumber !== null ? ($highestNumber + 1) : null;

        // Generate the new unique username
        $newUsername = $newNumber !== null ? $usernameBase . $newNumber : $usernameBase;

        return $newUsername;
    }
}
