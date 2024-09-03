<?php



// database/seeders/PermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'create-team',
            'add-team-member',
            'remove-team-member',
            'delete-user',
            // Add other permissions if necessary
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }
    }
}
