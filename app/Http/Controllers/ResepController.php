<?php
namespace App\Http\Controllers;
use App\Models\Resep;
use App\Models\Produk;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class ResepController extends Controller {
    public function edit($id_produk) {
        $produk = Produk::with('resep.bahan')->findOrFail($id_produk);
        $bahans = BahanBaku::all();
        return view('resep.edit', compact('produk', 'bahans'));
    }
    public function store(Request $request, $id_produk) {
        $request->validate([
            'id_bahan' => 'required', 'takaran' => 'required|integer', 'satuan' => 'required'
        ]);
        // Cek duplikasi bahan
        if(Resep::where('id_produk', $id_produk)->where('id_bahan', $request->id_bahan)->exists()){
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
    public function destroy($id) {
        Resep::findOrFail($id)->delete();
        return back()->with('success', 'Bahan dihapus dari resep');
    }
}