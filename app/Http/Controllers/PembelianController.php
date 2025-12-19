<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\BahanBaku;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelians = Pembelian::with(['supplier', 'detail.bahan', 'user'])
            ->latest()
            ->get();

        return view('pembelian.index', compact('pembelians'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $bahans    = BahanBaku::all();

        return view('pembelian.create', compact('suppliers', 'bahans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_supp' => 'required|exists:suppliers,id_supp',
            'tgl'     => 'required|date',
            'items'   => 'required|array|min:1',

            'items.*.id_bahan' => 'required|exists:bahan_bakus,id_bahan',

            // Mode pack
            'items.*.qty_pack'   => 'nullable|numeric|min:0',
            'items.*.isi_pack'   => 'nullable|numeric|min:0',
            'items.*.harga_pack' => 'nullable|numeric|min:0',

            // Mode base unit
            'items.*.jumlah'        => 'nullable|numeric|min:0',
            'items.*.harga_satuan'  => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {

                // ===== HEADER PEMBELIAN =====
                $pembelian = Pembelian::create([
                    'id_supp'    => $request->id_supp,
                    'tgl'        => $request->tgl,
                    'user_id'    => Auth::id(),
                    'note'       => $request->note ?? null,
                    'total_beli' => 0
                ]);

                $grandTotal = 0;

                foreach ($request->items as $item) {

                    $qtyBase      = 0; // stok masuk (gram/ml/pcs)
                    $hargaPerBase = 0;
                    $subtotal     = 0;

                    /**
                     * ===============================
                     * MODE 1: BELI PER PACK
                     * ===============================
                     */
                    if (
                        !empty($item['qty_pack']) &&
                        !empty($item['isi_pack']) &&
                        !empty($item['harga_pack'])
                    ) {
                        $qtyPack   = (float) $item['qty_pack'];
                        $isiPack   = (float) $item['isi_pack'];
                        $hargaPack = (float) $item['harga_pack'];

                        $qtyBase  = (int) round($qtyPack * $isiPack);
                        $subtotal = $qtyPack * $hargaPack;

                        $hargaPerBase = $qtyBase > 0
                            ? ($subtotal / $qtyBase)
                            : 0;
                    }

                    /**
                     * ===============================
                     * MODE 2: INPUT LANGSUNG BASE UNIT
                     * ===============================
                     */
                    elseif (
                        !empty($item['jumlah']) &&
                        !empty($item['harga_satuan'])
                    ) {
                        $qtyBase      = (int) round($item['jumlah']);
                        $hargaPerBase = (float) $item['harga_satuan'];
                        $subtotal     = $qtyBase * $hargaPerBase;
                    }

                    // Skip item tidak valid
                    if ($qtyBase <= 0 || $subtotal <= 0) {
                        continue;
                    }

                    // ===== DETAIL PEMBELIAN =====
                    DetailPembelian::create([
                        'id_beli'       => $pembelian->id_beli,
                        'id_bahan'      => $item['id_bahan'],
                        'jumlah'        => $qtyBase,
                        'harga_satuan'  => $hargaPerBase,
                        'sub_total'     => $subtotal,
                    ]);

                    // ===== TAMBAH STOK =====
                    BahanBaku::where('id_bahan', $item['id_bahan'])
                        ->increment('stok', $qtyBase);

                    $grandTotal += $subtotal;
                }

                // Update total pembelian
                $pembelian->update([
                    'total_beli' => $grandTotal
                ]);
            });

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Pembelian berhasil disimpan!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::with(['detail.bahan', 'supplier', 'user'])
            ->findOrFail($id);

        return view('pembelian.show', compact('pembelian'));
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {

                $pembelian = Pembelian::with('detail')->findOrFail($id);

                // Rollback stok
                foreach ($pembelian->detail as $detail) {
                    BahanBaku::where('id_bahan', $detail->id_bahan)
                        ->decrement('stok', $detail->jumlah);
                }

                $pembelian->delete();
            });

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Pembelian berhasil dihapus!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $pembelian = Pembelian::with(['detail.bahan', 'supplier', 'user'])
            ->findOrFail($id);

        return view('pembelian.print', compact('pembelian'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            // Mengambil data dari excel ke array
            // Gunakan library Maatwebsite Excel
            $data = \Excel::toArray([], $request->file('file_excel'))[0];
            
            // Hapus header excel (baris pertama) jika ada
            array_shift($data);

            DB::transaction(function () use ($data) {
                $lastPoNumber = null;
                $currentPembelian = null;

                foreach ($data as $row) {
                    // Mapping kolom excel (Sesuaikan index dengan file Anda)
                    // Index: 0=No_PO, 1=Tgl, 2=ID_Supp, 3=ID_Bahan, 4=Qty, 5=Harga_Satuan
                    $poNumber = $row[0]; 
                    $tanggal  = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]);
                    $idSupp   = $row[2];
                    $idBahan  = $row[3];
                    $jumlah   = $row[4];
                    $harga    = $row[5];
                    $subtotal = $jumlah * $harga;

                    // LOGIC GROUPING:
                    // Jika No_PO berbeda dengan baris sebelumnya, buat Header Pembelian baru
                    if ($poNumber !== $lastPoNumber) {
                        $currentPembelian = Pembelian::create([
                            'id_supp'    => $idSupp,
                            'tgl'        => $tanggal,
                            'user_id'    => Auth::id(),
                            'total_beli' => 0, // Akan diupdate setelah loop detail selesai
                            'note'       => 'Import Excel PO: ' . $poNumber
                        ]);
                        $lastPoNumber = $poNumber;
                    }

                    // Tambah Detail Pembelian
                    DetailPembelian::create([
                        'id_beli'      => $currentPembelian->id_beli,
                        'id_bahan'     => $idBahan,
                        'jumlah'       => $jumlah,
                        'harga_satuan' => $harga,
                        'sub_total'    => $subtotal,
                    ]);

                    // Update Stok Bahan Baku
                    BahanBaku::where('id_bahan', $idBahan)->increment('stok', $jumlah);

                    // Update Total di Header secara akumulatif
                    $currentPembelian->increment('total_beli', $subtotal);
                }
            });

            return redirect()->route('pembelian.index')->with('success', 'Import Berhasil! Data telah dikelompokkan berdasarkan nomor PO.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Ambil data pembelian beserta detail, supplier, dan daftar bahan baku
        $pembelian = Pembelian::with('detail.bahan')->findOrFail($id);
        $suppliers = Supplier::all();
        $bahans    = BahanBaku::all();

        return view('pembelian.edit', compact('pembelian', 'suppliers', 'bahans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_supp' => 'required|exists:suppliers,id_supp',
            'tgl'     => 'required|date',
            'items'   => 'required|array|min:1',
            'items.*.id_bahan' => 'required|exists:bahan_bakus,id_bahan',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $pembelian = Pembelian::with('detail')->findOrFail($id);

                /**
                 * 1. ROLLBACK STOK LAMA
                 * Sebelum update, kita kurangi stok berdasarkan data lama (pembelian dibatalkan sementara)
                 */
                foreach ($pembelian->detail as $oldDetail) {
                    BahanBaku::where('id_bahan', $oldDetail->id_bahan)
                        ->decrement('stok', $oldDetail->jumlah);
                }

                /**
                 * 2. HAPUS DETAIL LAMA
                 */
                $pembelian->detail()->delete();

                /**
                 * 3. PROSES DATA BARU (LOGIC SAMA DENGAN STORE)
                 */
                $grandTotal = 0;

                foreach ($request->items as $item) {
                    $qtyBase      = 0;
                    $hargaPerBase = 0;
                    $subtotal     = 0;

                    // Mode 1: Pack
                    if (!empty($item['qty_pack']) && !empty($item['isi_pack']) && !empty($item['harga_pack'])) {
                        $qtyPack   = (float) $item['qty_pack'];
                        $isiPack   = (float) $item['isi_pack'];
                        $hargaPack = (float) $item['harga_pack'];

                        $qtyBase  = (int) round($qtyPack * $isiPack);
                        $subtotal = $qtyPack * $hargaPack;
                        $hargaPerBase = $qtyBase > 0 ? ($subtotal / $qtyBase) : 0;
                    } 
                    // Mode 2: Base Unit
                    elseif (!empty($item['jumlah']) && !empty($item['harga_satuan'])) {
                        $qtyBase      = (int) round($item['jumlah']);
                        $hargaPerBase = (float) $item['harga_satuan'];
                        $subtotal     = $qtyBase * $hargaPerBase;
                    }

                    if ($qtyBase <= 0 || $subtotal <= 0) continue;

                    // Buat Detail Baru
                    DetailPembelian::create([
                        'id_beli'      => $pembelian->id_beli,
                        'id_bahan'     => $item['id_bahan'],
                        'jumlah'       => $qtyBase,
                        'harga_satuan' => $hargaPerBase,
                        'sub_total'    => $subtotal,
                    ]);

                    // Tambah Stok Baru
                    BahanBaku::where('id_bahan', $item['id_bahan'])
                        ->increment('stok', $qtyBase);

                    $grandTotal += $subtotal;
                }

                /**
                 * 4. UPDATE HEADER PEMBELIAN
                 */
                $pembelian->update([
                    'id_supp'    => $request->id_supp,
                    'tgl'        => $request->tgl,
                    'note'       => $request->note ?? null,
                    'total_beli' => $grandTotal
                ]);
            });

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Pembelian berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
