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
            // Primary key named to match model expectations
            $table->id('id_penjualan');

            // Customer relation (nullable for walk-in buyers)
            $table->unsignedBigInteger('id_cust')->nullable();

            // Transaction date and totals
            $table->date('tgl_penjualan');
            $table->decimal('total', 12, 2)->default(0);

            // Kasir / user who handled the sale
            $table->foreignId('user_id')->constrained('users');

            // Optional: details stored in separate detail_penjualans table
            $table->timestamps();

            // Foreign key for customer (set null on delete)
            $table->foreign('id_cust')
                ->references('id_cust')
                ->on('customers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};