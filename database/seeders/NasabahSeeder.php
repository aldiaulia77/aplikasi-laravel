<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Database\Seeder;

class NasabahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Suherman', 'telepon' => '0812xxxxxx1', 'alamat' => 'RT01/RW02 Penganjang'],
            ['nama' => 'Warsih', 'telepon' => '0812xxxxxx2', 'alamat' => 'RT02/RW01 Penganjang'],
            ['nama' => 'Kartono', 'telepon' => '0812xxxxxx3', 'alamat' => 'RT03/RW02 Penganjang'],
        ];

        foreach ($data as $row) {
            $nasabah = Nasabah::create($row + [
                'kode_nasabah' => Nasabah::generateKode(),
                'tanggal_daftar' => now()->toDateString(),
                'status' => 'aktif',
            ]);
            $nasabah->assignFinalKode();

            // Buatkan akun login untuk nasabah (opsional, role: nasabah).
            // Username & password default = kode nasabah, WAJIB diganti.
            User::create([
                'name' => $nasabah->nama,
                'username' => strtolower($nasabah->kode_nasabah),
                'password' => $nasabah->kode_nasabah,
                'role' => 'nasabah',
                'nasabah_id' => $nasabah->id,
                'is_active' => true,
            ]);
        }
    }
}
