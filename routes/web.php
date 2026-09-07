<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisSampahController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PenarikanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// ---------- Auth ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1') // maks 5 percobaan/menit — cegah brute force
        ->name('login.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth', 'active'])->name('logout');

// ---------- Semua role yang sudah login ----------
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Nasabah & staf boleh melihat riwayat transaksi (scoped di controller)
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');

    // Ganti kata sandi sendiri — tersedia untuk semua role yang login
    Route::get('/profil/password', [ProfileController::class, 'edit'])->name('profil.password');
    Route::put('/profil/password', [ProfileController::class, 'update'])->name('profil.password.update');
});

// ---------- Admin only ----------
Route::middleware(['auth', 'active', 'role:admin'])->group(function () {
    Route::get('/nasabah', [NasabahController::class, 'index'])->name('nasabah.index');
    Route::get('/nasabah/create', [NasabahController::class, 'create'])->name('nasabah.create');
    Route::post('/nasabah', [NasabahController::class, 'store'])->name('nasabah.store');
    Route::get('/nasabah/{nasabah}/edit', [NasabahController::class, 'edit'])->name('nasabah.edit');
    Route::put('/nasabah/{nasabah}', [NasabahController::class, 'update'])->name('nasabah.update');

    Route::get('/jenis-sampah', [JenisSampahController::class, 'index'])->name('jenis.index');
    Route::post('/jenis-sampah', [JenisSampahController::class, 'store'])->name('jenis.store');
    Route::put('/jenis-sampah/{jenis}', [JenisSampahController::class, 'update'])->name('jenis.update');

    Route::get('/operator', [OperatorController::class, 'index'])->name('operator.index');
    Route::post('/operator', [OperatorController::class, 'store'])->name('operator.store');
    Route::put('/operator/{operator}', [OperatorController::class, 'update'])->name('operator.update');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'export'])
        ->middleware('throttle:10,1') // export dibatasi agar tidak membebani DB
        ->name('laporan.export');
});

// ---------- Admin + Operator (pencarian nasabah dipakai saat setoran) ----------
Route::middleware(['auth', 'active', 'role:admin,operator'])->group(function () {
    Route::get('/cari-nasabah', [NasabahController::class, 'index'])->name('nasabah.search');

    Route::get('/penarikan', [PenarikanController::class, 'create'])->name('penarikan.create');
    Route::post('/penarikan', [PenarikanController::class, 'store'])
        ->middleware('throttle:20,1') // cegah spam input transaksi
        ->name('penarikan.store');
});

// ---------- Operator only ----------
Route::middleware(['auth', 'active', 'role:operator'])->group(function () {
    Route::get('/setoran', [SetoranController::class, 'create'])->name('setoran.create');
    Route::post('/setoran', [SetoranController::class, 'store'])
        ->middleware('throttle:20,1') // cegah spam input transaksi
        ->name('setoran.store');
});
