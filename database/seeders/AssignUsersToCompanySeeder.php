<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class AssignUsersToCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        // Get all companies
        $companies = Company::all();
        
        if ($users->isEmpty() || $companies->isEmpty()) {
            $this->command->info('No users or companies found. Please run the user and company seeders first.');
            return;
        }
        
        $this->command->info('Assigning users to companies...');
        
        // Assign each user to all companies for testing purposes
        foreach ($users as $user) {
            // Check if the user already has company assignments to avoid duplicates
            $existingCompanyIds = $user->companies()->pluck('companies.id')->toArray();
            
            // Only assign companies that aren't already assigned
            $companiesToAssign = $companies->filter(function($company) use ($existingCompanyIds) {
                return !in_array($company->id, $existingCompanyIds);
            });
            
            if ($companiesToAssign->isEmpty()) {
                $this->command->info("User {$user->name} already assigned to all companies. Skipping.");
                continue;
            }
            
            // Attach the filtered companies to the user
            $user->companies()->attach($companiesToAssign->pluck('id')->toArray());
            
            $this->command->info("User {$user->name} assigned to " . $companiesToAssign->count() . " companies");
        }
        
        $this->command->info('Users have been assigned to companies successfully!');
    }
}
