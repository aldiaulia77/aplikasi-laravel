@extends('layouts.app')
@section('title', 'Penarikan Saldo')
@section('content')
<div class="max-w-lg space-y-5">
    <p class="sd-display text-lg" style="color:var(--forest-deep)">Penarikan / Pencairan Saldo</p>

    <div class="sd-card p-5">
        <p class="text-xs font-medium text-gray-500 mb-2">1. Cari & Pilih Nasabah</p>
        <form method="GET" class="relative">
            <input name="q" value="{{ request('q') }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Ketik nama atau kode nasabah, lalu Enter…">
        </form>
        @if(request('q'))
            <div class="mt-2 space-y-1">
                @forelse($nasabahResult as $n)
                    <button form="penarikan-form" type="submit" name="nasabah_id" value="{{ $n->id }}"
                        class="w-full text-left px-3 py-2.5 text-sm hover:opacity-80 flex justify-between items-center sd-chip rounded-lg"
                        onclick="document.getElementById('selected-nasabah').value='{{ $n->id }}'; document.getElementById('selected-nasabah-label').innerText='{{ $n->nama }} ({{ $n->kode_nasabah }})'; document.getElementById('selected-saldo').innerText='Rp{{ number_format($n->saldo,0,',','.') }}'; document.getElementById('nasabah-box').classList.remove('hidden'); return false;">
                        <span>{{ $n->nama }}</span>
                        <span class="sd-mono text-xs text-gray-500">{{ $n->kode_nasabah }} · Rp{{ number_format($n->saldo,0,',','.') }}</span>
                    </button>
                @empty
                    <p class="text-sm text-gray-400 py-3">Tidak ditemukan atau nasabah tidak aktif.</p>
                @endforelse
            </div>
        @endif
        <p class="text-sm mt-3">Nasabah terpilih: <span id="selected-nasabah-label" class="font-medium">— belum ada —</span></p>
    </div>

    <div id="nasabah-box" class="sd-card p-5 hidden" style="background:var(--forest-deep)">
        <p class="sd-mono text-xs uppercase tracking-wider" style="color:var(--sage)">Saldo Tersedia</p>
        <p id="selected-saldo" class="sd-display text-2xl text-white mt-1">Rp0</p>
    </div>

    <form id="penarikan-form" method="POST" action="{{ route('penarikan.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="nasabah_id" id="selected-nasabah" value="{{ old('nasabah_id') }}">

        <div class="sd-card p-5">
            <p class="text-xs font-medium text-gray-500 mb-2">2. Jumlah Penarikan</p>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 sd-mono">Rp</span>
                <input type="number" name="jumlah" min="1" step="1" value="{{ old('jumlah') }}" class="sd-input w-full rounded-lg pl-9 pr-3 py-2.5 text-sm sd-mono" placeholder="0" required>
            </div>

            <p class="text-xs font-medium text-gray-500 mb-2 mt-4">Catatan (opsional)</p>
            <input type="text" name="catatan" value="{{ old('catatan') }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="mis. diambil tunai langsung">
        </div>

        <button type="submit" class="sd-btn-primary w-full rounded-lg py-3 text-sm font-medium">Proses Penarikan</button>
    </form>
</div>
@endsection
