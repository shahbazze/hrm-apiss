<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class OwnerController extends Controller
{

    public function setRolePermissions(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'roles_permissions' => 'required|array', // Ensure the request body is an array
        'roles_permissions.*.role' => 'required|string|exists:roles,name', // Ensure the role exists
        'roles_permissions.*.permissions' => 'required|string|exists:permissions,name', // Validate each permission
    ]);

    // Initialize an array to keep track of updated roles
    $updatedRoles = [];

    // Process each role-permissions object
    foreach ($request->roles_permissions as $item) {
        $role = Role::findByName($item['role'], 'api'); // Adjust the guard as needed
        
        // Sync the permissions for the role
        $role->syncPermissions([$item['permissions']]);

        // Record the role update
        $updatedRoles[] = $item['role'];
    }

    return response()->json([
        'message' => 'Permissions updated successfully for the roles.',
        'roles_updated' => array_unique($updatedRoles) // Return the list of roles updated
    ]);
}


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
