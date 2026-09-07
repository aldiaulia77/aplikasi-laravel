# PROJECT_MASTER.md — SampahDesa

## Tujuan
Aplikasi bank sampah digital untuk Desa Penganjang, Kec. Sindang, Kab.
Indramayu. Dibangun berdasarkan proposal KKM Universitas Muhammadiyah
Cirebon untuk membantu pencatatan nasabah, jenis/berat sampah, transaksi
setoran, saldo/tabungan, dan rekapitulasi laporan.

## Scope
Prototipe produksi ringan yang bisa dioperasikan oleh operator desa
(PKK/Karang Taruna) tanpa pelatihan teknis mendalam.

## Fitur
- Autentikasi & otorisasi berbasis role (admin, operator, nasabah)
- Master data nasabah & jenis sampah (harga per kg)
- Pencatatan setoran (multi-item per transaksi) dengan perhitungan
  otomatis (berat × harga)
- Saldo nasabah dihitung dari ledger transaksi (bukan kolom yang ditimpa)
- Riwayat transaksi (scoped per role)
- Laporan rekapitulasi per periode + ekspor CSV (streaming, chunked)
- Activity log untuk aksi penting (login, setoran, perubahan harga, dst)

## Role
| Role     | Akses utama                                              |
|----------|-----------------------------------------------------------|
| admin    | kelola nasabah, jenis sampah, operator, laporan, semua trx |
| operator | cari nasabah, catat setoran, lihat riwayat                |
| nasabah  | lihat profil, saldo, riwayat setoran sendiri               |

## Status Fitur (baseline rilis pertama)
- [x] Auth + role middleware
- [x] CRUD nasabah, jenis sampah, operator
- [x] Pencatatan setoran dengan DB::transaction() + row locking harga
- [x] Riwayat transaksi berpaginasi
- [x] Laporan rekap + ekspor CSV streaming
- [ ] Queue untuk export skala besar (siap dipasang, lihat PERFORMANCE.md)
- [ ] Redis cache/session (opsional, pasang saat traffic riil menuntutnya)
- [ ] Load testing k6/JMeter (Phase 12, lakukan sebelum klaim 1.000 CCU)

## Aturan Project
Lihat ARCHITECTURE.md, DATABASE.md, PERFORMANCE.md, SECURITY.md sebelum
melakukan perubahan besar. Jangan membuat controller/model/route duplikat.
