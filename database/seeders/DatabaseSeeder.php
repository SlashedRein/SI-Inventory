<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\Resep;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bikin Akun Owner
        User::create([
            'name' => 'Dewi',
            'username' => 'Dewi', // Username login
            'email' => 'owner@toko.com',
            'password' => Hash::make('nastar123'),
            'role' => 'owner',
        ]);

        // 2. Bikin Akun Kasir
        User::create([
            'name' => 'Karyawan',
            'username' => 'Karyawan', // Username login
            'email' => 'kasir@toko.com',
            'password' => Hash::make('12345678'),
            'role' => 'kasir',
        ]);

        // 2. Bikin Supplier Dummy
        $supp1 = Supplier::create([
            'id_supp' => 101, // Manual ID karena di ERD/Migration tadi kita tidak set auto-increment (opsional) atau biarkan default
            'nama' => 'CV. Tepung Jaya',
            'alamat' => 'Jl. Gandum No. 1',
            'no_telp' => '08123456789',
        ]);

        // 3. Bikin Customer Dummy
        Customer::create([
            'id_cust' => 1,
            'nama' => 'Budi Langganan',
            'alamat' => 'Jl. Mawar',
            'no_telp' => '08987654321',
        ]);

        // 4. Bikin Bahan Baku
        $tepung = BahanBaku::create([
            'nama_bahan' => 'Tepung Terigu',
            'stok' => 50000, // 50kg
            'stok_min' => 1000,
            'satuan' => 'gram',
        ]);

        $gula = BahanBaku::create([
            'nama_bahan' => 'Gula Pasir',
            'stok' => 20000,
            'stok_min' => 1000,
            'satuan' => 'gram',
        ]);

        // 5. Bikin Produk
        $roti = Produk::create([
            'nama_produk' => 'Roti Manis Original',
            'stok' => 10,
            'harga_jual' => 5000,
            'satuan' => 'pcs',
        ]);

        // 6. Bikin Resep (1 Roti butuh 200gr Tepung & 50gr Gula)
        Resep::create([
            'id_produk' => $roti->id_produk,
            'id_bahan' => $tepung->id_bahan,
            'takaran' => 200,
            'satuan' => 'gram',
        ]);
        
        Resep::create([
            'id_produk' => $roti->id_produk,
            'id_bahan' => $gula->id_bahan,
            'takaran' => 50,
            'satuan' => 'gram',
        ]);
    }
}