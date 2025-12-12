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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id('id_beli');
            
            // Relasi ke Supplier
            $table->foreignId('id_supp')
                ->constrained('suppliers', 'id_supp');
                
            $table->date('tgl');
            $table->decimal('total_beli', 12, 2)->default(0);
            $table->text('note')->nullable();
            
            // Relasi ke User (Siapa yang input)
            $table->foreignId('user_id')->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
