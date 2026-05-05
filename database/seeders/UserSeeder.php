<?php

namespace Database\Seeders;

use App\Models\User; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'      => 'Dede Hidayatullah',
            'email'     => 'admin@mdh-digital.com',
            'role'      => 'admin',
            'phone'     => '6281290645584',
            'email_verified_at'     => now(),
            'password'  => Hash::make('11223344'),
        ]);

        User::create([
            'name'      => 'Ahmad Rizki',
            'email'     => 'rizki@mdh-digital.com',
            'role'      => 'user',
            'phone'     => '6281234567890',
            'email_verified_at'     => now(),
            'password'  => Hash::make('11223344'),
        ]);

        User::create([
            'name'      => 'Siti Nurhaliza',
            'email'     => 'siti@mdh-digital.com',
            'role'      => 'user',
            'phone'     => '6281345678901',
            'email_verified_at'     => now(),
            'password'  => Hash::make('11223344'),
        ]);

        User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'budi@mdh-digital.com',
            'role'      => 'user',
            'phone'     => '6281456789012',
            'email_verified_at'     => now(),
            'password'  => Hash::make('11223344'),
        ]);

        User::create([
            'name'      => 'Rina Wijaya',
            'email'     => 'rina@mdh-digital.com',
            'role'      => 'user',
            'phone'     => '6281567890123',
            'email_verified_at'     => now(),
            'password'  => Hash::make('11223344'),
        ]);
    }
}
