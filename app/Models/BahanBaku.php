<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_bakus';
    protected $primaryKey = 'id_bahan';
    protected $guarded = [];

    // stok = BASE UNIT
    // satuan = gram | ml | pcs
}
