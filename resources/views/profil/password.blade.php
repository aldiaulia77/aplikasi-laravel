@extends('layouts.app')
@section('title', 'Ganti Kata Sandi')
@section('content')
<div class="max-w-md sd-card p-6">
    <p class="sd-display text-lg mb-4" style="color:var(--forest-deep)">Ganti Kata Sandi</p>
    <form method="POST" action="{{ route('profil.password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="text-xs font-medium mb-1 block">Kata Sandi Saat Ini</label>
            <input type="password" name="current_password" class="sd-input w-full rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="text-xs font-medium mb-1 block">Kata Sandi Baru</label>
            <input type="password" name="password" class="sd-input w-full rounded-lg px-3 py-2 text-sm" required minlength="6">
        </div>
        <div>
            <label class="text-xs font-medium mb-1 block">Ulangi Kata Sandi Baru</label>
            <input type="password" name="password_confirmation" class="sd-input w-full rounded-lg px-3 py-2 text-sm" required minlength="6">
        </div>
        <button class="sd-btn-primary w-full rounded-lg py-2.5 text-sm font-medium">Simpan Kata Sandi Baru</button>
    </form>
</div>
@endsection
