<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role if it doesn't exist
        Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with full access to all features']
        );

        // Create user role if it doesn't exist
        Role::firstOrCreate(
            ['name' => 'user'],
            ['description' => 'Regular user with limited access']
        );

        // Add basic permissions for user role if needed
        $userRole = Role::where('name', 'user')->first();
        
        // Get or create basic permissions
        $viewDashboardPermission = Permission::firstOrCreate(
            ['name' => 'view-dashboard'],
            ['description' => 'Can view dashboard']
        );
        
        $manageOwnProfilePermission = Permission::firstOrCreate(
            ['name' => 'manage-own-profile'],
            ['description' => 'Can manage own profile']
        );
        
        $manageRemindersPermission = Permission::firstOrCreate(
            ['name' => 'manage-reminders'],
            ['description' => 'Can manage reminders']
        );
        
        // Assign permissions to user role
        $userRole->permissions()->syncWithoutDetaching([
            $viewDashboardPermission->id,
            $manageOwnProfilePermission->id,
            $manageRemindersPermission->id
        ]);
    }
} 