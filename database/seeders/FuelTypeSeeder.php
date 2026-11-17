<?php

namespace Database\Seeders;

use App\Models\FuelType;
use Illuminate\Database\Seeder;

class FuelTypeSeeder extends Seeder
{
    public function run(): void
    {
        $fuelTypes = [
            ['name' => 'Pertalite',   'is_active' => true],
            ['name' => 'Pertamax',    'is_active' => true],
            ['name' => 'Pertamax Plus', 'is_active' => true],
            ['name' => 'Pertamax Turbo', 'is_active' => true],
            ['name' => 'Solar',       'is_active' => true],
            ['name' => 'Dexlite',     'is_active' => true],
            ['name' => 'Pertamina Dex', 'is_active' => true],
        ];

        foreach ($fuelTypes as $data) {
            FuelType::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
