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
        Schema::create('bahan_bakus', function (Blueprint $table) {
            $table->id('id_bahan');
            $table->string('nama_bahan');
            $table->integer('stok')->default(0);
            $table->integer('stok_min')->default(0);
            $table->string('satuan'); // Kg, Gr, Liter, dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_bakus'); // <--- PAKE 'S'
    }
};
