<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Create a default financial account if none exists
        if (\App\Models\FinancialAccount::count() === 0) {
            \App\Models\FinancialAccount::create([
                'name' => 'Caja General',
                'bank_name' => 'Efectivo',
                'initial_balance' => 0,
            ]);
        }

        // Seed financial accounts
        $this->call(FinancialAccountSeeder::class);

        // Seed brands and sample cars
        $this->call(BrandSeeder::class);
        $this->call(CarSeeder::class);
    }
}
