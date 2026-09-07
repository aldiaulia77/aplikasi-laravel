@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap items-end gap-3 justify-between">
        <p class="sd-display text-lg" style="color:var(--forest-deep)">Laporan Rekapitulasi</p>
        <form method="GET" class="flex items-end gap-2">
            <div>
                <label class="text-xs block mb-1">Dari</label>
                <input type="date" name="from" value="{{ $from }}" class="sd-input rounded-lg px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="text-xs block mb-1">Sampai</label>
                <input type="date" name="to" value="{{ $to }}" class="sd-input rounded-lg px-2 py-1.5 text-sm">
            </div>
            <button class="sd-btn-ghost rounded-lg px-3 py-2 text-sm">Terapkan</button>
            <a href="{{ route('laporan.export', ['from'=>$from,'to'=>$to]) }}" class="sd-btn-primary rounded-lg px-3 py-2 text-sm">Ekspor CSV</a>
        </form>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Total Transaksi" :value="$totals->total_trx" />
        <x-stat-card label="Total Berat Setoran" :value="number_format($totals->total_berat,1).' kg'" />
        <x-stat-card label="Total Setoran" value="Rp{{ number_format($totals->total_setoran,0,',','.') }}" gold="1" />
        <x-stat-card label="Total Penarikan" value="Rp{{ number_format($totals->total_penarikan,0,',','.') }}" />
    </div>

    <div class="sd-card p-5" style="background:var(--forest-deep)">
        <p class="sd-mono text-xs uppercase tracking-wider" style="color:var(--sage)">Perubahan Saldo Bersih (Setoran − Penarikan)</p>
        <p class="sd-display text-3xl text-white mt-1">Rp{{ number_format($totals->net,0,',','.') }}</p>
    </div>

    <div class="sd-card p-5">
        <p class="text-sm font-medium mb-3" style="color:var(--forest-deep)">Rekap per Jenis Sampah</p>
        @if($perJenis->isEmpty())
            <p class="text-sm text-gray-400 py-8 text-center">Tidak ada data pada rentang ini.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b" style="border-color:var(--line)">
                        <th class="py-2 font-medium">Jenis</th><th class="py-2 font-medium">Berat</th><th class="py-2 font-medium text-right">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($perJenis as $row)
                        <tr class="border-b last:border-0" style="border-color:var(--line)">
                            <td class="py-2">{{ $row->nama }}</td>
                            <td class="py-2 sd-mono">{{ number_format($row->berat,1) }} kg</td>
                            <td class="py-2 sd-mono text-right">Rp{{ number_format($row->nilai,0,',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
