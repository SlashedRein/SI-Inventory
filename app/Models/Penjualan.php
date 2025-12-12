<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualans';
    protected $primaryKey = 'id_penjualan';
    protected $guarded = [];

    // Relasi: 1 Nota punya banyak Detail Barang
    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan');
    }

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_cust');
    }

    // Relasi ke User (Kasir)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}