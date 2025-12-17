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
        // 1. Validasi File
        $request->validate([
            'file_excel' => 'required|file'
        ]);

        $file = $request->file('file_excel');
        $path = $file->getRealPath();

        // 2. Deteksi Separator Otomatis
        $handle = fopen($path, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';

        // 3. Baca Data CSV
        $data = [];
        $rowNumber = 0;
        
        if (($handle = fopen($path, "r")) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $rowNumber++;
                
                // SKIP jika baris kosong atau jumlah kolom kurang dari 5
                if(count($row) < 5) continue; 

                // SKIP BARIS JUDUL (Kalau kolom pertama isinya "no_transaksi" atau "tanggal")
                if ($rowNumber == 1 && (strtolower(trim($row[0])) == 'no_transaksi' || strtolower(trim($row[0])) == 'tanggal')) {
                    continue;
                }

                // Masukkan ke array
                $data[] = [
                    'no_transaksi' => trim($row[0]),
                    'tanggal'      => trim($row[1]),
                    'customer'     => trim($row[2]),
                    'nama_produk'  => trim($row[3]),
                    'qty'          => (int) trim($row[4]),
                ];
            }
            fclose($handle);
        }

        if (empty($data)) {
            return back()->with('error', 'File CSV kosong atau format header salah!');
        }

        // 4. Proses Insert ke Database
        DB::beginTransaction();
        try {
            $groups = collect($data)->groupBy('no_transaksi');
            $suksesCount = 0;
            $errorLog = [];

            foreach ($groups as $no_transaksi => $items) {
                $firstRow = $items->first();
                
                // Buat Customer (Default 'Umum' kalau kosong)
                $namaCust = $firstRow['customer'] ?: 'Umum';
                $customer = Customer::firstOrCreate(
                    ['nama' => $namaCust],
                    ['alamat' => '-', 'no_telp' => '-']
                );

                // Parsing Tanggal
                try {
                    $tgl = \Carbon\Carbon::parse($firstRow['tanggal']);
                } catch (\Exception $e) {
                    $tgl = now();
                }

                // Cek Produk
                $grandTotal = 0;
                $details = [];

                foreach ($items as $item) {
                    // Cari produk MIRIP (Case Insensitive)
                    $produk = Produk::where('nama_produk', 'LIKE', '%' . $item['nama_produk'] . '%')->first();

                    if ($produk) {
                        $subtotal = $produk->harga_jual * $item['qty'];
                        $grandTotal += $subtotal;
                        
                        $details[] = [
                            'produk' => $produk,
                            'qty' => $item['qty'],
                            'harga_satuan' => $produk->harga_jual,
                            'subtotal' => $subtotal
                        ];
                    } else {
                        // Catat error kalau produk gak ketemu
                        $errorLog[] = "Produk '" . $item['nama_produk'] . "' tidak ditemukan di database.";
                    }
                }

                if ($grandTotal == 0) continue; // Skip struk kalau isinya kosong

                // Simpan Header Penjualan (sesuai struktur tabel)
                $penjualan = Penjualan::create([
                    'tgl_penjualan' => $tgl,
                    'total' => $grandTotal,
                    'user_id' => Auth::id() ?? 1,
                    'id_cust' => $customer->id_cust,
                ]);

                // Simpan Detail & Potong Stok
                foreach ($details as $d) {
                    DetailPenjualan::create([
                        'id_penjualan' => $penjualan->id_penjualan,
                        'id_produk' => $d['produk']->id_produk,
                        'jumlah' => $d['qty'],
                        'harga_satuan' => $d['harga_satuan'],
                        'sub_total' => $d['subtotal']
                    ]);
                    
                    $d['produk']->decrement('stok', $d['qty']);
                }
                $suksesCount++;
            }

            DB::commit();

            // Cek apakah ada error produk
            if (count($errorLog) > 0) {
                // Tampilkan sukses TAPI ada catatan
                $pesanError = implode(" | ", array_unique($errorLog));
                return redirect()->route('penjualan.index')
                    ->with('success', "$suksesCount Transaksi berhasil! TAPI ada produk skip: $pesanError");
            }

            return redirect()->route('penjualan.index')
                ->with('success', "Sukses! $suksesCount Transaksi berhasil diimport.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error Database: ' . $e->getMessage());
        }
    }
}