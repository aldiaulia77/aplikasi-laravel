# AI_RULES.md — Aturan untuk AI Coding Assistant (Claude Code, dsb.)

## Sebelum Coding
1. Baca `docs/PROJECT_MASTER.md`
2. Baca `docs/ARCHITECTURE.md`
3. Baca `docs/DATABASE.md`
4. Baca `docs/PERFORMANCE.md` jika perubahan menyentuh performa
5. Baca `docs/SECURITY.md` jika perubahan menyentuh auth/keamanan
6. Inspeksi kode yang sudah ada — cari implementasi serupa sebelum
   membuat yang baru
7. Tentukan file yang benar-benar terdampak sebelum mengubah apa pun

## Jangan
- Membuat controller/model/route/service/component duplikat
- Membuat database kedua atau sistem auth kedua
- Mengganti stack (Laravel/MySQL/Blade/Tailwind) tanpa persetujuan eksplisit
- Melakukan rewrite besar-besaran atau menghapus fitur yang sudah berjalan
- Menyimpan `saldo` sebagai kolom yang ditimpa langsung — selalu hitung
  dari ledger transaksi (lihat DATABASE.md)
- Menambahkan Redis/queue/cache tambahan hanya supaya "terlihat modern" —
  setiap penambahan harus punya alasan teknis yang didokumentasikan

## Selalu
- Gunakan `paginate()` untuk listing yang bisa tumbuh besar, jangan
  `Model::all()`
- Gunakan eager loading (`with()`) untuk relasi yang ditampilkan berulang
- Bungkus operasi finansial multi-langkah dengan `DB::transaction()`
- Update dokumentasi terkait (`ARCHITECTURE.md`/`DATABASE.md`/dst.) jika
  perubahan memengaruhinya
- Tampilkan **Architecture Change Request** (current vs proposed, alasan,
  risiko, file & tabel terdampak) sebelum melakukan perubahan arsitektur
  besar, dan tunggu persetujuan
