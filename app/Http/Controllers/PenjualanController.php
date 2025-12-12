<?php
namespace App\Http\Controllers;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller {
    public function index() {
        $penjualans = Penjualan::with('customer')->latest()->get();
        return view('penjualan.index', compact('penjualans'));
    }
    public function create() {
        $customers = Customer::all();
        $produks = Produk::where('stok', '>', 0)->get();
        return view('penjualan.create', compact('customers', 'produks'));
    }
    public function store(Request $request) {
        $request->validate([
            'tgl_penjualan' => 'required|date', 'items' => 'required|array'
        ]);
        
        try {
            DB::transaction(function () use ($request) {
                $penjualan = Penjualan::create([
                    'id_cust' => $request->id_cust,
                    'tgl_penjualan' => $request->tgl_penjualan,
                    'user_id' => Auth::id(),
                    'total' => 0
                ]);
                
                $total = 0;
                foreach ($request->items as $item) {
                    $produk = Produk::findOrFail($item['id_produk']);
                    
                    if($produk->stok < $item['jumlah']) {
                        throw new \Exception("Stok {$produk->nama_produk} kurang!");
                    }
                    
                    $sub = $item['jumlah'] * $produk->harga_jual;
                    $total += $sub;
                    
                    DetailPenjualan::create([
                        'id_penjualan' => $penjualan->id_penjualan,
                        'id_produk' => $item['id_produk'],
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $produk->harga_jual,
                        'sub_total' => $sub
                    ]);
                    
                    // Kurangi Stok Produk
                    $produk->decrement('stok', $item['jumlah']);
                }
                $penjualan->update(['total' => $total]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        
        return redirect()->route('penjualan.index')->with('success', 'Penjualan Berhasil');
    }
    public function show($id){
        $penjualan = Penjualan::with(['detail.produk', 'customer'])->findOrFail($id);
        return view('penjualan.show', compact('penjualan'));
    }
}