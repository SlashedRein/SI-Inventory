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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Tambahkan ini:
            $table->string('username')->unique(); 
            
            $table->string('email')->unique(); // Biarkan email tetap ada untuk fitur reset password (opsional)
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['owner', 'kasir'])->default('kasir');
            $table->rememberToken();
            $table->timestamps();
        });
        
        // ... code tabel sessions dll biarkan saja
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
