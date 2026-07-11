<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Jalankan: php artisan db:seed  (menyiapkan akun demo utk 3 role) */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Rina Wijaya', 'email' => 'admin@eduonline.id',
            'password' => Hash::make('admin123'), 'role' => 'admin',
        ]);

        $dosen = User::create([
            'name' => 'Dr. Siti Aminah, M.Kom', 'email' => 'dosen@eduonline.id',
            'password' => Hash::make('dosen123'), 'role' => 'dosen', 'bidang' => 'Rekayasa Perangkat Lunak',
        ]);

        $mhs1 = User::create([
            'name' => 'Budi Santoso', 'email' => 'mahasiswa@eduonline.id',
            'password' => Hash::make('mhs123'), 'role' => 'mahasiswa', 'nim' => '2210511001',
        ]);
        $mhs2 = User::create([
            'name' => 'Citra Ayu Lestari', 'email' => 'citra@eduonline.id',
            'password' => Hash::make('mhs123'), 'role' => 'mahasiswa', 'nim' => '2210511002',
        ]);

        $kelas = Kelas::create(['nama' => 'Pemrograman Web Lanjut', 'kode' => 'IF-3201', 'dosen_id' => $dosen->id]);
        $kelas->mahasiswa()->attach([$mhs1->id, $mhs2->id]);

        Materi::create([
            'kelas_id' => $kelas->id, 'judul' => 'Pengenalan React & Component Lifecycle',
            'deskripsi' => 'Slide dasar React hooks.', 'file_path' => 'materi/contoh.pdf',
            'file_name' => '01-pengenalan-react.pdf', 'tipe' => 'pdf',
        ]);

        Tugas::create([
            'kelas_id' => $kelas->id, 'judul' => 'Tugas 1: Membangun Komponen Card',
            'instruksi' => 'Buat komponen card reusable dengan props title dan image.',
            'deadline' => now()->addDays(10),
        ]);
    }
}
