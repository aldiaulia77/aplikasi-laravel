<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nasabah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nasabah';

    protected $fillable = [
        'kode_nasabah', 'nama', 'telepon', 'alamat', 'tanggal_daftar', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_daftar' => 'date',
        ];
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Saldo dihitung dari ledger transaksi (SUM bersyarat berdasarkan
     * tipe), bukan kolom yang ditimpa langsung, agar konsisten dan dapat
     * diaudit (lihat DATABASE.md). Setoran menambah, penarikan mengurangi.
     * Dua withSum terpisah (bukan satu CASE WHEN) karena itu pola yang
     * didukung Eloquent secara native — tetap 1 query SQL gabungan.
     */
    public function scopeWithSaldo($query)
    {
        return $query
            ->withSum(['transaksi as total_setoran' => fn ($q) => $q->where('tipe', 'setoran')], 'total_nilai')
            ->withSum(['transaksi as total_penarikan' => fn ($q) => $q->where('tipe', 'penarikan')], 'total_nilai');
    }

    public function getSaldoAttribute(): float
    {
        if (array_key_exists('total_setoran', $this->attributes) || array_key_exists('total_penarikan', $this->attributes)) {
            return (float) ($this->attributes['total_setoran'] ?? 0) - (float) ($this->attributes['total_penarikan'] ?? 0);
        }

        $setoran = $this->transaksi()->where('tipe', 'setoran')->sum('total_nilai');
        $penarikan = $this->transaksi()->where('tipe', 'penarikan')->sum('total_nilai');

        return (float) $setoran - (float) $penarikan;
    }

    /**
     * Placeholder unik sementara sebelum ID auto-increment tersedia.
     * Kode final di-assign setelah insert (lihat assignFinalKode()) agar
     * tidak bergantung pada MAX(id)+1 yang rentan race condition jika dua
     * admin menambah nasabah pada saat bersamaan.
     */
    public static function generateKode(): string
    {
        return 'NSB-PENDING-'.uniqid();
    }

    public function assignFinalKode(): void
    {
        $this->kode_nasabah = 'NSB-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
        $this->saveQuietly();
    }
}
