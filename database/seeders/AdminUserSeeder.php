<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => 'Jawad Ahmad',
            'email' => 'jawad@admin.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('admin'); 
    }
}
