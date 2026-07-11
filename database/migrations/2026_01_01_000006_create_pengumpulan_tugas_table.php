<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rekam pengumpulan tugas mahasiswa. Kolom `nilai` & `feedback` diisi
 * oleh dosen lewat Grading Center (fitur (b) Dosen).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignUuid('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->enum('status', ['Uploaded', 'Terlambat'])->default('Uploaded');
            $table->timestamp('submitted_at')->useCurrent();
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->unique(['tugas_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
