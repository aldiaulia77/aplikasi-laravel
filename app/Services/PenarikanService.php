<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Nasabah;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

/**
 * Mengenkapsulasi logika pencairan/penarikan saldo nasabah. Sama seperti
 * SetoranService, seluruh proses dibungkus DB::transaction() +
 * lockForUpdate() pada baris nasabah — ini KRUSIAL di sini karena kita
 * harus memastikan saldo tidak pernah menjadi minus, bahkan jika dua
 * request penarikan untuk nasabah yang sama masuk hampir bersamaan.
 */
class PenarikanService
{
    public function tarik(int $nasabahId, int $operatorId, float $jumlah, ?string $catatan = null): Transaksi
    {
        if ($jumlah <= 0) {
            throw new \InvalidArgumentException('Jumlah penarikan harus lebih dari 0.');
        }

        return DB::transaction(function () use ($nasabahId, $operatorId, $jumlah, $catatan) {
            // lockForUpdate mengunci baris nasabah ini sampai transaksi
            // selesai — request penarikan/setoran LAIN untuk nasabah yang
            // sama akan menunggu (bukan membaca saldo yang sudah usang),
            // sehingga saldo tidak mungkin jadi minus akibat race condition.
            $nasabah = Nasabah::where('status', 'aktif')
                ->lockForUpdate()
                ->find($nasabahId);

            if (! $nasabah) {
                throw new \InvalidArgumentException('Nasabah tidak ditemukan atau sudah tidak aktif.');
            }

            // Hitung saldo terkini SECARA LANGSUNG di dalam transaksi yang
            // sudah mengunci baris nasabah (bukan pakai accessor/cache di
            // luar), supaya nilainya benar-benar terkini saat dicek.
            $totalSetoran = (float) Transaksi::where('nasabah_id', $nasabah->id)
                ->where('tipe', 'setoran')
                ->sum('total_nilai');
            $totalPenarikan = (float) Transaksi::where('nasabah_id', $nasabah->id)
                ->where('tipe', 'penarikan')
                ->sum('total_nilai');
            $saldoSaatIni = $totalSetoran - $totalPenarikan;

            if ($jumlah > $saldoSaatIni) {
                throw new \InvalidArgumentException(
                    'Saldo tidak mencukupi. Saldo saat ini: Rp'.number_format($saldoSaatIni, 0, ',', '.')
                );
            }

            $transaksi = Transaksi::create([
                'trx_id' => Transaksi::generateTrxId(), // placeholder unik sementara
                'tipe' => 'penarikan',
                'nasabah_id' => $nasabah->id,
                'operator_id' => $operatorId,
                'tanggal' => now()->toDateString(),
                'total_berat' => 0,
                'total_nilai' => $jumlah,
                'catatan' => $catatan,
            ]);

            // ID final berbasis auto-increment — konsisten dengan pola di
            // SetoranService, menghindari race condition nomor transaksi.
            $transaksi->assignFinalTrxId();

            ActivityLog::record(
                'penarikan.create',
                "Penarikan {$transaksi->trx_id} untuk nasabah #{$nasabahId} senilai Rp".number_format($jumlah, 0, ',', '.')
            );

            return $transaksi->load('nasabah');
        });
    }
}
