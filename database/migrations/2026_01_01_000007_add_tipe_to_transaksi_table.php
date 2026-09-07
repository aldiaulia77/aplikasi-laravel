<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // 'setoran' menambah saldo, 'penarikan' mengurangi saldo.
            // total_nilai TETAP disimpan sebagai angka positif (nominal),
            // tandanya ditentukan oleh 'tipe' saat menghitung saldo
            // (lihat Nasabah::getSaldoAttribute()) — supaya nilai di
            // tampilan selalu mudah dibaca (tidak ada minus yang membingungkan).
            $table->enum('tipe', ['setoran', 'penarikan'])->default('setoran')->after('trx_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
