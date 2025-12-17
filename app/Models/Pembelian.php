<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelians';
    protected $primaryKey = 'id_beli';
    protected $guarded = [];

    public function detail()
    {
        return $this->hasMany(DetailPembelian::class, 'id_beli');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supp');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
