# DATABASE.md — SampahDesa

## Tabel

### users
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | string | |
| username | string, unique | dipakai untuk login |
| email | string, unique, nullable | |
| password | string (hashed) | |
| role | enum(admin,operator,nasabah), index | |
| nasabah_id | FK -> nasabah.id, nullable | hanya diisi jika role=nasabah |
| is_active | boolean | |

### nasabah
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| kode_nasabah | string, unique | format NSB-0001 |
| nama | string, index | |
| telepon | string, nullable | |
| alamat | text, nullable | |
| tanggal_daftar | date | |
| status | enum(aktif,nonaktif), index | soft delete tersedia |

### jenis_sampah
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | string, index | |
| kategori | string, nullable | |
| satuan | string | default 'kg' |
| harga | decimal(12,2) | harga per satuan saat ini |
| status | enum(aktif,nonaktif), index | |

### transaksi
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| trx_id | string, unique | format TRX-YYYYMMDD-NNNNNN |
| nasabah_id | FK -> nasabah.id, index | |
| operator_id | FK -> users.id | |
| tanggal | date, index | index gabungan (nasabah_id, tanggal) |
| total_berat | decimal(10,2) | agregat dari transaksi_detail |
| total_nilai | decimal(14,2) | agregat dari transaksi_detail |

### transaksi_detail
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| transaksi_id | FK -> transaksi.id | |
| jenis_sampah_id | FK -> jenis_sampah.id (restrict delete) | |
| berat | decimal(10,2) | |
| harga_satuan | decimal(12,2) | snapshot harga saat transaksi (bukan referensi live) |
| subtotal | decimal(14,2) | |

### activity_logs
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | FK -> users.id, nullable, index | |
| action | string | mis. 'setoran.create', 'auth.login' |
| description | text, nullable | |
| created_at | timestamp, index | |

## Prinsip Saldo (Ledger)
Saldo TIDAK disimpan sebagai kolom yang ditimpa langsung
(`saldo = saldo + nilai`). Saldo dihitung on-the-fly:

```
saldo(nasabah) = SUM(transaksi.total_nilai) WHERE nasabah_id = X
```

Ini menjamin histori dapat diaudit dan menghindari race condition pada
update kolom saldo. Jika volume transaksi sudah sangat besar dan agregasi
on-the-fly mulai lambat, tambahkan tabel `saldo_ledger` ber-snapshot
periodik — jangan lakukan sebelum benar-benar diperlukan (lihat
PERFORMANCE.md, hindari overengineering).

## Index yang Dipasang
`users.email`, `users.role`, `nasabah.kode_nasabah` (unique),
`nasabah.nama`, `nasabah.status`, `jenis_sampah.nama`,
`jenis_sampah.status`, `transaksi.nasabah_id`, `transaksi.tanggal`,
`transaksi(nasabah_id, tanggal)` composite, `transaksi_detail.transaksi_id`
(via FK), `activity_logs.user_id`, `activity_logs.created_at`.
