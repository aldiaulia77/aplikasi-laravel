<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('trx_id', 30)->unique();
            $table->foreignId('nasabah_id')->constrained('nasabah')->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal')->index();
            $table->decimal('total_berat', 10, 2)->default(0);
            $table->decimal('total_nilai', 14, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['nasabah_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
