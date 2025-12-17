<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Customer; // Pastikan panggil model Customer

class TransaksiController extends Controller
{
    // 1. Menampilkan Form Input
    public function create()
    {
        // Ambil semua data customer buat ditaruh di Dropdown (Select Option)
        $customers = Customer::orderBy('nama_pelanggan', 'asc')->get();
        
        return view('transaksi.create', compact('customers'));
    }

    // 2. Proses Simpan Transaksi ke Database
    public function store(Request $request)
    {
        // Validasi input biar gak kosong
        $request->validate([
            'tgl_transaksi' => 'required|date',
            'id_pelanggan'  => 'required|exists:customers,id_pelanggan', // Pastikan ID ada di tabel customers
            'total_bayar'   => 'required|numeric|min:0',
        ]);

        $transaksi = new Transaksi();
        $transaksi->tgl_transaksi = $request->tgl_transaksi;
        $transaksi->id_pelanggan  = $request->id_pelanggan;
        $transaksi->total_bayar   = $request->total_bayar;
        $transaksi->save();

        // Tetap di Halaman Tambah (Refresh halaman ini) biar bisa input lagi
        return redirect()->back()->with('success', 'Data berhasil disimpan! Silakan input data selanjutnya. ✅');
    }

    // 3. (TAMBAHAN AJAX) Proses Simpan Customer Baru via Popup
    public function storeCustomerAjax(Request $request)
    {
        // Validasi nama
        $request->validate([
            'nama_pelanggan_baru' => 'required|string|max:255'
        ]);

        // Simpan ke database
        $customer = new Customer();
        // Sesuaikan field dengan tabel `customers` di project ini
        // Tabel menggunakan `id_cust` dan `nama`
        $customer->nama = $request->nama_pelanggan_baru;
        $customer->alamat = $request->alamat ?? '-';
        $customer->no_telp = $request->no_telp ?? '-';
        // $customer->alamat = '-'; // Opsional: kalau ada kolom lain yang wajib, kasih default dulu
        $customer->save();

        // Balikin respon JSON biar bisa dibaca Javascript di modal
        return response()->json([
            'success' => true,
            'id_cust' => $customer->id_cust,
            'nama' => $customer->nama
        ]);
    }
}