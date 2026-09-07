<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'trx_id', 'tipe', 'nasabah_id', 'operator_id', 'tanggal', 'total_berat', 'total_nilai', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total_berat' => 'decimal:2',
            'total_nilai' => 'decimal:2',
        ];
    }

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    public function isSetoran(): bool
    {
        return $this->tipe === 'setoran';
    }

    public function isPenarikan(): bool
    {
        return $this->tipe === 'penarikan';
    }

    /**
     * Efek transaksi ini terhadap saldo: positif untuk setoran, negatif
     * untuk penarikan. Dipakai di tampilan (mis. tanda +/- di riwayat).
     */
    public function getEfekSaldoAttribute(): float
    {
        return $this->isPenarikan() ? -1 * (float) $this->total_nilai : (float) $this->total_nilai;
    }

    /**
     * Format sementara sebelum ID auto-increment tersedia. ID final di-set
     * setelah insert (lihat assignFinalTrxId()) untuk menjamin keunikan
     * tanpa race condition — lihat catatan di bawah.
     */
    public static function generateTrxId(): string
    {
        // Placeholder unik sementara (dipakai hanya sesaat sebelum insert,
        // lalu ditimpa dengan ID final berbasis auto-increment).
        return 'TRX-PENDING-'.uniqid();
    }

    /**
     * ID final yang deterministik & unik, dibentuk dari auto-increment id
     * milik baris itu sendiri — TIDAK bergantung pada COUNT(*) yang rentan
     * race condition saat dua request bersamaan (lihat SetoranService /
     * PenarikanService). Prefix beda antara setoran (TRX) dan penarikan
     * (PNC) supaya gampang dibedakan sekilas di daftar transaksi.
     */
    public function assignFinalTrxId(): void
    {
        $prefix = $this->isPenarikan() ? 'PNC' : 'TRX';
        $this->trx_id = sprintf('%s-%s-%06d', $prefix, $this->created_at->format('Ymd'), $this->id);
        $this->saveQuietly();
    }
}
