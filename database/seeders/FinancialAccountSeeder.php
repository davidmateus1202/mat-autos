<?php

namespace Database\Seeders;

use App\Models\FinancialAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinancialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if accounts already exist to avoid duplicates
        if (FinancialAccount::count() > 0) {
            $this->command->info('Financial accounts already seeded. Skipping...');
            return;
        }

        // Create sample financial accounts
        FinancialAccount::create([
            'name' => 'Caja General',
            'bank_name' => 'Efectivo',
            'account_number' => 'CASH-001',
            'type' => 'cash',
            'initial_balance' => 0,
        ]);

        FinancialAccount::create([
            'name' => 'Banco Bogotá - Corriente',
            'bank_name' => 'Banco de Bogotá',
            'account_number' => '123456789',
            'type' => 'checking',
            'initial_balance' => 5000000, // 5 millones
        ]);

        FinancialAccount::create([
            'name' => 'Banco Occidente - Ahorros',
            'bank_name' => 'Banco Occidente',
            'account_number' => '987654321',
            'type' => 'savings',
            'initial_balance' => 10000000, // 10 millones
        ]);

        FinancialAccount::create([
            'name' => 'Nequi - Billetera Digital',
            'bank_name' => 'Nequi',
            'account_number' => 'NEQUI-12345',
            'type' => 'digital',
            'initial_balance' => 2000000, // 2 millones
        ]);

        FinancialAccount::create([
            'name' => 'Caja de Emergencia',
            'bank_name' => 'Efectivo',
            'account_number' => 'CASH-002',
            'type' => 'cash',
            'initial_balance' => 1000000, // 1 millón
        ]);

        $this->command->info('Financial accounts seeded successfully!');
    }
}
