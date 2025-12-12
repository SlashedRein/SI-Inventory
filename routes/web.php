<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grouping Middleware Auth (Harus Login Dulu)
Route::middleware('auth')->group(function () {
    
    // === MASTER DATA ===
    Route::resource('supplier', SupplierController::class);
    Route::resource('customer', CustomerController::class);
    Route::resource('bahan-baku', BahanBakuController::class);
    Route::resource('produk', ProdukController::class);

    // === RESEP (Khusus) ===
    // URL: /produk/{id}/resep
    Route::get('produk/{id}/resep', [ResepController::class, 'edit'])->name('resep.edit');
    Route::post('produk/{id}/resep', [ResepController::class, 'store'])->name('resep.store');
    Route::delete('resep/{id}', [ResepController::class, 'destroy'])->name('resep.destroy');

    // === TRANSAKSI ===
    Route::resource('pembelian', PembelianController::class);
    Route::resource('penjualan', PenjualanController::class);

    // === LAPORAN ===
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // === PROFILE (Bawaan Breeze) ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';