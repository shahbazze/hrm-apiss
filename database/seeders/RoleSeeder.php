<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database with roles.
     *
     * @return void
     */
    public function run()
    {
        // Define role names and their corresponding guard names
        $roles = [
            ['name' => 'Owner', 'guard_name' => 'api'],
            ['name' => 'Admin', 'guard_name' => 'api'],
            ['name' => 'HR', 'guard_name' => 'api'],
            ['name' => 'Project Manager', 'guard_name' => 'api'],
            ['name' => 'Team Lead', 'guard_name' => 'api'],
            ['name' => 'Team member', 'guard_name' => 'api'],
        ];

        // Create roles with the guard name corresponding to each role
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
