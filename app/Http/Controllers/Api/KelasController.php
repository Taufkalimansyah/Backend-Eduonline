<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

/**
 * Dipakai oleh:
 * - Dashboard Mahasiswa -> "Kelas Saya" (GET /api/classes, otomatis discope
 *   ke kelas yang diikuti user login)
 * - Dashboard Dosen -> "Kelola Kelas" + fitur "Tambah Kelas" (POST /api/classes)
 */
class KelasController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Kelas::with(['dosen', 'materi', 'tugas']);

        if ($user->isMahasiswa()) {
            $query->whereHas('mahasiswa', fn ($q) => $q->where('users.id', $user->id));
        } elseif ($user->isDosen()) {
            $query->where('dosen_id', $user->id);
        }
        // admin: tanpa filter, melihat semua kelas

        return response()->json($query->get());
    }

    public function show(Kelas $kela)
    {
        return response()->json($kela->load(['dosen', 'materi', 'tugas', 'mahasiswa']));
    }

    // Fitur 2.d: dosen membuat kelas baru — otomatis muncul di "Kelas Saya" mahasiswa
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:kelas,kode',
            'mahasiswa_ids' => 'array', // opsional: daftar mahasiswa yang langsung didaftarkan
        ]);

        $kelas = Kelas::create([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
            'dosen_id' => $request->user()->id,
        ]);

        if (! empty($data['mahasiswa_ids'])) {
            $kelas->mahasiswa()->attach($data['mahasiswa_ids']);
        }

        return response()->json($kelas, 201);
    }
}
