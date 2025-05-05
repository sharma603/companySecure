<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;

class AssignUsersToCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-to-companies {user_id? : The ID of the user to assign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign users to companies for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        // Get companies
        $companies = Company::all();
        
        if ($companies->isEmpty()) {
            $this->error('No companies found. Please create some companies first.');
            return 1;
        }
        
        // If user ID is provided, assign only that user
        if ($userId) {
            $user = User::find($userId);
            
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
            
            $this->assignUserToCompanies($user, $companies);
            return 0;
        }
        
        // Otherwise assign all users
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->error('No users found. Please create some users first.');
            return 1;
        }
        
        $this->info('Assigning users to companies...');
        
        foreach ($users as $user) {
            $this->assignUserToCompanies($user, $companies);
        }
        
        $this->info('All users have been assigned to companies successfully!');
        
        return 0;
    }
    
    /**
     * Assign a user to companies
     */
    private function assignUserToCompanies(User $user, $companies)
    {
        // Check if the user already has company assignments to avoid duplicates
        $existingCompanyIds = $user->companies()->pluck('companies.id')->toArray();
        
        // Only assign companies that aren't already assigned
        $companiesToAssign = $companies->filter(function($company) use ($existingCompanyIds) {
            return !in_array($company->id, $existingCompanyIds);
        });
        
        if ($companiesToAssign->isEmpty()) {
            $this->info("User {$user->name} already assigned to all companies. Skipping.");
            return;
        }
        
        // Attach the filtered companies to the user
        $user->companies()->attach($companiesToAssign->pluck('id')->toArray());
        
        $this->info("User {$user->name} assigned to " . $companiesToAssign->count() . " companies");
    }
}
