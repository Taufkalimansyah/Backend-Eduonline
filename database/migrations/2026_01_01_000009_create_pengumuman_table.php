<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Pengumuman global yang dibuat admin, tampil di dashboard dosen & mahasiswa. */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {

        $table->uuid('id')->primary();

        $table->foreignUuid('pembuat_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->string('judul');

        $table->text('isi');

        $table->date('tanggal');

        $table->enum('status',[
            'aktif',
            'nonaktif'
        ])->default('aktif');

        $table->timestamps();
    });
        }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};