<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Customer;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('tgl_awal', date('Y-m-01'));
        $endDate   = $request->input('tgl_akhir', date('Y-m-d'));
        $tipe      = $request->input('tipe', 'semua');

        $query = Penjualan::with('customer')
                    ->whereBetween('tgl_penjualan', [$startDate, $endDate]);

        if ($tipe == 'tetap') {
            $query->whereHas('customer', function($q) {
                $q->where('nama', 'LIKE', '%PT%')
                  ->orWhere('nama', 'LIKE', '%CV%')
                  ->orWhere('nama', 'LIKE', '%UD%');
            });
        } elseif ($tipe == 'biasa') {
            $query->whereHas('customer', function($q) {
                $q->where('nama', 'NOT LIKE', '%PT%')
                  ->where('nama', 'NOT LIKE', '%CV%')
                  ->where('nama', 'NOT LIKE', '%UD%');
            });
        }

        $transaksi = $query->orderBy('tgl_penjualan', 'desc')->get();

        return view('laporan.index', compact('transaksi', 'startDate', 'endDate', 'tipe'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('tgl_awal', date('Y-m-01'));
        $endDate   = $request->input('tgl_akhir', date('Y-m-d'));
        $tipe      = $request->input('tipe', 'semua');

        $query = Penjualan::with('customer')
                    ->whereBetween('tgl_penjualan', [$startDate, $endDate]);

        if ($tipe == 'tetap') {
            $query->whereHas('customer', function($q) {
                $q->where('nama', 'LIKE', '%PT%')
                  ->orWhere('nama', 'LIKE', '%CV%')
                  ->orWhere('nama', 'LIKE', '%UD%');
            });
        } elseif ($tipe == 'biasa') {
            $query->whereHas('customer', function($q) {
                $q->where('nama', 'NOT LIKE', '%PT%')
                  ->where('nama', 'NOT LIKE', '%CV%')
                  ->where('nama', 'NOT LIKE', '%UD%');
            });
        }

        $transaksi = $query->get();

        return view('laporan.excel', compact('transaksi', 'startDate', 'endDate'));
    }
}