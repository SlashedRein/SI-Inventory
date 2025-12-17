<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Penjualan (Bulan ini)
        $total_penjualan = Penjualan::whereYear('tgl_penjualan', date('Y'))
                                    ->whereMonth('tgl_penjualan', date('m'))
                                    ->sum('total');

        // Total Produk
        $total_produk = Produk::count();

        // Total Customer
        $total_customer = Customer::count();

        // Produk dengan stok minim (asumsi stok < 10)
        $stok_minim_count = Produk::where('stok', '<', 10)->count();

        return view('dashboard', compact(
            'total_penjualan',
            'total_produk',
            'total_customer',
            'stok_minim_count'
        ));
    }
}
