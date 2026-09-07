<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Bank Sampah',
            'username' => 'admin',
            'email' => 'admin@sampahdesa.test',
            'password' => 'admin123', // auto-hashed via cast, GANTI setelah instalasi
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Operator Desa',
            'username' => 'operator',
            'email' => 'operator@sampahdesa.test',
            'password' => 'operator123', // GANTI setelah instalasi
            'role' => 'operator',
            'is_active' => true,
        ]);
    }
}
