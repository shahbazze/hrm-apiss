<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Import the Storage facade
use Spatie\Permission\Models\Role;
use App\Helpers\User\UsernameHelper; // Import the UsernameHelper
use Spatie\Permission\Models\Permission;
class OwnerController extends Controller
{
   
   

    /**
     * Assign permissions to a role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setRolePermissions(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'role' => 'required|string|exists:roles,name', // Ensure the role exists
            'permissions' => 'required|array', // Permissions should be an array
            'permissions.*' => 'string|exists:permissions,name', // Validate each permission
        ]);

        // Find the role by name
        $role = Role::findByName($request->role, 'api'); // Adjust the guard as needed

        // Sync the permissions for the role
        $role->syncPermissions($request->permissions);

        return response()->json(['message' => 'Permissions updated successfully for the role.']);
    }

     /**
     * Add a new permission to the system.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermissions(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'permissions' => 'required|array|min:1', // Ensure 'permissions' is an array with at least one item
            'permissions.*.name' => 'required|string|unique:permissions,name|max:255',
            'permissions.*.guard_name' => 'sometimes|string|max:255', // Optional guard_name for each permission
        ]);
    
        $createdPermissions = [];
    
        // Iterate through the permissions array and create each permission
        foreach ($request->permissions as $permissionData) {
            $guardName = $permissionData['guard_name'] ?? 'api'; // Default to 'api' if not provided
    
            // Create the new permission
            $permission = Permission::create([
                'name' => $permissionData['name'],
                'guard_name' => $guardName,
            ]);
    
            $createdPermissions[] = $permission;
        }
    
        return response()->json(['message' => 'Permissions created successfully', 'permissions' => $createdPermissions], 201);
    }
    
}
