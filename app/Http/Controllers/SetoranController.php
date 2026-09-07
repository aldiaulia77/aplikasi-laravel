<?php

namespace App\Http\Controllers;

use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Services\SetoranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetoranController extends Controller
{
    public function create(Request $request)
    {
        $jenisSampah = JenisSampah::where('status', 'aktif')->orderBy('nama')->get();

        $nasabahResult = collect();
        if ($request->q) {
            $nasabahResult = Nasabah::where('status', 'aktif')
                ->where(function ($q) use ($request) {
                    $q->where('nama', 'like', "%{$request->q}%")
                        ->orWhere('kode_nasabah', 'like', "%{$request->q}%");
                })
                ->limit(8)
                ->get();
        }

        return view('setoran.create', compact('jenisSampah', 'nasabahResult'));
    }

    public function store(Request $request, SetoranService $service)
    {
        $validated = $request->validate([
            'nasabah_id' => ['required', 'exists:nasabah,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.jenis_sampah_id' => ['required', 'exists:jenis_sampah,id'],
            'items.*.berat' => ['required', 'numeric', 'min:0.01'],
        ]);

        try {
            $transaksi = $service->catat(
                nasabahId: (int) $validated['nasabah_id'],
                operatorId: Auth::id(),
                items: $validated['items'],
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['items' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('setoran.create')
            ->with('success', "Setoran {$transaksi->trx_id} tersimpan · Rp".number_format((float) $transaksi->total_nilai, 0, ',', '.'));
    }
}
