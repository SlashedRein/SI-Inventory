<?php
namespace App\Http\Controllers;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller {
    public function index() {
        $produks = Produk::all();
        return view('produk.index', compact('produks'));
    }
    public function create() {
        return view('produk.create');
    }
    public function store(Request $request) {
        $request->validate([
            'nama_produk' => 'required', 'harga_jual' => 'required|numeric', 'stok' => 'required|integer', 'satuan' => 'required'
        ]);
        Produk::create($request->all());
        return redirect()->route('produk.index')->with('success', 'Produk Disimpan');
    }
    public function edit($id) {
        $produk = Produk::findOrFail($id);
        return view('produk.edit', compact('produk'));
    }
    public function update(Request $request, $id) {
        $produk = Produk::findOrFail($id);
        $produk->update($request->all());
        return redirect()->route('produk.index')->with('success', 'Produk Diupdate');
    }
    public function destroy($id) {
        Produk::findOrFail($id)->delete();
        return redirect()->route('produk.index')->with('success', 'Produk Dihapus');
    }

    public function stokMinim() {
        $produks = Produk::where('stok', '<', 10)
                         ->orderBy('stok', 'asc')
                         ->get();
        return view('produk.stok-minim', compact('produks'));
    }
}