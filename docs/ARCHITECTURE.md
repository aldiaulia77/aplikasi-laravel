# ARCHITECTURE.md — SampahDesa

## Stack
Laravel 12 · PHP 8.2+ · MySQL · Blade · Tailwind CSS v4 · Vite
Production: Nginx + PHP-FPM + MySQL (lihat README.md §Deployment)

## Struktur Folder (delta di atas skeleton `laravel/laravel`)
```
app/
  Http/
    Controllers/   -> Auth, Dashboard, Nasabah, JenisSampah, Operator,
                       Setoran, Transaksi, Laporan
    Middleware/     -> RoleMiddleware (alias: role:admin|operator|nasabah)
  Models/           -> User, Nasabah, JenisSampah, Transaksi,
                       TransaksiDetail, ActivityLog
  Services/         -> SetoranService (transaksi DB + row locking)
database/
  migrations/       -> users, nasabah, jenis_sampah, transaksi,
                       transaksi_detail, activity_logs
  seeders/          -> UserSeeder, JenisSampahSeeder, NasabahSeeder
resources/views/    -> layouts/app, auth/login, dashboard/*, nasabah/*,
                       jenis/*, operator/*, setoran/*, transaksi/*, laporan/*
routes/web.php      -> seluruh route, dikelompokkan per role
bootstrap/app.php   -> registrasi middleware alias 'role', health check
```

## Alur Request
```
CLIENT -> NGINX -> PHP-FPM -> Laravel Router -> Middleware(auth, role,
throttle) -> Controller -> Service/Model (Eloquent) -> MySQL
```

## Alur Setoran (kritis)
```
Operator input nasabah + item(jenis, berat)
  -> SetoranController@store (validasi)
  -> SetoranService::catat()
       DB::transaction {
         lockForUpdate() jenis_sampah yang dipakai
         hitung subtotal per item
         create Transaksi + TransaksiDetail
         ActivityLog::record()
       }
  -> saldo nasabah = SUM(transaksi.total_nilai) [ledger, dihitung on-the-fly]
```

## Authentication & Authorization
- Session-based auth bawaan Laravel (`Auth::attempt`)
- Role disimpan di kolom `users.role` (enum: admin/operator/nasabah)
- `RoleMiddleware` (alias `role:...`) membatasi route per role
- Rate limiting: `throttle:5,1` pada login, `throttle:20,1` pada setoran,
  `throttle:10,1` pada export laporan

## Queue & Cache (siap pakai, belum wajib aktif)
- `QUEUE_CONNECTION=database` secara default; pindah ke Redis hanya jika
  volume export/notifikasi sudah signifikan (lihat PERFORMANCE.md §9-10)
- Cache cocok untuk: daftar jenis sampah, statistik dashboard non-real-time

## API
Belum ada endpoint `routes/api.php` pada baseline ini karena proposal KKM
tidak mensyaratkan integrasi eksternal. Tambahkan hanya jika ada kebutuhan
nyata (mis. integrasi WhatsApp gateway notifikasi saldo).
