<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    protected $table = 'detail_pembelians';
    protected $guarded = [];

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan');
    }
}