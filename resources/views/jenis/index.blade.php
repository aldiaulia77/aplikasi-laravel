@extends('layouts.app')
@section('title', 'Jenis Sampah')
@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <p class="sd-display text-lg" style="color:var(--forest-deep)">Jenis & Harga Sampah</p>
        <button onclick="document.getElementById('modal-new').classList.remove('hidden')" class="sd-btn-primary rounded-lg px-4 py-2 text-sm">+ Tambah Jenis</button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($jenis as $j)
            <div class="sd-card p-4 flex flex-col gap-2">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-medium text-sm">{{ $j->nama }}</p>
                        <span class="sd-chip text-[11px] px-2 py-0.5 rounded-full inline-block mt-1">{{ $j->kategori }}</span>
                    </div>
                    <button onclick="document.getElementById('modal-edit-{{ $j->id }}').classList.remove('hidden')" class="text-xs" style="color:var(--forest)">Edit</button>
                </div>
                <p class="sd-mono text-lg" style="color:var(--gold)">Rp{{ number_format($j->harga,0,',','.') }}<span class="text-xs text-gray-400">/{{ $j->satuan }}</span></p>
                <span class="sd-chip text-[11px] px-2 py-1 rounded-full self-start {{ $j->status!=='aktif'?'opacity-50':'' }}">{{ $j->status }}</span>
            </div>

            <div id="modal-edit-{{ $j->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(21,54,39,0.45)">
                <div class="sd-card w-full max-w-md p-6 shadow-xl">
                    <p class="sd-display text-base mb-4" style="color:var(--forest-deep)">Edit Jenis Sampah</p>
                    <form method="POST" action="{{ route('jenis.update', $j) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <input name="nama" value="{{ $j->nama }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Nama">
                        <input name="kategori" value="{{ $j->kategori }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Kategori">
                        <div class="grid grid-cols-2 gap-3">
                            <input name="satuan" value="{{ $j->satuan }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Satuan">
                            <input type="number" name="harga" value="{{ $j->harga }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Harga">
                        </div>
                        <select name="status" class="sd-input w-full rounded-lg px-3 py-2 text-sm">
                            <option value="aktif" @selected($j->status==='aktif')>Aktif</option>
                            <option value="nonaktif" @selected($j->status==='nonaktif')>Nonaktif</option>
                        </select>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modal-edit-{{ $j->id }}').classList.add('hidden')" class="sd-btn-ghost rounded-lg px-4 py-2 text-sm">Batal</button>
                            <button class="sd-btn-primary rounded-lg px-4 py-2 text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <x-pagination :paginator="$jenis" />

    <div id="modal-new" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(21,54,39,0.45)">
        <div class="sd-card w-full max-w-md p-6 shadow-xl">
            <p class="sd-display text-base mb-4" style="color:var(--forest-deep)">Tambah Jenis Sampah</p>
            <form method="POST" action="{{ route('jenis.store') }}" class="space-y-3">
                @csrf
                <input name="nama" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Nama jenis sampah">
                <input name="kategori" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Kategori">
                <div class="grid grid-cols-2 gap-3">
                    <input name="satuan" value="kg" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Satuan">
                    <input type="number" name="harga" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Harga/satuan">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-new').classList.add('hidden')" class="sd-btn-ghost rounded-lg px-4 py-2 text-sm">Batal</button>
                    <button class="sd-btn-primary rounded-lg px-4 py-2 text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
