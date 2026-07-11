<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel inti: menyimpan semua akun (admin, dosen, mahasiswa) dalam satu
 * tabel dengan kolom `role` sebagai pembeda (single table inheritance).
 * Kolom `nim` dan `bidang` bersifat nullable karena hanya relevan untuk
 * role tertentu (mahasiswa / dosen).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'dosen', 'mahasiswa'])->default('mahasiswa');
            $table->string('nim')->nullable();      // khusus mahasiswa
            $table->string('bidang')->nullable();    // khusus dosen
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
