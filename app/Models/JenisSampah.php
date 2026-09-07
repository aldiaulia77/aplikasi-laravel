<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisSampah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_sampah';

    protected $fillable = [
        'nama', 'kategori', 'satuan', 'harga', 'status',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
