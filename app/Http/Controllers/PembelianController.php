<?php
namespace App\Http\Controllers;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\BahanBaku;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller {
    public function index() {
        $pembelians = Pembelian::with('supplier')->latest()->get();
        return view('pembelian.index', compact('pembelians'));
    }
    public function create() {
        $suppliers = Supplier::all();
        $bahans = BahanBaku::all();
        return view('pembelian.create', compact('suppliers', 'bahans'));
    }
    public function store(Request $request) {
        $request->validate([
            'id_supp' => 'required', 'tgl' => 'required|date', 'items' => 'required|array'
        ]);
        
        DB::transaction(function () use ($request) {
            $pembelian = Pembelian::create([
                'id_supp' => $request->id_supp,
                'tgl' => $request->tgl,
                'user_id' => Auth::id(),
                'note' => $request->note,
                'total_beli' => 0
            ]);
            
            $total = 0;
            foreach ($request->items as $item) {
                $sub = $item['jumlah'] * $item['harga_satuan'];
                $total += $sub;
                
                DetailPembelian::create([
                    'id_beli' => $pembelian->id_beli,
                    'id_bahan' => $item['id_bahan'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'sub_total' => $sub
                ]);
                
                // Tambah Stok Bahan
                BahanBaku::where('id_bahan', $item['id_bahan'])->increment('stok', $item['jumlah']);
            }
            $pembelian->update(['total_beli' => $total]);
        });
        return redirect()->route('pembelian.index')->with('success', 'Pembelian Sukses');
    }
    public function show($id){
        $pembelian = Pembelian::with(['detail.bahan', 'supplier'])->findOrFail($id);
        return view('pembelian.show', compact('pembelian'));
    }
}