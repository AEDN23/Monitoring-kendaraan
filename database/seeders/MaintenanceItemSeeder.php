<?php

namespace Database\Seeders;

use App\Models\MaintenanceItem;
use Illuminate\Database\Seeder;

class MaintenanceItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Mesin
            [
                'name'                  => 'Oli Mesin',
                'category'              => 'Mesin',
                'default_interval_km'   => 3000,
                'default_interval_days' => 90,
                'tracking_mode'         => 'km_or_time',
                'applies_to'            => 'both',
                'description'           => 'Penggantian oli mesin',
            ],
            [
                'name'                  => 'Filter Oli',
                'category'              => 'Mesin',
                'default_interval_km'   => 6000,
                'default_interval_days' => 180,
                'tracking_mode'         => 'km_or_time',
                'applies_to'            => 'car',
                'description'           => 'Penggantian filter oli mesin',
            ],
            [
                'name'                  => 'Oli Gardan',
                'category'              => 'Mesin',
                'default_interval_km'   => 10000,
                'default_interval_days' => 365,
                'tracking_mode'         => 'km_or_time',
                'applies_to'            => 'both',
                'description'           => 'Penggantian oli gardan / transmisi',
            ],
            [
                'name'                  => 'Busi',
                'category'              => 'Mesin',
                'default_interval_km'   => 8000,
                'default_interval_days' => null,
                'tracking_mode'         => 'km',
                'applies_to'            => 'both',
                'description'           => 'Penggantian busi',
            ],
            [
                'name'                  => 'Filter Udara',
                'category'              => 'Mesin',
                'default_interval_km'   => 10000,
                'default_interval_days' => 365,
                'tracking_mode'         => 'km_or_time',
                'applies_to'            => 'both',
                'description'           => 'Pembersihan / penggantian filter udara',
            ],
            [
                'name'                  => 'Filter Bensin',
                'category'              => 'Mesin',
                'default_interval_km'   => 20000,
                'default_interval_days' => null,
                'tracking_mode'         => 'km',
                'applies_to'            => 'both',
                'description'           => 'Penggantian filter bensin',
            ],

            // Rem
            [
                'name'                  => 'Kampas Rem Depan',
                'category'              => 'Rem',
                'default_interval_km'   => null,
                'default_interval_days' => null,
                'tracking_mode'         => 'condition',
                'applies_to'            => 'both',
                'description'           => 'Pengecekan / penggantian kampas rem depan berdasarkan kondisi',
            ],
            [
                'name'                  => 'Kampas Rem Belakang',
                'category'              => 'Rem',
                'default_interval_km'   => null,
                'default_interval_days' => null,
                'tracking_mode'         => 'condition',
                'applies_to'            => 'both',
                'description'           => 'Pengecekan / penggantian kampas rem belakang berdasarkan kondisi',
            ],
            [
                'name'                  => 'Minyak Rem',
                'category'              => 'Rem',
                'default_interval_km'   => null,
                'default_interval_days' => 365,
                'tracking_mode'         => 'time',
                'applies_to'            => 'both',
                'description'           => 'Penggantian minyak rem',
            ],

            // Ban
            [
                'name'                  => 'Ban Depan',
                'category'              => 'Ban',
                'default_interval_km'   => null,
                'default_interval_days' => null,
                'tracking_mode'         => 'condition',
                'applies_to'            => 'both',
                'description'           => 'Pengecekan / penggantian ban depan berdasarkan kondisi',
            ],
            [
                'name'                  => 'Ban Belakang',
                'category'              => 'Ban',
                'default_interval_km'   => null,
                'default_interval_days' => null,
                'tracking_mode'         => 'condition',
                'applies_to'            => 'both',
                'description'           => 'Pengecekan / penggantian ban belakang berdasarkan kondisi',
            ],

            // Transmisi & Drivetrain
            [
                'name'                  => 'Rantai / Kampas Kopling',
                'category'              => 'Transmisi',
                'default_interval_km'   => 5000,
                'default_interval_days' => null,
                'tracking_mode'         => 'km',
                'applies_to'            => 'motorcycle',
                'description'           => 'Pelumasan / pemeriksaan rantai atau kampas kopling motor',
            ],
            [
                'name'                  => 'Oli Transmisi CVT',
                'category'              => 'Transmisi',
                'default_interval_km'   => 8000,
                'default_interval_days' => 365,
                'tracking_mode'         => 'km_or_time',
                'applies_to'            => 'motorcycle',
                'description'           => 'Penggantian oli transmisi motor matic',
            ],

            // Kelistrikan & Lainnya
            [
                'name'                  => 'Aki / Baterai',
                'category'              => 'Kelistrikan',
                'default_interval_km'   => null,
                'default_interval_days' => 730,
                'tracking_mode'         => 'time',
                'applies_to'            => 'both',
                'description'           => 'Pengecekan / penggantian aki',
            ],
            [
                'name'                  => 'Servis Rutin',
                'category'              => 'Umum',
                'default_interval_km'   => 5000,
                'default_interval_days' => 180,
                'tracking_mode'         => 'km_or_time',
                'applies_to'            => 'both',
                'description'           => 'Servis berkala / tune-up di bengkel',
            ],
        ];

        foreach ($items as $data) {
            MaintenanceItem::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
