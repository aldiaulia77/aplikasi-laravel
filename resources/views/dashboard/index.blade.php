@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Total Nasabah" :value="$stats['total_nasabah']" :sub="$stats['nasabah_aktif'].' aktif'" />
        <x-stat-card label="Berat Bulan Ini" :value="number_format($stats['berat_bulan_ini'],1).' kg'" />
        <x-stat-card label="Nilai Bulan Ini" value="Rp{{ number_format($stats['nilai_bulan_ini'],0,',','.') }}" :sub="$stats['trx_bulan_ini'].' transaksi'" gold="1" />
        <x-stat-card label="Jenis Sampah" :value="$stats['total_jenis']" />
    </div>

    @if(auth()->user()->isOperator())
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('setoran.create') }}" class="sd-btn-primary rounded-xl px-5 py-3 text-sm font-medium inline-flex items-center gap-2">+ Catat Setoran Baru</a>
            <a href="{{ route('penarikan.create') }}" class="sd-btn-ghost rounded-xl px-5 py-3 text-sm font-medium inline-flex items-center gap-2">Penarikan Saldo</a>
        </div>
    @endif

    <div class="sd-card p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="sd-display text-base" style="color:var(--forest-deep)">Transaksi Terbaru</p>
            <a href="{{ route('transaksi.index') }}" class="text-xs" style="color:var(--forest)">Lihat semua →</a>
        </div>
        @forelse($recent as $t)
            <x-ledger-row :t="$t" :show-nasabah="true" />
        @empty
            <p class="text-sm text-gray-400 py-8 text-center">Belum ada transaksi.</p>
        @endforelse
    </div>
</div>
@endsection
