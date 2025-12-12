<?php
namespace App\Http\Controllers;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller {
    public function index() {
        $bahans = BahanBaku::all();
        return view('bahan_baku.index', compact('bahans'));
    }
    public function create() {
        return view('bahan_baku.create');
    }
    public function store(Request $request) {
        $request->validate([
            'nama_bahan' => 'required', 'stok' => 'required|integer', 'stok_min' => 'required|integer', 'satuan' => 'required'
        ]);
        BahanBaku::create($request->all());
        return redirect()->route('bahan-baku.index')->with('success', 'Bahan Baku Disimpan');
    }
    public function edit($id) {
        $bahan = BahanBaku::findOrFail($id);
        return view('bahan_baku.edit', compact('bahan'));
    }
    public function update(Request $request, $id) {
        $bahan = BahanBaku::findOrFail($id);
        $bahan->update($request->all());
        return redirect()->route('bahan-baku.index')->with('success', 'Bahan Baku Diupdate');
    }
    public function destroy($id) {
        BahanBaku::findOrFail($id)->delete();
        return redirect()->route('bahan-baku.index')->with('success', 'Bahan Baku Dihapus');
    }
}