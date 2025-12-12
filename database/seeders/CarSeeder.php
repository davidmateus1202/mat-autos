<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure some brands exist
        $brands = [
            'Toyota', 'Honda', 'Chevrolet', 'Ford', 'Nissan', 'Mazda'
        ];

        $brandIds = [];
        foreach ($brands as $name) {
            $brandIds[$name] = DB::table('brands')->updateOrInsert(
                ['name' => $name],
                ['created_at' => now(), 'updated_at' => now()]
            ) ?: DB::table('brands')->where('name', $name)->value('id');
        }

        // Fetch or create a default financial account for purchases
        $accountId = DB::table('financial_accounts')->value('id');
        if (!$accountId) {
            $accountId = DB::table('financial_accounts')->insertGetId([
                'name' => 'Caja Principal',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sample cars
        $cars = [
            ['brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2018, 'vin' => 'JTDBR32E180001', 'purchase_price' => 38000000, 'purchase_date' => now()->subMonths(6)],
            ['brand' => 'Honda', 'model' => 'Civic', 'year' => 2020, 'vin' => '2HGFC2F69LH0002', 'purchase_price' => 45000000, 'purchase_date' => now()->subMonths(4)],
            ['brand' => 'Chevrolet', 'model' => 'Onix', 'year' => 2019, 'vin' => '9BGKS48U09B0003', 'purchase_price' => 32000000, 'purchase_date' => now()->subMonths(5)],
            ['brand' => 'Ford', 'model' => 'Fiesta', 'year' => 2017, 'vin' => '3FADP4EJ7HM0004', 'purchase_price' => 28000000, 'purchase_date' => now()->subMonths(8)],
            ['brand' => 'Nissan', 'model' => 'Sentra', 'year' => 2021, 'vin' => '3N1AB8BV4MY0005', 'purchase_price' => 52000000, 'purchase_date' => now()->subMonths(2)],
            ['brand' => 'Mazda', 'model' => '3', 'year' => 2019, 'vin' => 'JM1BN1V78K00006', 'purchase_price' => 47000000, 'purchase_date' => now()->subMonths(3)],
        ];

        foreach ($cars as $c) {
            $brandId = DB::table('brands')->where('name', $c['brand'])->value('id');
            $carId = DB::table('cars')->insertGetId([
                'brand_id' => $brandId,
                'model' => $c['model'],
                'year' => $c['year'],
                'vin' => $c['vin'],
                'purchase_price' => $c['purchase_price'],
                'purchase_date' => $c['purchase_date'],
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
