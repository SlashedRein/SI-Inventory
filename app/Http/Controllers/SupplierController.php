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

    /**
     * Quick add supplier via AJAX (untuk form pembelian)
     */
    public function quick_store(Request $request)
    {
        // Validasi nama
        $request->validate([
            'nama_supplier_baru' => 'required|string|max:255'
        ]);

        // Simpan ke database
        $supplier = new Supplier();
        $supplier->nama = $request->nama_supplier_baru;
        $supplier->alamat = '-';
        $supplier->no_telp = '-';
        $supplier->save();

        // Return JSON response
        return response()->json([
            'success' => true,
            'id_supp' => $supplier->id_supp,
            'nama_supplier' => $supplier->nama
        ]);
    }
}