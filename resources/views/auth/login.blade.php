<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk · SampahDesa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="sd-root min-h-screen flex" style="background:var(--forest-deep)">

    <div class="hidden md:flex relative flex-col justify-between w-1/2 lg:w-[55%] p-10 overflow-hidden">
        <img
            src="{{ asset('images/kkn-penganjang.jpg') }}"
            alt="Kegiatan mahasiswa KKN bersama perangkat Desa Penganjang"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0" style="background:linear-gradient(180deg, rgba(21,54,39,0.55) 0%, rgba(21,54,39,0.35) 45%, rgba(21,54,39,0.92) 100%)"></div>

        <div class="relative z-10 flex items-center gap-2 text-white">
            <div class="w-9 h-9 rounded-full flex items-center justify-center shadow-lg" style="background:var(--sage)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 21c0-9 6-15 15-15 0 9-6 15-15 15Z"/><path d="M5 21c2-6 6-10 12-12"/></svg></div>
            <span class="sd-display text-lg tracking-wide">SampahDesa</span>
        </div>

        <div class="relative z-10">
            <p class="sd-mono text-xs tracking-widest uppercase mb-3" style="color:var(--sage)">
                Desa Penganjang · Kec. Sindang · Indramayu
            </p>
            <h1 class="sd-display text-white text-4xl lg:text-[2.75rem] leading-tight mb-4 drop-shadow-sm">
                Buku tabungan sampah,<br> dijaga rapi tiap setoran.
            </h1>
            <p class="text-[#E4EEE8] text-sm leading-relaxed max-w-md mb-6">
                Dibangun bersama mahasiswa KKM dan perangkat Desa Penganjang —
                setiap setoran tercatat seperti buku rekening: jelas berat,
                jenis, nilai, dan saldo warga.
            </p>
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-[#CFE1D6] text-xs sd-mono">
                <span>PENCATATAN AKURAT</span>
                <span class="opacity-50">·</span>
                <span>SALDO TRANSPARAN</span>
                <span class="opacity-50">·</span>
                <span>KKM UMC 2026</span>
            </div>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-6" style="background:var(--paper)">
        <div class="w-full max-w-sm">
            <div class="md:hidden flex items-center gap-2 mb-8 justify-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background:var(--forest)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 21c0-9 6-15 15-15 0 9-6 15-15 15Z"/><path d="M5 21c2-6 6-10 12-12"/></svg></div>
                <span class="sd-display text-lg" style="color:var(--forest)">SampahDesa</span>
            </div>

            <div class="sd-card p-8 shadow-xl" style="border-radius:20px">
                <p class="sd-display text-xl mb-1" style="color:var(--forest-deep)">Selamat datang kembali</p>
                <p class="text-sm text-gray-500 mb-6">Masuk untuk mengelola bank sampah desa.</p>

                @if($errors->any())
                    <div class="mb-4 text-sm px-3 py-2.5 rounded-lg flex items-start gap-2" style="background:#FBEAE4; color:var(--danger)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="mt-0.5 shrink-0"><path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs font-medium mb-1.5 block" style="color:var(--forest-deep)">Username</label>
                        <div class="relative">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/></svg>
                            <input name="username" value="{{ old('username') }}" class="sd-input w-full rounded-lg pl-9 pr-3 py-2.5 text-sm" autofocus placeholder="Masukkan username">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1.5 block" style="color:var(--forest-deep)">Kata Sandi</label>
                        <div class="relative">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><rect x="4" y="10" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="2"/></svg>
                            <input type="password" name="password" class="sd-input w-full rounded-lg pl-9 pr-3 py-2.5 text-sm" placeholder="Masukkan kata sandi">
                        </div>
                    </div>
                    <button type="submit" class="sd-btn-primary w-full rounded-lg py-2.5 text-sm font-medium mt-2 shadow-sm">Masuk</button>
                </form>

                <p class="text-xs text-gray-400 pt-5 text-center leading-relaxed">
                    Lupa kata sandi? Hubungi admin pengelola bank sampah desa.
                </p>
            </div>

            <p class="text-center text-[11px] text-gray-400 mt-6">
                SampahDesa · Program KKM Desa Penganjang, Indramayu
            </p>
        </div>
    </div>
</body>
</html>
