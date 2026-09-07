<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Services\PenarikanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenarikanController extends Controller
{
    public function create(Request $request)
    {
        $nasabahResult = collect();
        if ($request->q) {
            $nasabahResult = Nasabah::withSaldo()
                ->where('status', 'aktif')
                ->where(function ($q) use ($request) {
                    $q->where('nama', 'like', "%{$request->q}%")
                        ->orWhere('kode_nasabah', 'like', "%{$request->q}%");
                })
                ->limit(8)
                ->get();
        }

        return view('penarikan.create', compact('nasabahResult'));
    }

    public function store(Request $request, PenarikanService $service)
    {
        $validated = $request->validate([
            'nasabah_id' => ['required', 'exists:nasabah,id'],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $transaksi = $service->tarik(
                nasabahId: (int) $validated['nasabah_id'],
                operatorId: Auth::id(),
                jumlah: (float) $validated['jumlah'],
                catatan: $validated['catatan'] ?? null,
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['jumlah' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('penarikan.create')
            ->with('success', "Penarikan {$transaksi->trx_id} tersimpan · Rp".number_format((float) $transaksi->total_nilai, 0, ',', '.'));
    }
}
