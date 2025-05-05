<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add test companies (only if they don't exist)
        if (Company::count() == 0) {
            Company::create([
                'name' => 'Test Company 1',
                'contact' => '123-456-7890',
                'address' => '123 Test Street',
                'url' => 'https://example.com'
            ]);
            
            Company::create([
                'name' => 'Test Company 2',
                'contact' => '987-654-3210',
                'address' => '456 Example Avenue',
                'url' => 'https://test.com'
            ]);
            
            $this->command->info('Created 2 test companies');
        } else {
            $this->command->info('Companies already exist, skipping seed');
        }
    }
}
