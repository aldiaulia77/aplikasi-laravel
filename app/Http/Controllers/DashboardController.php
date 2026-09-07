<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isNasabah()) {
            $nasabah = $user->nasabah;
            $riwayat = $nasabah
                ? $nasabah->transaksi()->with('detail.jenisSampah')->latest('tanggal')->limit(10)->get()
                : collect();

            return view('dashboard.nasabah', [
                'nasabah' => $nasabah,
                'riwayat' => $riwayat,
            ]);
        }

        // Admin & operator: statistik ringan pakai aggregate query (COUNT/SUM),
        // BUKAN mengambil seluruh baris transaksi (lihat PERFORMANCE.md §8).
        //
        // PENTING: filter pakai whereBetween(tanggal awal, tanggal akhir),
        // BUKAN DATE_FORMAT(tanggal, '%Y-%m') — membungkus kolom dengan
        // fungsi membuat index pada kolom 'tanggal' tidak terpakai sama
        // sekali (full table scan tiap load dashboard). whereBetween tetap
        // bisa memakai index karena kolomnya dibandingkan langsung.
        $awalBulan = now()->startOfMonth()->toDateString();
        $akhirBulan = now()->endOfMonth()->toDateString();

        $agregatBulanIni = Transaksi::whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->selectRaw('COUNT(*) as trx, COALESCE(SUM(total_berat),0) as berat, COALESCE(SUM(total_nilai),0) as nilai')
            ->first();

        $stats = [
            'total_nasabah' => Nasabah::count(),
            'nasabah_aktif' => Nasabah::where('status', 'aktif')->count(),
            'total_jenis' => JenisSampah::count(),
            'berat_bulan_ini' => (float) $agregatBulanIni->berat,
            'nilai_bulan_ini' => (float) $agregatBulanIni->nilai,
            'trx_bulan_ini' => (int) $agregatBulanIni->trx,
        ];

        $recent = Transaksi::with(['nasabah:id,nama,kode_nasabah', 'detail.jenisSampah:id,nama'])
            ->latest('tanggal')
            ->latest('id')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact('stats', 'recent'));
    }
}
