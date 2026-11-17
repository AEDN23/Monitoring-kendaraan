<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name'              => 'Pengguna Utama',
                'password'          => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'demo@mail.com'],
            [
                'name'              => 'Akun Demo',
                'password'          => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
