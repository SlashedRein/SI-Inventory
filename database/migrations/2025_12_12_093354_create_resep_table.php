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
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();
            // Relasi ke Produk
            $table->foreignId('id_produk')
                ->constrained('produks', 'id_produk')
                ->onDelete('cascade'); // Kalau produk dihapus, resep ikut kehapus
                
            // Relasi ke Bahan Baku
            $table->foreignId('id_bahan')
                ->constrained('bahan_bakus', 'id_bahan')
                ->onDelete('cascade');
                
            $table->integer('takaran'); // Jumlah yang dibutuhkan
            $table->string('satuan'); 
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
