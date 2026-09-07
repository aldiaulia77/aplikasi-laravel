<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Nasabah;
use Illuminate\Http\Request;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        // Selalu paginate — jangan pernah Nasabah::all() (lihat PERFORMANCE.md §6).
        $nasabah = Nasabah::withSaldo()
            ->when($request->q, fn ($q) => $q->where(function ($sub) use ($request) {
                $sub->where('nama', 'like', "%{$request->q}%")
                    ->orWhere('kode_nasabah', 'like', "%{$request->q}%")
                    ->orWhere('telepon', 'like', "%{$request->q}%");
            }))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('nasabah.index', compact('nasabah'));
    }

    public function create()
    {
        return view('nasabah.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        $data['kode_nasabah'] = Nasabah::generateKode();
        $data['tanggal_daftar'] = now()->toDateString();
        $data['status'] = 'aktif';

        $nasabah = Nasabah::create($data);
        $nasabah->assignFinalKode();

        ActivityLog::record('nasabah.create', "Nasabah baru: {$nasabah->kode_nasabah} - {$nasabah->nama}");

        return redirect()->route('nasabah.index')->with('success', 'Nasabah berhasil ditambahkan.');
    }

    public function edit(Nasabah $nasabah)
    {
        return view('nasabah.edit', compact('nasabah'));
    }

    public function update(Request $request, Nasabah $nasabah)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $nasabah->update($data);

        ActivityLog::record('nasabah.update', "Update nasabah {$nasabah->kode_nasabah}");

        return redirect()->route('nasabah.index')->with('success', 'Data nasabah diperbarui.');
    }
}
