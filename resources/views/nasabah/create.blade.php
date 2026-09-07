@extends('layouts.app')
@section('title', 'Tambah Nasabah')
@section('content')
<div class="max-w-md sd-card p-6">
    <p class="sd-display text-lg mb-4" style="color:var(--forest-deep)">Tambah Nasabah</p>
    <form method="POST" action="{{ route('nasabah.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-xs font-medium mb-1 block">Nama Lengkap</label>
            <input name="nama" value="{{ old('nama') }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="text-xs font-medium mb-1 block">Nomor Telepon</label>
            <input name="telepon" value="{{ old('telepon') }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs font-medium mb-1 block">Alamat</label>
            <textarea name="alamat" rows="2" class="sd-input w-full rounded-lg px-3 py-2 text-sm">{{ old('alamat') }}</textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('nasabah.index') }}" class="sd-btn-ghost rounded-lg px-4 py-2 text-sm">Batal</a>
            <button class="sd-btn-primary rounded-lg px-4 py-2 text-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection
