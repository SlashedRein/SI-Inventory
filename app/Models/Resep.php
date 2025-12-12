<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    protected $table = 'reseps';
    protected $guarded = [];

    // Relasi balik ke Bahan Baku (Untuk ambil nama bahannya)
    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan');
    }
}