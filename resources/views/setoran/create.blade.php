@extends('layouts.app')
@section('title', 'Catat Setoran')
@section('content')
<div class="max-w-2xl space-y-5">
    <p class="sd-display text-lg" style="color:var(--forest-deep)">Catat Setoran Sampah</p>

    <div class="sd-card p-5">
        <p class="text-xs font-medium text-gray-500 mb-2">1. Cari & Pilih Nasabah</p>
        <form method="GET" class="relative">
            <input name="q" value="{{ request('q') }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Ketik nama atau kode nasabah, lalu Enter…">
        </form>
        @if(request('q'))
            <div class="mt-2 divide-y" style="border-color:var(--line)">
                @forelse($nasabahResult as $n)
                    <button form="setoran-form" type="submit" name="nasabah_id" value="{{ $n->id }}"
                        class="w-full text-left px-3 py-2 text-sm hover:opacity-80 flex justify-between sd-chip rounded-lg my-1"
                        onclick="document.getElementById('selected-nasabah').value='{{ $n->id }}'; document.getElementById('selected-nasabah-label').innerText='{{ $n->nama }} ({{ $n->kode_nasabah }})'; return false;">
                        <span>{{ $n->nama }}</span><span class="sd-mono text-xs text-gray-500">{{ $n->kode_nasabah }}</span>
                    </button>
                @empty
                    <p class="text-sm text-gray-400 py-3">Tidak ditemukan.</p>
                @endforelse
            </div>
        @endif
        <p class="text-sm mt-3">Nasabah terpilih: <span id="selected-nasabah-label" class="font-medium">— belum ada —</span></p>
    </div>

    <form id="setoran-form" method="POST" action="{{ route('setoran.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="nasabah_id" id="selected-nasabah" value="{{ old('nasabah_id') }}">

        <div class="sd-card p-5">
            <p class="text-xs font-medium text-gray-500 mb-3">2. Jenis & Berat Sampah</p>
            <div id="item-rows" class="space-y-2">
                <div class="flex gap-2 items-center item-row">
                    <select name="items[0][jenis_sampah_id]" class="sd-input rounded-lg px-2 py-2 text-sm flex-1">
                        @foreach($jenisSampah as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }} · Rp{{ number_format($j->harga,0,',','.') }}/{{ $j->satuan }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.1" min="0" name="items[0][berat]" placeholder="Berat (kg)" class="sd-input rounded-lg px-3 py-2 text-sm w-28">
                </div>
            </div>
            <button type="button" id="add-row" class="sd-btn-ghost rounded-lg px-3 py-1.5 text-xs mt-3">+ Tambah jenis lain</button>
        </div>

        <button type="submit" class="sd-btn-primary w-full rounded-lg py-3 text-sm font-medium">Simpan Setoran</button>
    </form>
</div>

<script>
let rowIndex = 1;
document.getElementById('add-row').addEventListener('click', function () {
    const wrapper = document.getElementById('item-rows');
    const row = wrapper.querySelector('.item-row').cloneNode(true);
    row.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${rowIndex}]`);
        if (el.tagName === 'INPUT') el.value = '';
    });
    wrapper.appendChild(row);
    rowIndex++;
});
</script>
@endsection
