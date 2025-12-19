<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualans = Penjualan::with(['customer', 'detail.produk', 'user'])->latest()->get();
        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $customers = Customer::all();
        $produks = Produk::where('stok', '>', 0)->get();
        return view('penjualan.create', compact('customers', 'produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cust' => 'required|exists:customers,id_cust',
            'tgl_penjualan' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id_produk',
            'items.*.jumlah' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::create([
                'id_cust' => $request->id_cust,
                'tgl_penjualan' => $request->tgl_penjualan,
                'user_id' => Auth::id(),
                'total' => 0
            ]);

            $grandTotal = 0;
            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['id_produk']);
                if ($produk->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$produk->nama_produk} tidak cukup!");
                }

                $subtotal = $item['jumlah'] * $produk->harga_jual;
                $grandTotal += $subtotal;

                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_produk'    => $item['id_produk'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $produk->harga_jual,
                    'sub_total'    => $subtotal
                ]);

                $produk->decrement('stok', $item['jumlah']);
            }

            $penjualan->update(['total' => $grandTotal]);
            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $penjualan = Penjualan::with(['detail.produk', 'customer'])->findOrFail($id);
        $customers = Customer::all();
        $produks   = Produk::all();
        return view('penjualan.edit', compact('penjualan', 'customers', 'produks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_cust' => 'required|exists:customers,id_cust',
            'tgl_penjualan' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::with('detail')->findOrFail($id);

            // Revert stok lama sebelum update
            foreach ($penjualan->detail as $oldDetail) {
                Produk::where('id_produk', $oldDetail->id_produk)->increment('stok', $oldDetail->jumlah);
            }
            $penjualan->detail()->delete();

            $grandTotal = 0;
            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['id_produk']);
                if ($produk->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$produk->nama_produk} kurang!");
                }

                $subtotal = $item['jumlah'] * $produk->harga_jual;
                $grandTotal += $subtotal;

                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_produk'    => $item['id_produk'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $produk->harga_jual,
                    'sub_total'    => $subtotal
                ]);

                $produk->decrement('stok', $item['jumlah']);
            }

            $penjualan->update([
                'id_cust' => $request->id_cust,
                'tgl_penjualan' => $request->tgl_penjualan,
                'total' => $grandTotal
            ]);

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Penjualan diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $penjualan = Penjualan::with('detail')->findOrFail($id);
            foreach ($penjualan->detail as $detail) {
                Produk::where('id_produk', $detail->id_produk)->increment('stok', $detail->jumlah);
            }
            $penjualan->delete();
            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Transaksi dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file_excel' => 'required|file']);

        try {
            $sheets = Excel::toArray([], $request->file('file_excel'));
            $rows = $sheets[0] ?? [];
            if (count($rows) <= 1) return back()->with('error', 'File Excel kosong.');

            DB::beginTransaction();
            $sukses = 0;
            $errorLog = [];
            $produkList = Produk::all();

            foreach ($rows as $i => $row) {
                if ($i === 0 || (empty($row[0]) && empty($row[3]))) continue;

                try {
                    // Mapping data & Konversi Tanggal
                    $tglRaw = $row[1];
                    $tanggal = is_numeric($tglRaw) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tglRaw)->format('Y-m-d') : date('Y-m-d', strtotime($tglRaw));
                    $namaCust = trim($row[2] ?? 'Umum');
                    $inputProduk = trim($row[3]);
                    $qty = (int) ($row[4] ?? 0);

                    // Cari Produk pake Nama (Normalisasi string)
                    $namaExcel = $this->normalizeString($inputProduk);
                    $produk = $produkList->first(function ($p) use ($namaExcel) {
                        return $this->normalizeString($p->nama_produk) === $namaExcel;
                    });

                    if (!$produk) {
                        $errorLog[] = "Baris ".($i+1).": Produk '{$inputProduk}' gak ada di DB.";
                        continue;
                    }

                    // Cek stok real-time di DB
                    $dbProduk = Produk::find($produk->id_produk);
                    if ($dbProduk->stok < $qty) {
                        $errorLog[] = "Baris ".($i+1).": Stok '{$produk->nama_produk}' kurang (Sisa: {$dbProduk->stok}).";
                        continue;
                    }

                    // Customer & Transaksi (Setiap baris = 1 Transaksi)
                    $customer = Customer::firstOrCreate(['nama' => $namaCust], ['alamat' => '-', 'no_telp' => '-']);
                    $subtotal = $qty * $produk->harga_jual;

                    $penjualan = Penjualan::create([
                        'id_cust' => $customer->id_cust,
                        'tgl_penjualan' => $tanggal,
                        'user_id' => Auth::id() ?? 1,
                        'total' => $subtotal
                    ]);

                    DetailPenjualan::create([
                        'id_penjualan' => $penjualan->id_penjualan,
                        'id_produk'    => $produk->id_produk,
                        'jumlah'       => $qty,
                        'harga_satuan' => $produk->harga_jual,
                        'sub_total'    => $subtotal
                    ]);

                    $dbProduk->decrement('stok', $qty);
                    $sukses++;

                } catch (\Exception $e) {
                    $errorLog[] = "Baris ".($i+1).": " . $e->getMessage();
                }
            }

            DB::commit();
            $msg = "Berhasil import {$sukses} transaksi.";
            return redirect()->route('penjualan.index')->with('success', $msg . (count($errorLog) > 0 ? " Catatan: " . implode(' | ', $errorLog) : ""));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    private function normalizeString($str) {
        return strtolower(preg_replace('/[^a-z0-9]/', '', $str));
    }

    public function print($id) {
        $penjualan = Penjualan::with(['detail.produk', 'customer', 'user'])->findOrFail($id);
        
        // Deteksi Tipe Pelanggan untuk milih Blade Struk
        $namaPelanggan = optional($penjualan->customer)->nama ?? '';
        $isCorporate = preg_match('/(PT|CV|UD|Toko)/i', $namaPelanggan);

        if ($isCorporate) {
            return view('penjualan.print-corporate', compact('penjualan'));
        }
        
        return view('penjualan.print-personal', compact('penjualan'));
    }
}