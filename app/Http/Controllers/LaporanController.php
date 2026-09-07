<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    protected function range(Request $request): array
    {
        $from = $request->from ?: now()->startOfMonth()->toDateString();
        $to = $request->to ?: now()->toDateString();

        return [$from, $to];
    }

    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        // Setoran & penarikan dihitung TERPISAH (bukan dijumlah jadi satu
        // angka) karena keduanya punya arti berlawanan pada saldo —
        // menjumlahkannya langsung akan menyesatkan (mis. Rp1jt setoran +
        // Rp1jt penarikan bukan berarti "Rp2jt aktivitas netral").
        $totalSetoran = Transaksi::where('tipe', 'setoran')
            ->whereBetween('tanggal', [$from, $to])
            ->selectRaw('COUNT(*) as trx, COALESCE(SUM(total_berat),0) as berat, COALESCE(SUM(total_nilai),0) as nilai')
            ->first();

        $totalPenarikan = Transaksi::where('tipe', 'penarikan')
            ->whereBetween('tanggal', [$from, $to])
            ->selectRaw('COUNT(*) as trx, COALESCE(SUM(total_nilai),0) as nilai')
            ->first();

        $totals = (object) [
            'total_trx' => $totalSetoran->trx + $totalPenarikan->trx,
            'total_berat' => $totalSetoran->berat,
            'total_setoran' => $totalSetoran->nilai,
            'total_penarikan' => $totalPenarikan->nilai,
            'net' => $totalSetoran->nilai - $totalPenarikan->nilai,
        ];

        // Rekap per jenis sampah — hanya relevan untuk setoran (penarikan
        // tidak punya rincian jenis sampah), aggregate query (GROUP BY).
        $perJenis = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_detail.transaksi_id')
            ->join('jenis_sampah', 'jenis_sampah.id', '=', 'transaksi_detail.jenis_sampah_id')
            ->where('transaksi.tipe', 'setoran')
            ->whereBetween('transaksi.tanggal', [$from, $to])
            ->groupBy('jenis_sampah.nama')
            ->selectRaw('jenis_sampah.nama as nama, SUM(transaksi_detail.berat) as berat, SUM(transaksi_detail.subtotal) as nilai')
            ->orderByDesc('nilai')
            ->get();

        return view('laporan.index', compact('totals', 'perJenis', 'from', 'to'));
    }

    /**
     * Export CSV via streamed response supaya tidak memuat seluruh data
     * ke memory sekaligus (lihat PERFORMANCE.md §23 — export besar).
     * Untuk volume sangat besar, arahkan proses ini ke Queue Job terpisah.
     */
    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);

        $filename = "laporan-sampahdesa-{$from}_{$to}.csv";

        $callback = function () use ($from, $to) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Trx ID', 'Tipe', 'Tanggal', 'Nasabah', 'Jenis Sampah', 'Berat (kg)', 'Nilai (Rp)']);

            Transaksi::with(['nasabah:id,nama', 'detail.jenisSampah:id,nama'])
                ->whereBetween('tanggal', [$from, $to])
                ->orderBy('tanggal')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $trx) {
                        if ($trx->tipe === 'penarikan') {
                            fputcsv($handle, [
                                $trx->trx_id,
                                'Penarikan',
                                $trx->tanggal->format('Y-m-d'),
                                $trx->nasabah->nama ?? '-',
                                $trx->catatan ?: 'Pencairan saldo',
                                0,
                                -1 * $trx->total_nilai,
                            ]);
                            continue;
                        }

                        foreach ($trx->detail as $d) {
                            fputcsv($handle, [
                                $trx->trx_id,
                                'Setoran',
                                $trx->tanggal->format('Y-m-d'),
                                $trx->nasabah->nama ?? '-',
                                $d->jenisSampah->nama ?? '-',
                                $d->berat,
                                $d->subtotal,
                            ]);
                        }
                    }
                });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
