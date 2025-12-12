<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produks';
    protected $primaryKey = 'id_produk'; // Kasih tahu Laravel PK-nya bukan 'id'
    protected $guarded = []; // Biar bisa input semua kolom sekaligus

    // Relasi ke Resep (1 Produk punya banyak bahan penyusun)
    public function resep()
    {
        return $this->hasMany(Resep::class, 'id_produk');
    }
}