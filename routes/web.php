<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
// Import Controller yang sudah kita buat
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard'); // <--- INI WAJIB ADA

// Grouping Middleware Auth (Harus Login Dulu)
Route::middleware('auth')->group(function () {
    
    // === MASTER DATA ===
    // Route::resource otomatis bikin nama: supplier.index, supplier.create, dst.
    Route::resource('supplier', SupplierController::class);
    Route::post('/supplier/quick-add', [SupplierController::class, 'quick_store'])->name('supplier.quick_store');
    Route::resource('customer', CustomerController::class);
    Route::resource('bahan-baku', BahanBakuController::class);
    Route::resource('produk', ProdukController::class);

    // === STOK MINIM ===
    Route::get('produk-stok-minim', [ProdukController::class, 'stokMinim'])->name('produk.stok-minim');

    // === RESEP (Khusus) ===
    Route::get('resep', [ResepController::class, 'index'])->name('resep.index');
    Route::get('produk/{id}/resep', [ResepController::class, 'edit'])->name('resep.edit');
    Route::post('produk/{id}/resep', [ResepController::class, 'store'])->name('resep.store');
    Route::delete('resep/{id}', [ResepController::class, 'destroy'])->name('resep.destroy');
    Route::get('/resep/{id_produk}/estimasi', [ResepController::class, 'showEstimasi'])
    ->name('resep.estimasi');


    // === TRANSAKSI ===
    Route::resource('pembelian', PembelianController::class);
    Route::resource('penjualan', PenjualanController::class);
    Route::get('/penjualan/{id}/edit', [PenjualanController::class, 'edit'])
    ->name('penjualan.edit');
    Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])
    ->name('penjualan.destroy');

    // Print/Print-friendly view for a single penjualan (opens in new tab)
    Route::get('/penjualan/{id}/print', [PenjualanController::class, 'print'])->name('penjualan.print');
    Route::get('/pembelian/{id}/print', [PembelianController::class, 'print'])->name('pembelian.print');

    // Halaman Laporan & Filter
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Action Download Excel
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

    // Route Tambah Penjualan
    Route::get('/transaksi/tambah', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi/simpan', [TransaksiController::class, 'store'])->name('transaksi.store');
    // Route untuk Quick Add Customer (Ajax)
    Route::post('/transaksi/customer-quick', [App\Http\Controllers\TransaksiController::class, 'storeCustomerAjax'])->name('customer.quick_store');

    // === PROFILE (Bawaan Breeze) ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/penjualan/import', [App\Http\Controllers\PenjualanController::class, 'import'])->name('penjualan.import');
    Route::post('/pembelian/import', [App\Http\Controllers\PembelianController::class, 'import'])->name('pembelian.import');
});


// Baris ini PENTING untuk login!
require __DIR__.'/auth.php';

Route::get('/cek-php', function() {
    phpinfo();
});