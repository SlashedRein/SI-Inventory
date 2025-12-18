<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Produk;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResepController extends Controller
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

    // Lihat detail resep + estimasi produksi
    public function showEstimasi($id_produk)
    {
        $produk = Produk::with('resep.bahan')->findOrFail($id_produk);

        $estimasi = null;
        $detailEstimasi = [];

        foreach ($produk->resep as $r) {
            $stokBahan = $r->bahan->stok;       // stok base unit (gram/ml/pcs)
            $takaran   = $r->takaran;            // kebutuhan per 1 produk

            if ($takaran <= 0) {
                continue;
            }

            $bisaProduksi = intdiv($stokBahan, $takaran);

            $detailEstimasi[] = [
                'nama_bahan' => $r->bahan->nama_bahan,
                'stok' => $stokBahan,
                'takaran' => $takaran,
                'satuan' => $r->satuan,
                'maks_produk' => $bisaProduksi
            ];

            if ($estimasi === null || $bisaProduksi < $estimasi) {
                $estimasi = $bisaProduksi;
            }
        }

        return view('resep.estimasi', compact(
            'produk',
            'estimasi',
            'detailEstimasi'
        ));
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
