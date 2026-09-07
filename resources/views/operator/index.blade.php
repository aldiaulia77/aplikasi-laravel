@extends('layouts.app')
@section('title', 'Operator')
@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <p class="sd-display text-lg" style="color:var(--forest-deep)">Akun Operator</p>
        <button onclick="document.getElementById('modal-new').classList.remove('hidden')" class="sd-btn-primary rounded-lg px-4 py-2 text-sm">+ Tambah Operator</button>
    </div>

    <div class="sd-card divide-y" style="border-color:var(--line)">
        @forelse($operators as $op)
            <div class="flex items-center justify-between px-4 py-3">
                <div>
                    <p class="font-medium text-sm">{{ $op->name }} @unless($op->is_active)<span class="text-xs text-red-500">(nonaktif)</span>@endunless</p>
                    <p class="sd-mono text-xs text-gray-400">{{ '@'.$op->username }}</p>
                </div>
                <button onclick="document.getElementById('modal-edit-{{ $op->id }}').classList.remove('hidden')" class="text-xs" style="color:var(--forest)">Edit</button>
            </div>
            <div id="modal-edit-{{ $op->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(21,54,39,0.45)">
                <div class="sd-card w-full max-w-md p-6 shadow-xl">
                    <p class="sd-display text-base mb-4" style="color:var(--forest-deep)">Edit Operator</p>
                    <form method="POST" action="{{ route('operator.update', $op) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <input name="name" value="{{ $op->name }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Nama">
                        <input name="username" value="{{ $op->username }}" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Username">
                        <input name="password" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Kata sandi baru (kosongkan jika tidak diubah)">
                        <select name="is_active" class="sd-input w-full rounded-lg px-3 py-2 text-sm">
                            <option value="1" @selected($op->is_active)>Aktif</option>
                            <option value="0" @selected(!$op->is_active)>Nonaktif</option>
                        </select>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('modal-edit-{{ $op->id }}').classList.add('hidden')" class="sd-btn-ghost rounded-lg px-4 py-2 text-sm">Batal</button>
                            <button class="sd-btn-primary rounded-lg px-4 py-2 text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <p class="px-4 py-10 text-center text-gray-400 text-sm">Belum ada akun operator.</p>
        @endforelse
    </div>
    <x-pagination :paginator="$operators" />

    <div id="modal-new" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(21,54,39,0.45)">
        <div class="sd-card w-full max-w-md p-6 shadow-xl">
            <p class="sd-display text-base mb-4" style="color:var(--forest-deep)">Tambah Operator</p>
            <form method="POST" action="{{ route('operator.store') }}" class="space-y-3">
                @csrf
                <input name="name" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Nama lengkap">
                <input name="username" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Username">
                <input name="password" class="sd-input w-full rounded-lg px-3 py-2 text-sm" placeholder="Kata sandi">
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-new').classList.add('hidden')" class="sd-btn-ghost rounded-lg px-4 py-2 text-sm">Batal</button>
                    <button class="sd-btn-primary rounded-lg px-4 py-2 text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
