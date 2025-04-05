<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\HeadOfAccounts;
use App\Models\SubHeadOfAccounts;
use App\Models\ChartOfAccounts;
use App\Models\ProjectStatus;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $now = now(); // Get the current timestamp

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        HeadOfAccounts::insert([
            ['id' => 1, 'name' => 'Assets'],
            ['id' => 2, 'name' => 'Liabilities'],
            ['id' => 3, 'name' => 'Expenses'],
            ['id' => 4, 'name' => 'Revenue'],
            ['id' => 5, 'name' => 'Equity'],
        ]);
        
        SubHeadOfAccounts::insert([
            ['id' => 1, 'hoa_id' => 1 , 'name' => "Current Assets"],
            ['id' => 2, 'hoa_id' => 1 , 'name' => "Inventory"],
            ['id' => 3, 'hoa_id' => 2 , 'name' => "Current Liabilities"],
            ['id' => 4, 'hoa_id' => 2 , 'name' => "Long-Term Liabilities"],
            ['id' => 5, 'hoa_id' => 4 , 'name' => "Sales"],
            ['id' => 6, 'hoa_id' => 3 , 'name' => "Expenses"],
            ['id' => 7, 'hoa_id' => 5 , 'name' => "Equity"],
        ]);
        
        ChartOfAccounts::insert([
            ['id' => 1, 'shoa_id' => 1 , 'name' => "Cash", 'receivables' => "0", 'payables' => "0", 'opening_date' => "2025-01-01", 'remarks' => "Asset", 'address' => "", 'phone_no' => "",'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'shoa_id' => 1 , 'name' => "Bank", 'receivables' => "0", 'payables' => "0", 'opening_date' => "2025-01-01", 'remarks' => "Asset", 'address' => "", 'phone_no' => "",'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'shoa_id' => 1 , 'name' => "Accounts Receivable", 'receivables' => "0", 'payables' => "0", 'opening_date' => "2025-01-01", 'remarks' => "Customer Accounts", 'address' => "", 'phone_no' => "",'created_at' => $now, 'updated_at' => $now], 
            ['id' => 4, 'shoa_id' => 2 , 'name' => "Raw Material Inventory", 'receivables' => "0", 'payables' => "0", 'opening_date' => "2025-01-01", 'remarks' => "Inventory", 'address' => "", 'phone_no' => "",'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'shoa_id' => 2 , 'name' => "Finished Goods Inventory", 'receivables' => "0", 'payables' => "0", 'opening_date' => "2025-01-01", 'remarks' => "Inventory", 'address' => "", 'phone_no' => "",'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'shoa_id' => 3 , 'name' => "Accounts Payable", 'receivables' => "0", 'payables' => "0", 'opening_date' => "2025-01-01", 'remarks' => "Supplier Accounts", 'address' => "", 'phone_no' => "",'created_at' => $now, 'updated_at' => $now], 
            ['id' => 7, 'shoa_id' => 5 , 'name' => "Sale Account", 'receivables' => "0", 'payables' => "0", 'opening_date' => "2025-01-01", 'remarks' => "Revenue", 'address' => "", 'phone_no' => "",'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'shoa_id' => 6 , 'name' => "Expense Account", 'receivables' => "0", 'payables' => "0", 'opening_date' => "2025-01-01", 'remarks' => "Expense", 'address' => "", 'phone_no' => "",'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'shoa_id' => 7 , 'name' => "Owner's Equity", 'receivables' => "0", 'payables' => "0", 'opening_date' => "2025-01-01", 'remarks' => "Equity", 'address' => "", 'phone_no' => "",'created_at' => $now, 'updated_at' => $now], 
        ]);

        ProjectStatus::insert([
            ['id' => 1, 'name' => 'Assigned', 'color' => '#ff3838'],
            ['id' => 2, 'name' => 'In Progress', 'color' => '#fcff4d'],
            ['id' => 3, 'name' => 'Completed', 'color' => '#a9f915'],
        ]);
    }
}
