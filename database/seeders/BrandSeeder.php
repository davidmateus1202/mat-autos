<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Toyota', 'Honda', 'Chevrolet', 'Ford', 'Nissan', 'Renault', 'Mazda', 'Hyundai', 'Kia', 'Volkswagen',
            'BMW', 'Mercedes-Benz', 'Audi', 'Peugeot', 'Citroën', 'Subaru', 'Suzuki', 'Mitsubishi', 'Volvo', 'Land Rover'
        ];

        foreach ($brands as $name) {
            DB::table('brands')->updateOrInsert(['name' => $name], [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
