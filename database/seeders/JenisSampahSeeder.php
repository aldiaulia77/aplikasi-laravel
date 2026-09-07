<?php

namespace Database\Seeders;

use App\Models\JenisSampah;
use Illuminate\Database\Seeder;

class JenisSampahSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama' => 'Botol Plastik (PET)', 'kategori' => 'Plastik', 'satuan' => 'kg', 'harga' => 3000],
            ['nama' => 'Kardus', 'kategori' => 'Kertas', 'satuan' => 'kg', 'harga' => 2000],
            ['nama' => 'Kertas Campur', 'kategori' => 'Kertas', 'satuan' => 'kg', 'harga' => 1500],
            ['nama' => 'Besi/Logam', 'kategori' => 'Logam', 'satuan' => 'kg', 'harga' => 4000],
            ['nama' => 'Kaleng Aluminium', 'kategori' => 'Logam', 'satuan' => 'kg', 'harga' => 8000],
            ['nama' => 'Botol Kaca', 'kategori' => 'Kaca', 'satuan' => 'kg', 'harga' => 500],
        ];

        foreach ($items as $item) {
            JenisSampah::create($item + ['status' => 'aktif']);
        }
    }
}
