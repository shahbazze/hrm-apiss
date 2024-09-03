<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin user
        $user = User::create([
            'first_name' => 'Muhammad',
            'last_name' => 'Shahbaz',
            'email' => 'shahbaz@mudsoft.tech',
            'username' => 'shahbaz',
            'pass' => Hash::make('shahbaz654'), // Hashing the password
            'status' => true,
        ]);

        // Ensure the 'Admin' role exists with the correct guard
        $role = Role::where('name', 'Admin')->where('guard_name', 'api')->first(); // Adjust guard_name if needed

        // Assign the 'Admin' role to the user
        if ($role) {
            $user->assignRole($role); 
        } else {
            // Log the error or handle the missing role
            $this->command->error("Role 'Admin' with guard 'api' does not exist.");
        }
    }
}
