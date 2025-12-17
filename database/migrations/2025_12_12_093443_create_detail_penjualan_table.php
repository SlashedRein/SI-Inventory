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
        Schema::create('detail_penjualans', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Struk Penjualan
            $table->foreignId('id_penjualan')
                ->constrained('penjualans', 'id_penjualan')
                ->onDelete('cascade');
                
            // Relasi ke Produk yang dijual
            $table->foreignId('id_produk')
                ->constrained('produks', 'id_produk');
                
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
        Schema::dropIfExists('detail_penjualans');
    }
};
