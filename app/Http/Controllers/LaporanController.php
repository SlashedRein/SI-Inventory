<?php
namespace App\Http\Controllers;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class LaporanController extends Controller {
    public function index() {
        $total_penjualan = Penjualan::sum('total');
        $total_pembelian = Pembelian::sum('total_beli');
        $transaksi_terakhir = Penjualan::latest()->take(5)->get();
        
        return view('laporan.index', compact('total_penjualan', 'total_pembelian', 'transaksi_terakhir'));
    }
}