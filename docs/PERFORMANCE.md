# PERFORMANCE.md — SampahDesa

## Target
Didesain untuk 1.000+ concurrent users ("Designed for 1,000+ concurrent
users" — klaim ini baru valid setelah load testing aktual, lihat §Load
Testing).

## Prinsip yang Sudah Diterapkan
- **Pagination di semua listing besar**: nasabah, jenis sampah, operator,
  transaksi (`paginate()`), tidak ada `Model::all()` pada tabel yang bisa
  tumbuh besar.
- **Eager loading**: semua listing transaksi memakai
  `with(['nasabah', 'detail.jenisSampah'])` untuk mencegah N+1.
- **Aggregate query**: statistik dashboard & laporan pakai `SUM`, `COUNT`,
  `GROUP BY` di level database, bukan diambil lalu dihitung di PHP.
- **select() kolom spesifik** pada relasi ringan (mis.
  `nasabah:id,nama,kode_nasabah`) untuk mengurangi payload.
- **DB::transaction() + lockForUpdate()** pada pencatatan setoran
  (SetoranService) agar dua request bersamaan tidak merusak saldo.
- **Rate limiting**: login (5/menit), setoran (20/menit), export laporan
  (10/menit).
- **Export CSV streaming + chunk(200)** agar tidak memuat seluruh dataset
  ke memory PHP.

## Belum Diaktifkan (aktifkan hanya jika diperlukan)
- **Redis** untuk cache/session/queue — baseline pakai driver `database`.
  Pindah ke Redis jika: (a) traffic riil menunjukkan bottleneck DB session,
  atau (b) queue job mulai menumpuk. Jangan pasang tanpa data yang
  menunjukkan kebutuhan nyata.
- **Queue job async** untuk export skala sangat besar. Baseline
  menggunakan streaming synchronous karena volume data desa masih kecil;
  pindahkan ke Job + status PROCESSING/COMPLETED/FAILED saat data sudah
  besar (ribuan+ baris per export).

## Checklist Sebelum Deploy Produksi
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `composer install --optimize-autoloader --no-dev`
- [ ] `npm run build`
- [ ] Set `APP_DEBUG=false`
- [ ] Konfigurasi PHP-FPM worker count sesuai kapasitas server
- [ ] Konfigurasi MySQL `max_connections` sesuai PHP-FPM worker count

## Load Testing (Phase 12 — wajib sebelum klaim kapasitas)
Gunakan k6 / Apache JMeter / Artillery. Ukur: RPS, response time,
p95/p99 latency, error rate, CPU/RAM, PHP-FPM worker saturation, MySQL
connections, slow query log. Jangan menyatakan "sudah support 1.000 user"
tanpa hasil pengujian ini didokumentasikan di sini.
