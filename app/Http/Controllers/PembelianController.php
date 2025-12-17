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
        $pembelians = Pembelian::with(['supplier', 'detail.bahan', 'user'])->latest()->get();
        return view('pembelian.index', compact('pembelians'));
    }
    
    public function create() {
        $suppliers = Supplier::all();
        $bahans = BahanBaku::all();
        return view('pembelian.create', compact('suppliers', 'bahans'));
    }
    
    public function store(Request $request) {
        $request->validate([
            'id_supp' => 'required|exists:suppliers,id_supp',
            'tgl' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_bahan' => 'required|exists:bahan_bakus,id_bahan',
                'items.*.jumlah' => 'nullable|numeric|min:0',
            'items.*.harga_satuan' => 'required|numeric|min:0'
        ]);
        
        try {
            DB::transaction(function () use ($request) {
                $pembelian = Pembelian::create([
                    'id_supp' => $request->id_supp,
                    'tgl' => $request->tgl,
                    'user_id' => Auth::id(),
                    'note' => $request->note ?? null,
                    'total_beli' => 0
                ]);
                
                $total = 0;
                foreach ($request->items as $item) {
                    // Normalize input: support two input modes (pack-based or base-unit)
                    $idBahan = $item['id_bahan'];
                    $qtyBase = 0; // quantity in base unit (integer)
                    $pricePerBase = 0; // harga per base unit
                    $subtotal = 0;

                    if (!empty($item['qty_pack']) && !empty($item['isi_pack'])) {
                        // pack-based input
                        $qtyPack = (float) $item['qty_pack'];
                        $isiPack = (float) $item['isi_pack'];
                        $hargaPack = isset($item['harga_pack']) ? (float) $item['harga_pack'] : 0;

                        $subtotal = $qtyPack * $hargaPack;
                        $qtyBase = (int) round($qtyPack * $isiPack);
                        $pricePerBase = $qtyBase > 0 ? ($subtotal / $qtyBase) : 0;
                    } elseif (!empty($item['jumlah']) && isset($item['harga_satuan'])) {
                        // already base-unit inputs
                        $qtyBase = (int) round($item['jumlah']);
                        $pricePerBase = (float) $item['harga_satuan'];
                        $subtotal = $qtyBase * $pricePerBase;
                    } else {
                        // skip invalid/empty item
                        continue;
                    }

                    if ($qtyBase <= 0) continue;

                    $total += $subtotal;

                    DetailPembelian::create([
                        'id_beli' => $pembelian->id_beli,
                        'id_bahan' => $idBahan,
                        'jumlah' => $qtyBase,
                        'harga_satuan' => $pricePerBase,
                        'sub_total' => $subtotal
                    ]);

                    // Tambah Stok Bahan (qtyBase is in base unit)
                    BahanBaku::where('id_bahan', $idBahan)->increment('stok', $qtyBase);
                }
                $pembelian->update(['total_beli' => $total]);
            });
            
            return redirect()->route('pembelian.index')->with('success', 'Pembelian Berhasil Disimpan!');
            
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function show($id){
        $pembelian = Pembelian::with(['detail.bahan', 'supplier', 'user'])->findOrFail($id);
        return view('pembelian.show', compact('pembelian'));
    }
    
    public function destroy($id) {
        try {
            DB::transaction(function () use ($id) {
                $pembelian = Pembelian::findOrFail($id);
                
                // Reverse stok untuk setiap item
                foreach ($pembelian->detail as $detail) {
                    BahanBaku::where('id_bahan', $detail->id_bahan)->decrement('stok', $detail->jumlah);
                }
                
                $pembelian->delete();
            });
            
            return redirect()->route('pembelian.index')->with('success', 'Pembelian Berhasil Dihapus!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Print-friendly view
     */
    public function print($id) {
        $pembelian = Pembelian::with(['detail.bahan', 'supplier', 'user'])->findOrFail($id);
        
        return view('pembelian.print', compact('pembelian'));
    }

    /**
     * Import pembelian dari Excel/CSV
     */
    public function import(Request $request) {
        // TODO: Implement Excel/CSV import for pembelian
        // For now, redirect back with info message
        return redirect()->route('pembelian.index')->with('info', 'Import functionality coming soon!');
    }
}