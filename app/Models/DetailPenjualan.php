<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualans';
    protected $guarded = [];

    // Relasi balik ke Produk (Untuk tahu nama produk yang dijual)
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}