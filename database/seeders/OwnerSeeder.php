<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the user
        $user = User::create([
            'first_name' => 'Umar',
            'last_name' => 'Draz',
            'email' => 'admin@mudsoft.tech',
            'username' => 'umardaraz012',
            'pass' => Hash::make('shahbaz654'), // Hashing the password
            'status' => true,
        ]);

        // Ensure the 'Owner' role exists with the correct guard
        $role = Role::where('name', 'Owner')->where('guard_name', 'api')->first(); // Adjust 'web' if needed

        // Assign the 'Owner' role to the user
        if ($role) {
            $user->assignRole($role); 
        } else {
            // Log the error or handle the missing role
            $this->command->error("Role 'Owner' with guard 'web' does not exist.");
        }
    }
}
