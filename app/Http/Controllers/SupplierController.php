<?php
namespace App\Http\Controllers;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller {
    public function index() {
        $suppliers = Supplier::all();
        return view('supplier.index', compact('suppliers'));
    }
    public function create() {
        return view('supplier.create');
    }
    public function store(Request $request) {
        $request->validate([
            'nama' => 'required', 'alamat' => 'required', 'no_telp' => 'required',
        ]);
        Supplier::create($request->all());
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan');
    }
    public function edit($id) {
        $supplier = Supplier::findOrFail($id);
        return view('supplier.edit', compact('supplier'));
    }
    public function update(Request $request, $id) {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());
        return redirect()->route('supplier.index')->with('success', 'Supplier diupdate');
    }
    public function destroy($id) {
        Supplier::findOrFail($id)->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier dihapus');
    }
}