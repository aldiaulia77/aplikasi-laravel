<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SampahDesa') · Bank Sampah Digital</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="sd-root min-h-screen flex" style="background:var(--paper)">
    @php
        $user = auth()->user();
        $icon = function (string $name) {
            $paths = [
                'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
                'users' => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 20c0-3.6 2.5-6 5.5-6s5.5 2.4 5.5 6"/><circle cx="17" cy="8" r="2.5"/><path d="M15 14.2c2.4.4 4 2.4 4 5.8"/>',
                'recycle' => '<path d="M7 19H4.8a2 2 0 0 1-1.7-3l3-5.2M13 5.5l1.6 2.7M8 3.5h4.5L15 8M17 5.5l3 5.2a2 2 0 0 1-1.7 3H16M11 19h5.5l-1.7 3M9.5 22 7 17.5"/>',
                'shield' => '<path d="M12 3l7 3v6c0 4.6-3 7.8-7 9-4-1.2-7-4.4-7-9V6l7-3Z"/>',
                'clipboard' => '<rect x="6" y="4" width="12" height="17" rx="2"/><rect x="9" y="2.5" width="6" height="3" rx="1"/><path d="M9 11h6M9 15h6"/>',
                'file-text' => '<path d="M7 3h7l4 4v14a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M9 13h6M9 17h6"/>',
                'plus-circle' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>',
                'minus-circle' => '<circle cx="12" cy="12" r="9"/><path d="M8 12h8"/>',
                'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m20 20-4.3-4.3"/>',
                'history' => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v4h4"/><path d="M12 8v4l3 2"/>',
                'key' => '<circle cx="8" cy="15" r="4.5"/><path d="M11.5 11.5 20 3M16.5 7.5 19 5M18 9l2-2"/>',
                'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/>',
            ];
            return $paths[$name] ?? '';
        };
        $navLink = function (string $route, string $label, string $iconName, ?string $routePattern = null) use ($icon) {
            $pattern = $routePattern ?? $route;
            $active = request()->routeIs($pattern) ? 'active' : '';
            $svg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$icon($iconName).'</svg>';
            // e() meng-escape $label — jaga-jaga (defense in depth) meskipun
            // saat ini semua $label berasal dari teks tetap, bukan input user.
            return '<a href="'.route($route).'" class="sd-nav-item flex items-center gap-2.5 px-3 py-2.5 rounded-lg '.$active.'">'.$svg.'<span>'.e($label).'</span></a>';
        };
        $initials = collect(explode(' ', $user->name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    @endphp

    <aside class="w-[224px] shrink-0 hidden sm:flex flex-col justify-between py-5 px-3" style="background:var(--forest-deep)">
        <div>
            <div class="flex items-center gap-2.5 px-3 mb-8">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm" style="background:var(--sage)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--forest-deep)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 21c0-9 6-15 15-15 0 9-6 15-15 15Z"/><path d="M5 21c2-6 6-10 12-12"/></svg>
                </div>
                <span class="sd-display text-white text-base tracking-wide">SampahDesa</span>
            </div>
            <nav class="space-y-1 text-sm">
                {!! $navLink('dashboard', 'Dashboard', 'dashboard') !!}

                @if($user->isAdmin())
                    {!! $navLink('nasabah.index', 'Nasabah', 'users', 'nasabah.*') !!}
                    {!! $navLink('jenis.index', 'Jenis Sampah', 'recycle', 'jenis.*') !!}
                    {!! $navLink('operator.index', 'Operator', 'shield', 'operator.*') !!}
                    {!! $navLink('penarikan.create', 'Penarikan Saldo', 'minus-circle', 'penarikan.*') !!}
                    {!! $navLink('transaksi.index', 'Transaksi', 'clipboard', 'transaksi.*') !!}
                    {!! $navLink('laporan.index', 'Laporan', 'file-text', 'laporan.*') !!}
                @endif

                @if($user->isOperator())
                    {!! $navLink('setoran.create', 'Catat Setoran', 'plus-circle', 'setoran.*') !!}
                    {!! $navLink('penarikan.create', 'Penarikan Saldo', 'minus-circle', 'penarikan.*') !!}
                    {!! $navLink('nasabah.search', 'Cari Nasabah', 'search', 'nasabah.search') !!}
                    {!! $navLink('transaksi.index', 'Riwayat', 'history', 'transaksi.*') !!}
                @endif

                @if($user->isNasabah())
                    {!! $navLink('transaksi.index', 'Riwayat Setoran', 'history', 'transaksi.*') !!}
                @endif

                <div class="pt-1 mt-1 border-t" style="border-color:rgba(255,255,255,0.08)">
                    {!! $navLink('profil.password', 'Ganti Kata Sandi', 'key', 'profil.*') !!}
                </div>
            </nav>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="sd-nav-item w-full text-left flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icon('logout') !!}</svg>
                <span>Keluar</span>
            </button>
        </form>
    </aside>

    <main class="flex-1 flex flex-col min-w-0">
        <header class="flex items-center justify-between px-5 md:px-8 py-4 border-b" style="border-color:var(--line); background:var(--paper-card); box-shadow:0 1px 2px rgba(21,54,39,0.04)">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 sd-mono text-xs font-semibold" style="background:var(--sage-soft); color:var(--forest-deep)">
                    {{ $initials }}
                </div>
                <div>
                    <p class="sd-mono text-[11px] uppercase tracking-wider text-gray-400">
                        {{ ['admin' => 'Administrator', 'operator' => 'Operator', 'nasabah' => 'Nasabah'][$user->role] }}
                    </p>
                    <p class="sd-display text-base leading-tight" style="color:var(--forest-deep)">{{ $user->name }}</p>
                </div>
            </div>
            <div class="sd-chip px-3 py-1.5 rounded-full text-xs sd-mono hidden sm:block">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-5 md:p-8">
            @if(session('success'))
                <div class="sd-card p-3.5 mb-4 text-sm flex items-center gap-2.5" style="border-color:var(--sage); background:var(--sage-soft)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--forest)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 5-5"/></svg>
                    <span style="color:var(--forest-deep)">{{ session('success') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="sd-card p-3.5 mb-4 text-sm" style="border-color:var(--danger); color:var(--danger); background:#FBEAE4">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
