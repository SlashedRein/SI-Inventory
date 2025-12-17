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
        return redirect()
            ->route('pembelian.index')
            ->with('info', 'Import pembelian belum tersedia.');
    }
}
