@extends('layouts.app')
@section('title', 'Nasabah')
@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" class="relative w-full sm:w-72">
            <input name="q" value="{{ request('q') }}" class="sd-input w-full rounded-lg pl-3 pr-3 py-2 text-sm" placeholder="Cari nama, kode, atau telepon…">
        </form>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('nasabah.create') }}" class="sd-btn-primary rounded-lg px-4 py-2 text-sm inline-flex items-center gap-1.5 self-start">+ Tambah Nasabah</a>
        @endif
    </div>

    <div class="sd-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 border-b" style="border-color:var(--line)">
                    <th class="px-4 py-3 font-medium">Kode</th>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium hidden md:table-cell">Telepon</th>
                    <th class="px-4 py-3 font-medium">Saldo</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    @if(auth()->user()->isAdmin())<th class="px-4 py-3 font-medium text-right">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($nasabah as $n)
                    <tr class="border-b last:border-0" style="border-color:var(--line)">
                        <td class="px-4 py-3 sd-mono text-xs">{{ $n->kode_nasabah }}</td>
                        <td class="px-4 py-3 font-medium">{{ $n->nama }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-gray-500">{{ $n->telepon ?? '—' }}</td>
                        <td class="px-4 py-3 sd-mono">Rp{{ number_format($n->saldo,0,',','.') }}</td>
                        <td class="px-4 py-3"><span class="sd-chip text-[11px] px-2 py-0.5 rounded-full {{ $n->status!=='aktif'?'opacity-50':'' }}">{{ $n->status }}</span></td>
                        @if(auth()->user()->isAdmin())
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('nasabah.edit', $n) }}" class="text-xs" style="color:var(--forest)">Edit</a>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">Tidak ada data nasabah.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$nasabah" />
    </div>
</div>
@endsection
