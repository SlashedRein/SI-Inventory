<?php
namespace App\Http\Controllers;
use App\Imports\PenjualanImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller {
    public function index() {
        $penjualans = Penjualan::with(['customer', 'detail.produk', 'user'])->latest()->get();
        return view('penjualan.index', compact('penjualans'));
    }
    public function create() {
        $customers = Customer::all();
        $produks = Produk::where('stok', '>', 0)->get();
        return view('penjualan.create', compact('customers', 'produks'));
    }

    public function edit($id)
    {
        $penjualan = Penjualan::with(['detail.produk', 'customer'])
            ->findOrFail($id);

        $customers = Customer::all();
        $produks   = Produk::all();

        return view('penjualan.edit', compact(
            'penjualan',
            'customers',
            'produks'
        ));
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {

                $penjualan = Penjualan::with('detail.produk')
                    ->findOrFail($id);

                // 1. Kembalikan stok produk
                foreach ($penjualan->detail as $detail) {
                    if ($detail->produk) {
                        $detail->produk->increment('stok', $detail->jumlah);
                    }
                }

                // 2. Hapus detail penjualan
                DetailPenjualan::where('id_penjualan', $penjualan->id_penjualan)->delete();

                // 3. Hapus header penjualan
                $penjualan->delete();
            });

            return redirect()
                ->route('penjualan.index')
                ->with('success', 'Transaksi penjualan berhasil dihapus & stok dikembalikan.');

        } catch (\Exception $e) {
            return back()->with(
                'error',
                'Gagal menghapus transaksi: ' . $e->getMessage()
            );
        }
    }


    public function store(Request $request) {
        $request->validate([
            'id_cust' => 'required|exists:customers,id_cust',
            'tgl_penjualan' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id_produk',
            'items.*.jumlah' => 'required|integer|min:1'
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
            
            return redirect()->route('penjualan.index')->with('success', 'Penjualan Berhasil Disimpan!');
            
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function show($id){
        $penjualan = Penjualan::with(['detail.produk', 'customer'])->findOrFail($id);
        
        // Deteksi tipe pelanggan: PT/CV = Corporate, lainnya = Personal
        $isCorporate = preg_match('/(PT|CV|UD|Toko)/i', $penjualan->customer->nama) ? true : false;
        
        return view('penjualan.show', compact('penjualan', 'isCorporate'));
    }

    /**
     * Print-friendly view for a single penjualan (opens in new tab).
     */
    public function print($id)
    {
        $penjualan = Penjualan::with(['detail.produk', 'customer', 'user'])->findOrFail($id);

        // If customer looks corporate, show corporate print layout; otherwise show personal print
        $isCorporate = preg_match('/(PT|CV|UD|Toko)/i', optional($penjualan->customer)->nama ?? '') ? true : false;

        if ($isCorporate) {
            return view('penjualan.print-corporate', compact('penjualan'));
        }

        return view('penjualan.print-personal', compact('penjualan'));
    }
    
   public function import(Request $request)
{
    $request->validate([
        'file_excel' => 'required|file'
    ]);

    $file = $request->file('file_excel');
    $ext  = strtolower($file->getClientOriginalExtension());
    $data = [];

    /* =======================
     * 1. BACA FILE
     * ======================= */
    if (in_array($ext, ['xls', 'xlsx'])) {
        $sheets = Excel::toArray([], $file);
        $rows = $sheets[0] ?? [];

        foreach ($rows as $i => $row) {
            if ($i === 0) continue;
            if (!is_array($row) || count($row) < 5) continue;

            $data[] = [
                'no_transaksi' => trim($row[0]),
                'tanggal'      => trim($row[1]),
                'customer'     => trim($row[2]),
                'nama_produk'  => trim($row[3]),
                'qty'          => (int) $row[4],
            ];
        }
    } else {
        $path = $file->getRealPath();
        if (($handle = fopen($path, 'r')) !== false) {
            $i = 0;
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $i++;
                if ($i === 1 || count($row) < 5) continue;

                $data[] = [
                    'no_transaksi' => trim($row[0]),
                    'tanggal'      => trim($row[1]),
                    'customer'     => trim($row[2]),
                    'nama_produk'  => trim($row[3]),
                    'qty'          => (int) $row[4],
                ];
            }
            fclose($handle);
        }
    }

    if (empty($data)) {
        return back()->with('error', 'File kosong atau format salah.');
    }

    /* =======================
     * 2. PROSES IMPORT
     * ======================= */
    DB::beginTransaction();
    try {
        $groups = collect($data)->groupBy('no_transaksi');
        $sukses = 0;
        $errorLog = [];

        // Ambil semua produk SEKALI (biar kenceng)
        $produkList = Produk::all();

        foreach ($groups as $no_transaksi => $items) {

            // Parse tanggal
            try {
                $tgl = \Carbon\Carbon::parse($items->first()['tanggal']);
            } catch (\Exception $e) {
                $tgl = now();
            }

            $details = [];
            $grandTotal = 0;

            foreach ($items as $item) {

                if ($item['qty'] <= 0) {
                    $errorLog[] = "Transaksi {$no_transaksi}: qty produk '{$item['nama_produk']}' tidak valid.";
                    continue;
                }

                $namaExcel = $this->normalizeProduk($item['nama_produk']);

                $produk = $produkList->first(function ($p) use ($namaExcel) {
                    $db = $this->normalizeProduk($p->nama_produk);
                    return $db === $namaExcel || str_contains($db, $namaExcel);
                });

                if (!$produk) {
                    $errorLog[] = "Transaksi {$no_transaksi}: produk '{$item['nama_produk']}' tidak ditemukan.";
                    continue;
                }

                if ($produk->stok < $item['qty']) {
                    $errorLog[] = "Transaksi {$no_transaksi}: stok '{$produk->nama_produk}' kurang.";
                    continue;
                }

                $subtotal = $produk->harga_jual * $item['qty'];
                $grandTotal += $subtotal;

                $details[] = [
                    'produk' => $produk,
                    'qty' => $item['qty'],
                    'harga' => $produk->harga_jual,
                    'subtotal' => $subtotal
                ];
            }

            // ❌ Kalau tidak ada produk valid → skip TANPA bikin customer
            if (count($details) === 0) {
                $errorLog[] = "Transaksi {$no_transaksi}: tidak ada produk valid, transaksi di-skip.";
                continue;
            }

            // ✅ Baru buat customer
            $namaCust = $items->first()['customer'] ?: 'Umum';
            $customer = Customer::firstOrCreate(
                ['nama' => $namaCust],
                ['alamat' => '-', 'no_telp' => '-']
            );

            // ✅ Simpan penjualan
            $penjualan = Penjualan::create([
                'tgl_penjualan' => $tgl,
                'total' => $grandTotal,
                'user_id' => Auth::id() ?? 1,
                'id_cust' => $customer->id_cust,
            ]);

            // ✅ Simpan detail penjualan
            foreach ($details as $d) {
                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_produk' => $d['produk']->id_produk,
                    'jumlah' => $d['qty'],
                    'harga_satuan' => $d['harga'],
                    'sub_total' => $d['subtotal'],
                ]);

                $d['produk']->decrement('stok', $d['qty']);
            }

            $sukses++;
        }

        DB::commit();

        $pesan = "Import selesai: {$sukses} transaksi berhasil.";
        if (!empty($errorLog)) {
            $pesan .= " Catatan: " . implode(' | ', array_unique($errorLog));
        }

        return redirect()->route('penjualan.index')->with('success', $pesan);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Import gagal: ' . $e->getMessage());
    }
}

    /* =======================
    * HELPER NORMALISASI
    * ======================= */
    private function normalizeProduk($nama)
    {
        return strtolower(
            preg_replace('/[^a-z0-9]/', '', $nama)
        );
    }

}