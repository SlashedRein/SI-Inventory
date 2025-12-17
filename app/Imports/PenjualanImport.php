<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Kita butuh ini buat ngintip error
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\Customer;
use Carbon\Carbon;

class PenjualanImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // 1. Grouping berdasarkan No Transaksi
        $transaksiGroup = $rows->groupBy('no_transaksi');

        foreach ($transaksiGroup as $no_transaksi => $items) {
            
            // Log proses (Biar tau sistem jalan)
            Log::info("Memproses Transaksi: " . $no_transaksi);

            $firstRow = $items->first();
            
            // --- A. LOGIKA CUSTOMER (AUTO CREATE) ---
            $namaCust = trim($firstRow['customer'] ?? 'Umum');
            $customer = Customer::where('nama', 'LIKE', $namaCust)->first();
            
            if (!$customer) {
                // Kalau gak ada, buat baru otomatis!
                $customer = Customer::create([
                    'nama' => $namaCust,
                    'alamat' => '-',
                    'no_telp' => '-'
                ]);
                Log::info("Customer baru dibuat: " . $namaCust);
            }
            $id_customer = $customer->id_cust;

            // --- B. LOGIKA TANGGAL (AUTO DETECT) ---
            try {
                $rawTanggal = $firstRow['tanggal'];
                if (is_numeric($rawTanggal)) {
                    // Kalau format Excel angka (Serial Date)
                    $tgl = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawTanggal);
                } else {
                    // Kalau format Teks (2024-12-01)
                    $tgl = Carbon::parse($rawTanggal);
                }
            } catch (\Exception $e) {
                $tgl = now();
                Log::warning("Tanggal error di $no_transaksi, pakai tanggal hari ini.");
            }

            // --- C. LOGIKA PRODUK (CARI PAKSA) ---
            $grandTotal = 0;
            $validItems = [];

            foreach ($items as $row) {
                // Bersihkan nama produk dari spasi aneh & huruf besar/kecil
                $namaExcel = trim($row['nama_produk']);
                
                // Cari produk pakai LIKE (mirip-mirip dikit gapapa)
                $produk = Produk::where('nama_produk', 'LIKE', '%' . $namaExcel . '%')->first();

                if ($produk) {
                    $qty = (int) $row['qty'];
                    
                    // Cek stok cukup gak? (Opsional: kalau mau maksa masuk walau minus, hapus if ini)
                    if ($produk->stok < $qty) {
                        Log::warning("Stok minus untuk $namaExcel di transaksi $no_transaksi. Tetap diproses.");
                    }

                    $subtotal = $produk->harga_jual * $qty;
                    $grandTotal += $subtotal;

                    $validItems[] = [
                        'produk' => $produk,
                        'qty' => $qty,
                        'subtotal' => $subtotal
                    ];
                } else {
                    // PENTING: Ini bakal kasih tau kamu barang apa yang gak ketemu
                    Log::error("PRODUK TIDAK DITEMUKAN: '$namaExcel'. Pastikan ejaan di database sama.");
                }
            }

            // Kalau gak ada barang valid 1 pun, skip transaksi ini
            if ($grandTotal == 0 || empty($validItems)) {
                Log::error("Transaksi $no_transaksi di-skip karena tidak ada produk valid.");
                continue;
            }

            // --- D. SIMPAN KE DATABASE ---
            $penjualan = Penjualan::create([
                'no_transaksi' => $no_transaksi,
                'tgl_penjualan' => $tgl,
                'total' => $grandTotal,
                'bayar' => $grandTotal, 
                'kembali' => 0,
                'id_user' => Auth::id() ?? 1,
                'id_customer' => $id_customer,
            ]);

            foreach ($validItems as $item) {
                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id_penjualan,
                    'id_produk' => $item['produk']->id_produk,
                    'jumlah' => $item['qty'],
                    'subtotal' => $item['subtotal']
                ]);

                // Potong Stok
                $item['produk']->decrement('stok', $item['qty']);
            }
            
            Log::info("Sukses Import Transaksi: $no_transaksi");
        }
    }
}