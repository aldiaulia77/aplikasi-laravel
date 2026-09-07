@props(['t', 'showNasabah' => false])
@php
    $isPenarikan = $t->tipe === 'penarikan';
@endphp
<div class="sd-ledger-row py-3 flex items-center justify-between gap-3">
    <div class="min-w-0">
        <p class="sd-mono text-[11px] text-gray-400">
            {{ $t->trx_id }} · {{ $t->tanggal->translatedFormat('d M Y') }}
            @if($isPenarikan)
                <span class="sd-chip text-[10px] px-1.5 py-0.5 rounded-full ml-1" style="background:#FBEAE4; color:var(--danger)">Penarikan</span>
            @endif
        </p>
        <p class="text-sm truncate">
            @if($showNasabah)
                <span class="font-medium">{{ $t->nasabah->nama ?? '-' }}</span> ·
            @endif
            @if($isPenarikan)
                {{ $t->catatan ?: 'Pencairan saldo' }}
            @else
                {{ $t->detail->pluck('jenisSampah.nama')->filter()->join(', ') }}
            @endif
        </p>
    </div>
    <div class="text-right shrink-0">
        <p class="sd-mono text-sm font-semibold" style="color: {{ $isPenarikan ? 'var(--danger)' : 'var(--forest)' }}">
            {{ $isPenarikan ? '-' : '+' }}Rp{{ number_format($t->total_nilai,0,',','.') }}
        </p>
        @if(!$isPenarikan)
            <p class="text-[11px] text-gray-400">{{ number_format($t->total_berat,1) }} kg</p>
        @endif
    </div>
</div>
