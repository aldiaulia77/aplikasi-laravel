# SECURITY.md — SampahDesa

## Diterapkan
- CSRF protection (bawaan Laravel, semua form pakai `@csrf`)
- Password hashing otomatis via cast `'password' => 'hashed'`
- Session-based auth + `session()->regenerate()` setelah login
- Role-based access control via `RoleMiddleware`
- Validasi input di setiap controller (`$request->validate()`)
- Rate limiting pada login, setoran, export
- Output otomatis di-escape oleh Blade (`{{ }}`)
- `.env` tidak boleh di-commit (lihat `.gitignore` bawaan Laravel)

## Checklist Sebelum Produksi
- [ ] Ganti seluruh password default (admin123, operator123, kode nasabah)
- [ ] Set `APP_DEBUG=false` di `.env` produksi
- [ ] Set `APP_KEY` unik (`php artisan key:generate`), jangan pernah
      dibagikan
- [ ] Aktifkan HTTPS (redirect http->https di Nginx)
- [ ] Backup database rutin
- [ ] Batasi akses fisik/SSH ke server produksi

## File Upload
Baseline belum memiliki fitur upload file. Jika ditambahkan (mis. bukti
setoran/foto), wajib: validasi MIME type & ukuran, generate nama file
acak, simpan di `storage/app/public` (bukan langsung ke DB), jangan
percaya nama file dari user.

## Logging
Gunakan `activity_logs` untuk aksi penting (login, setoran, perubahan
harga, perubahan data user). Jangan pernah mencatat password/token ke log
aplikasi (`storage/logs/laravel.log`).
