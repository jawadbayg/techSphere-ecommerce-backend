<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find the admin user by email or ID
        $admin = User::where('email', 'jawad@admin.com')->first();
        
        if ($admin) {
            // Update the role to 'admin'
            $admin->role = 'admin';
            $admin->save();
        }
    }
}
