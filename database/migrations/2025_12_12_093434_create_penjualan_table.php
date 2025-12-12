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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id('id_penjualan');
            
            // Relasi ke Customer (Boleh null jika pembeli umum/non-member)
            $table->foreignId('id_cust')
                ->nullable()
                ->constrained('customers', 'id_cust');
                
            $table->date('tgl_penjualan');
            $table->decimal('total', 12, 2)->default(0);
            
            // Relasi ke Kasir yang melayani
            $table->foreignId('user_id')->constrained('users');
            
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
