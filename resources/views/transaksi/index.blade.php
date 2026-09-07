@extends('layouts.app')
@section('title', 'Transaksi')
@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <p class="sd-display text-lg" style="color:var(--forest-deep)">Riwayat Transaksi</p>
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="from" value="{{ request('from') }}" class="sd-input rounded-lg px-2 py-1.5 text-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="sd-input rounded-lg px-2 py-1.5 text-sm">
            <button class="sd-btn-ghost rounded-lg px-3 py-1.5 text-sm">Filter</button>
        </form>
    </div>

    <div class="sd-card p-2">
        @forelse($transaksi as $t)
            <div class="px-3">
                <x-ledger-row :t="$t" :show-nasabah="!auth()->user()->isNasabah()" />
            </div>
        @empty
            <p class="text-sm text-gray-400 py-10 text-center">Belum ada transaksi.</p>
        @endforelse
    </div>
    <x-pagination :paginator="$transaksi" />
</div>
@endsection
