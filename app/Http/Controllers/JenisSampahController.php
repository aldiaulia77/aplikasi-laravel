<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\JenisSampah;
use Illuminate\Http\Request;

class JenisSampahController extends Controller
{
    public function index()
    {
        $jenis = JenisSampah::orderBy('nama')->paginate(12);

        return view('jenis.index', compact('jenis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'satuan' => ['required', 'string', 'max:10'],
            'harga' => ['required', 'numeric', 'min:0'],
        ]);
        $data['status'] = 'aktif';

        $jenis = JenisSampah::create($data);

        ActivityLog::record('jenis.create', "Jenis sampah baru: {$jenis->nama}");

        return back()->with('success', 'Jenis sampah ditambahkan.');
    }

    public function update(Request $request, JenisSampah $jenis)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'satuan' => ['required', 'string', 'max:10'],
            'harga' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $before = $jenis->harga;
        $jenis->update($data);

        if ($before != $jenis->harga) {
            ActivityLog::record('jenis.harga_update', "Harga {$jenis->nama}: {$before} -> {$jenis->harga}");
        }

        return back()->with('success', 'Jenis sampah diperbarui.');
    }
}
