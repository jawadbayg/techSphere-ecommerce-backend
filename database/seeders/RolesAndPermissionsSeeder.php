<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create Permissions
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'buy products']);

        // Create Roles and Assign Permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(['manage products', 'manage users']);

        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo(['view products', 'buy products']);
    }
}
