<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;
use App\Models\Register;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class CreateAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:create-admin {email? : The email of the user to assign admin role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin role and assign to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating admin role...');
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Create admin role if it doesn't exist
            $adminRole = Role::firstOrCreate(
                ['name' => 'admin'],
                ['description' => 'Administrator with full access to all features']
            );
            
            $this->info('Admin role created: ' . $adminRole->name);
            
            // Create basic permissions
            $permissions = [
                ['name' => 'manage-users', 'description' => 'Can manage users'],
                ['name' => 'manage-roles', 'description' => 'Can manage roles'],
                ['name' => 'manage-permissions', 'description' => 'Can manage permissions'],
                ['name' => 'view-dashboard', 'description' => 'Can view dashboard'],
                ['name' => 'manage-companies', 'description' => 'Can manage companies'],
                ['name' => 'manage-notes', 'description' => 'Can manage notes'],
                ['name' => 'manage-reminders', 'description' => 'Can manage reminders'],
            ];
            
            $createdPermissions = [];
            foreach ($permissions as $permission) {
                $createdPermissions[] = Permission::firstOrCreate(
                    ['name' => $permission['name']],
                    ['description' => $permission['description']]
                );
            }
            
            // Assign all permissions to admin role
            $adminRole->permissions()->sync(collect($createdPermissions)->pluck('id'));
            
            $this->info('Permissions assigned to admin role');
            
            // Assign admin role to user if email provided
            $email = $this->argument('email');
            
            if ($email) {
                // Try to find user in both User and Register models
                $user = User::where('email', $email)->first();
                $register = Register::where('email', $email)->first();
                
                if ($user) {
                    $user->roles()->syncWithoutDetaching([$adminRole->id]);
                    $this->info("Admin role assigned to User: {$user->name} ({$email})");
                }
                
                if ($register) {
                    $register->roles()->syncWithoutDetaching([$adminRole->id]);
                    $this->info("Admin role assigned to Register: {$register->name} ({$email})");
                }
                
                if (!$user && !$register) {
                    $this->error("No user found with email: {$email}");
                    DB::rollBack();
                    return 1;
                }
            } else {
                $this->warn('No email provided. Admin role created but not assigned to any user.');
                $this->info('To assign admin role to a user, run:');
                $this->line('php artisan role:create-admin user@example.com');
            }
            
            // Commit transaction
            DB::commit();
            
            $this->info('Admin role setup completed successfully!');
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to create admin role: ' . $e->getMessage());
            return 1;
        }
    }
} 