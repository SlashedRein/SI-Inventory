<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    SupplierController,
    CustomerController,
    BahanBakuController,
    ProdukController,
    ResepController,
    PembelianController,
    PenjualanController,
    LaporanController,
    DashboardController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. LANDING PAGE (Welcome)
// Ini biar gak langsung redirect ke login. Pastikan ada file resources/views/welcome.blade.php
Route::get('/', function () {
    return view('welcome');
});

// 2. DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// 3. GROUPING AUTH (Wajib Login)
Route::middleware('auth')->group(function () {
    
    // === MASTER DATA ===
    Route::resource('supplier', SupplierController::class);
    Route::post('/supplier/quick-add', [SupplierController::class, 'quick_store'])->name('supplier.quick_store');
    
    Route::resource('customer', CustomerController::class);
    Route::post('/customer/quick-add', [CustomerController::class, 'storeCustomerAjax'])->name('customer.quick_store');
    
    Route::resource('bahan-baku', BahanBakuController::class);
    
    Route::resource('produk', ProdukController::class);
    Route::get('produk-stok-minim', [ProdukController::class, 'stokMinim'])->name('produk.stok-minim');

    // === MANAGEMENT RESEP ===
    Route::get('resep', [ResepController::class, 'index'])->name('resep.index');
    Route::get('produk/{id}/resep', [ResepController::class, 'edit'])->name('resep.edit');
    Route::post('produk/{id}/resep', [ResepController::class, 'store'])->name('resep.store');
    Route::delete('resep/{id}', [ResepController::class, 'destroy'])->name('resep.destroy');
    Route::get('/resep/{id_produk}/estimasi', [ResepController::class, 'showEstimasi'])->name('resep.estimasi');

    // === TRANSAKSI PEMBELIAN (BAHAN BAKU) ===
    Route::resource('pembelian', PembelianController::class);
    Route::get('/pembelian/{id}/print', [PembelianController::class, 'print'])->name('pembelian.print');
    Route::post('/pembelian/import', [PembelianController::class, 'import'])->name('pembelian.import');

    // === TRANSAKSI PENJUALAN (PRODUK JADI) ===
    Route::resource('penjualan', PenjualanController::class);
    Route::get('/penjualan/{id}/print', [PenjualanController::class, 'print'])->name('penjualan.print');
    Route::post('/penjualan/import', [PenjualanController::class, 'import'])->name('penjualan.import');

    // === LAPORAN ===
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

    // === USER PROFILE (Laravel Breeze) ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// AUTH SYSTEM (Breeze)
require __DIR__.'/auth.php';

// DEBUG TOOL
Route::get('/cek-php', function() {
    phpinfo();
});