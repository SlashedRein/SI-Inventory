<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Produk;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResepController2 extends Controller
{
    // Tampilkan daftar resep (semua produk dengan resepnya)
    public function index()
    {
        $produks = Produk::with('resep.bahan')->get();
        return view('resep.index', compact('produks'));
    }

    // Edit resep untuk satu produk (lihat dan tambah bahan)
    public function edit($id_produk)
    {
        $produk = Produk::with('resep.bahan')->findOrFail($id_produk);
        $bahans = BahanBaku::all();
        return view('resep.edit', compact('produk', 'bahans'));
    }

    // Simpan bahan ke resep (hanya owner)
    public function store(Request $request, $id_produk)
    {
        if (!Auth::user() || Auth::user()->role !== 'owner') {
            abort(403);
        }

        $request->validate([
            'id_bahan' => 'required',
            'takaran' => 'required|integer',
            'satuan' => 'required'
        ]);

        // Cek duplikasi bahan
        if (Resep::where('id_produk', $id_produk)->where('id_bahan', $request->id_bahan)->exists()) {
            return back()->with('error', 'Bahan sudah ada!');
        }

        Resep::create([
            'id_produk' => $id_produk,
            'id_bahan' => $request->id_bahan,
            'takaran' => $request->takaran,
            'satuan' => $request->satuan
        ]);

        return back()->with('success', 'Bahan ditambahkan');
    }

    // Hapus bahan resep (hanya owner)
    public function destroy($id)
    {
        if (!Auth::user() || Auth::user()->role !== 'owner') {
            abort(403);
        }

        Resep::findOrFail($id)->delete();
        return back()->with('success', 'Bahan dihapus dari resep');
    }
}
