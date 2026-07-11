<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Rekap nilai akhir per mahasiswa per kelas: tugas, kuis, ujian. */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('nilai_akhir', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignUuid('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai_tugas')->nullable();
            $table->unsignedTinyInteger('nilai_kuis')->nullable();
            $table->unsignedTinyInteger('nilai_ujian')->nullable();
            $table->timestamps();
            $table->unique(['kelas_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_akhir');
    }
};
