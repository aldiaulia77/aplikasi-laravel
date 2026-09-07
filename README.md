# SampahDesa — Bank Sampah Digital Desa Penganjang

Laravel 12 + MySQL. Paket ini berisi **kode aplikasi** (migrations, models,
controllers, views, routes, seeders, dokumentasi) yang perlu ditempelkan
ke atas skeleton resmi Laravel 12. Ini karena instalasi Composer/Node
tidak bisa dijalankan dari tempat kode ini dibuat — jadi langkah instalasi
di bawah dijalankan di komputer/server kamu sendiri (Laragon/Herd/XAMPP
untuk development, atau Nginx+PHP-FPM untuk production).

---

## 1. Prasyarat

- PHP 8.2+ dengan ekstensi: `mbstring`, `openssl`, `pdo_mysql`, `curl`,
  `fileinfo`, `bcmath`
- Composer 2.x
- Node.js 18+ dan npm
- MySQL 8.x (atau MariaDB 10.6+)
- Git (opsional, memudahkan tapi tidak wajib)

Cek dulu:
```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

---

## 2. Buat Skeleton Laravel 12 Resmi

Jalankan di folder tempat kamu mau menyimpan project (mis. `htdocs`,
`www`, atau folder project Laragon/Herd):

```bash
composer create-project laravel/laravel sampahdesa "12.*"
cd sampahdesa
```

Perintah ini mengunduh Laravel 12 asli lengkap dengan `vendor/`,
`public/index.php`, `artisan`, dsb — semua yang tidak bisa saya
sertakan langsung di paket ini.

> Catatan: `composer create-project laravel/laravel sampahdesa "12.*"`
> otomatis mengunci `composer.json` ke `"laravel/framework": "^12.0"`,
> jadi tidak perlu diedit manual. Kalau kamu memakai Laravel Installer
> (`laravel new sampahdesa`), pilih versi 12.x saat diminta.

---

## 3. Tempelkan File dari Paket Ini

Salin folder/file berikut dari paket `sampahdesa/` (yang saya berikan) ke
dalam project Laravel yang baru dibuat, **timpa file yang sudah ada**:

```
app/Http/Controllers/*.php   -> app/Http/Controllers/
app/Http/Middleware/*.php    -> app/Http/Middleware/
app/Models/*.php             -> app/Models/          (timpa User.php)
app/Services/*.php           -> app/Services/         (buat folder baru)
bootstrap/app.php            -> bootstrap/app.php     (timpa)
database/migrations/*.php    -> database/migrations/  (hapus migration
                                 default users/cache/jobs bawaan Laravel
                                 dulu supaya tidak bentrok — lihat §3a)
database/seeders/*.php       -> database/seeders/     (timpa DatabaseSeeder.php)
resources/views/*            -> resources/views/       (timpa welcome.blade.php boleh dihapus)
resources/css/app.css        -> resources/css/app.css (timpa)
resources/js/app.js          -> resources/js/app.js   (timpa)
resources/js/bootstrap.js    -> resources/js/bootstrap.js (timpa)
routes/web.php               -> routes/web.php        (timpa)
routes/console.php           -> routes/console.php    (timpa)
vite.config.js                -> vite.config.js        (timpa)
package.json                  -> package.json          (timpa)
docs/*.md                     -> docs/                 (buat folder baru)
.env.example                  -> .env.example          (timpa)
```

### 3a. Hapus migration bawaan yang bentrok

Skeleton Laravel baru sudah punya migration default untuk `users`,
`cache`, dan `jobs`. Hapus yang **users** dan **cache** bawaan (karena
sudah digantikan versi kustom di paket ini), tapi **biarkan** migration
`jobs` bawaan (dipakai untuk `QUEUE_CONNECTION=database`):

```bash
rm database/migrations/*_create_users_table.php
rm database/migrations/*_create_cache_table.php
# migration users & cache versi kustom dari paket ini akan menggantikannya
```

Lalu salin migration dari paket ini ke `database/migrations/`.

---

## 4. Install Dependency

```bash
composer install
npm install
npm install @tailwindcss/vite tailwindcss --save-dev
```

---

## 5. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`, sesuaikan koneksi database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sampahdesa
DB_USERNAME=root
DB_PASSWORD=
```

Buat database-nya (via phpMyAdmin, HeidiSQL, atau CLI):
```sql
CREATE DATABASE sampahdesa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 6. Migrasi & Seed Data Awal

```bash
php artisan migrate --seed
```

Ini akan membuat semua tabel dan mengisi data awal:
- Admin: `admin` / `admin123`
- Operator: `operator` / `operator123`
- 3 nasabah contoh dengan akun login `nsb-0001` / `NSB-0001`, dst.

**Wajib ganti seluruh password default ini sebelum dipakai warga desa.**

---

## 7. Build Frontend & Jalankan

Development:
```bash
npm run dev
# di terminal terpisah:
php artisan serve
```
Buka `http://localhost:8000`.

Production build:
```bash
npm run build
```

---

## 8. Deployment ke Production (Nginx + PHP-FPM + MySQL)

1. Upload seluruh project ke server (kecuali `node_modules/`, `.env`).
2. `composer install --optimize-autoloader --no-dev`
3. `npm ci && npm run build` (bisa dilakukan lokal lalu upload folder
   `public/build/` saja jika server tidak punya Node).
4. Set `.env` production: `APP_ENV=production`, `APP_DEBUG=false`,
   `APP_URL` sesuai domain, kredensial DB production.
5. `php artisan key:generate --force` (sekali saja, di server)
6. `php artisan migrate --force --seed` (seed cukup sekali saat awal)
7. Cache untuk performa:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
8. Arahkan document root Nginx ke folder `public/` project ini, contoh
   konfigurasi dasar:
   ```nginx
   server {
       listen 80;
       server_name sampahdesa.desapenganjang.id;
       root /var/www/sampahdesa/public;

       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```
9. Set permission storage: `chmod -R 775 storage bootstrap/cache` dan
   pastikan dimiliki user PHP-FPM (mis. `www-data`).
10. Pasang HTTPS (Let's Encrypt/Certbot).
11. Ikuti checklist lengkap di `docs/PERFORMANCE.md` dan
    `docs/SECURITY.md` sebelum go-live.

---

## 9. Struktur Dokumentasi

Baca urutan ini sebelum mengembangkan fitur baru:
`docs/PROJECT_MASTER.md` → `docs/ARCHITECTURE.md` → `docs/DATABASE.md` →
`docs/PERFORMANCE.md` (jika menyentuh performa) →
`docs/SECURITY.md` (jika menyentuh auth/keamanan).

## 10. Kembangkan Lanjutan dengan Claude Code

Paket ini adalah baseline yang solid dan bisa langsung dipakai. Untuk
pengembangan lanjutan (fitur baru, load testing, integrasi Redis/queue,
dsb.), paling efektif dikerjakan dengan **Claude Code** langsung di
folder project ini — karena Claude Code bisa menjalankan `composer`,
`artisan`, `npm`, dan mysql secara langsung di komputermu, yang tidak
bisa dilakukan dari chat ini.
