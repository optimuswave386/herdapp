<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::firstOrCreate(['name' => 'delete-user']);
        
        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin-role']);
        $adminRole->givePermissionTo('delete-user');
        
        // Assign roles to existing users (example)
        // $user = User::find(1);
        // if ($user) {
        //     $user->assignRole('admin-role');
        // }

    }
}