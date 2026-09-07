@extends('layouts.app')
@section('title', 'Ringkasan')
@section('content')
<div class="max-w-2xl space-y-6">
    <div class="sd-card p-6" style="background:var(--forest-deep)">
        <p class="sd-mono text-xs uppercase tracking-wider" style="color:var(--sage)">Saldo Tabungan Sampah</p>
        <p class="sd-display text-4xl text-white mt-2">Rp{{ number_format($nasabah->saldo ?? 0,0,',','.') }}</p>
        <p class="text-[#CFE1D6] text-sm mt-1 sd-mono">{{ $nasabah->kode_nasabah ?? '-' }} · {{ $nasabah->nama ?? '-' }}</p>
    </div>
    <div class="sd-card p-5">
        <p class="sd-display text-base mb-3" style="color:var(--forest-deep)">Riwayat Terbaru</p>
        @forelse($riwayat as $t)
            <x-ledger-row :t="$t" />
        @empty
            <p class="text-sm text-gray-400 py-8 text-center">Belum ada transaksi tercatat.</p>
        @endforelse
    </div>
</div>
@endsection
