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
        Schema::create('suppliers', function (Blueprint $table) {
            // Kita pakai id_supp sebagai primary key sesuai ERD
            $table->id('id_supp'); 
            $table->string('nama');
            $table->text('alamat');
            $table->string('no_telp');
            $table->string('no_rek')->nullable(); // Boleh kosong
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
