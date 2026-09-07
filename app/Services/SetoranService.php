<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\JenisSampah;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\DB;

/**
 * Mengenkapsulasi logika pencatatan setoran agar konsisten (lihat
 * PERFORMANCE.md & SECURITY.md — financial data consistency).
 * Seluruh perubahan (transaksi + detail) dibungkus DB::transaction()
 * supaya dua request bersamaan tidak menyebabkan data setengah tersimpan.
 */
class SetoranService
{
    /**
     * @param  array<int, array{jenis_sampah_id: int, berat: float}>  $items
     */
    public function catat(int $nasabahId, int $operatorId, array $items, ?string $catatan = null): Transaksi
    {
        return DB::transaction(function () use ($nasabahId, $operatorId, $items, $catatan) {
            // Lock baris nasabah & pastikan statusnya aktif dan belum
            // dihapus (soft delete). Query Eloquent otomatis mengecualikan
            // baris yang sudah di-soft-delete, tapi status 'nonaktif' harus
            // dicek eksplisit — validasi 'exists' di controller tidak cukup
            // karena tidak memeriksa status maupun soft-delete.
            $nasabah = \App\Models\Nasabah::where('status', 'aktif')
                ->lockForUpdate()
                ->find($nasabahId);

            if (! $nasabah) {
                throw new \InvalidArgumentException('Nasabah tidak ditemukan atau sudah tidak aktif.');
            }

            $jenisIds = collect($items)->pluck('jenis_sampah_id');

            // Lock baris jenis sampah yang relevan agar harga tidak berubah
            // di tengah transaksi (mencegah race condition harga).
            $jenisMap = JenisSampah::whereIn('id', $jenisIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalBerat = 0;
            $totalNilai = 0;
            $detailRows = [];

            foreach ($items as $item) {
                $jenis = $jenisMap->get($item['jenis_sampah_id']);
                if (! $jenis || $jenis->status !== 'aktif') {
                    continue;
                }

                $berat = (float) $item['berat'];
                if ($berat <= 0) {
                    continue;
                }

                $subtotal = $berat * (float) $jenis->harga;
                $totalBerat += $berat;
                $totalNilai += $subtotal;

                $detailRows[] = [
                    'jenis_sampah_id' => $jenis->id,
                    'berat' => $berat,
                    'harga_satuan' => $jenis->harga,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($detailRows)) {
                throw new \InvalidArgumentException('Tidak ada item setoran yang valid.');
            }

            $transaksi = Transaksi::create([
                'trx_id' => Transaksi::generateTrxId(), // placeholder unik sementara
                'tipe' => 'setoran',
                'nasabah_id' => $nasabah->id,
                'operator_id' => $operatorId,
                'tanggal' => now()->toDateString(),
                'total_berat' => $totalBerat,
                'total_nilai' => $totalNilai,
            ]);

            // Timpa dengan ID final berbasis auto-increment (dijamin unik
            // oleh database, tidak bergantung pada COUNT(*) yang rentan
            // race condition saat dua operator input bersamaan).
            $transaksi->assignFinalTrxId();

            foreach ($detailRows as $row) {
                $transaksi->detail()->create($row);
            }

            ActivityLog::record(
                'setoran.create',
                "Setoran {$transaksi->trx_id} untuk nasabah #{$nasabahId} senilai Rp".number_format($totalNilai, 0, ',', '.')
            );

            return $transaksi->load('detail.jenisSampah', 'nasabah');
        });
    }
}
