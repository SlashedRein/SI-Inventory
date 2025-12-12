<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up(): void
    {
        Schema::create('detail_pembelians', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Nota Pembelian
            $table->foreignId('id_beli')
                ->constrained('pembelians', 'id_beli')
                ->onDelete('cascade');
                
            // Relasi ke Bahan Baku yang dibeli
            $table->foreignId('id_bahan')
                ->constrained('bahan_bakus', 'id_bahan');
                
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('sub_total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian');
    }
};
