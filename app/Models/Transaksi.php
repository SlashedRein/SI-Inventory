<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    // 1. INI KUNCINYA: Arahkan ke nama tabel yang benar
    protected $table = 'penjualans'; 

    // 2. CEK JUGA PRIMARY KEY-NYA
    // Kalau di tabel 'penjualans', nama kolom ID-nya apa? 
    // Apakah 'id'? atau 'id_penjualan'? atau 'id_transaksi'?
    // Sesuaikan di sini:
    protected $primaryKey = 'id_transaksi'; // Ganti kalau ternyata namanya 'id' atau 'id_penjualan'

    public $timestamps = false; 

    // Relasi ke Customer
    public function customer()
    {
        // Pastikan 'id_pelanggan' sesuai dengan nama kolom foreign key di tabel 'penjualans'
        return $this->belongsTo(Customer::class, 'id_pelanggan');
    }
}