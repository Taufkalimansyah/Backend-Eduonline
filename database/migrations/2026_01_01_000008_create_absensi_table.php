<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kelas_id')->constrained('kelas')->cascadeOnDelete();

            // kolom dulu tanpa constraint FK (self-reference ditambahkan belakangan)
            $table->uuid('sesi_id')->nullable();
            $table->foreignUuid('mahasiswa_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->string('pertemuan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->enum('status', ['hadir', 'izin', 'alpha'])->nullable();

            $table->timestamps();

            $table->unique(['sesi_id', 'mahasiswa_id']);
        });

        // tambahkan FK self-reference setelah tabel benar-benar ada
        Schema::table('absensi', function (Blueprint $table) {
            $table->foreign('sesi_id')->references('id')->on('absensi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropForeign(['sesi_id']);
        });

        Schema::dropIfExists('absensi');
    }
};