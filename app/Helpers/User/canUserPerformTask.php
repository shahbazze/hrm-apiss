<?php
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

if (!function_exists('canUserPerformTask')) {
    /**
     * Check if the user can perform the task based on their role and permissions.
     *
     * @param  \App\Models\User  $user
     * @param  string  $permissionName
     * @return bool
     */
    function canUserPerformTask($user, $permissionName)
    {
        // Check if the user has the 'Owner' role
        if ($user->hasRole('Owner')) {
            return true; // Allow the task without checking permission
        }

        // Check if the user has the required permission directly
        // if ($user->can($permissionName)) {
        //     return true;
        // }

        // Retrieve all roles assigned to the user
        $roles = $user->roles;

        foreach ($roles as $role) {
            // Retrieve permissions for the role
            $permissions = $role->permissions;

            // Check if any permission matches the required permission
            foreach ($permissions as $permission) {
                if ($permission->name === $permissionName) {
                    return true;
                }
            }
        }

        // If no permission matches, return false
        return false;
    }
}
