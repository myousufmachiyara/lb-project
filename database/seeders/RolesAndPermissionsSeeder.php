<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create 'superadmin' role if not exists
        $superAdmin = Role::firstOrCreate(['name' => 'superadmin']);

        // Find user by email
        $user = User::where('email', 'admin@gmail.com')->first();

        if ($user) {
            $user->assignRole($superAdmin);
        }
    }
}
