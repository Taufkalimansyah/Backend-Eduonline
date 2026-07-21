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

        $query = Kelas::with([
            'dosen',
            'materi',
            'tugas',
        ])->withCount('mahasiswa');

        if ($user->isMahasiswa()) {
            $query->whereHas('mahasiswa', fn ($q) =>
                $q->where('users.id', $user->id)
            );
        } elseif ($user->isDosen()) {
            $query->where('dosen_id', $user->id);
        }

        return response()->json($query->latest()->get());
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

    // Update kelas
    public function update(Request $request, Kelas $kela)
    {
        // Pastikan hanya dosen pemilik kelas yang bisa update
        if ($kela->dosen_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengubah kelas ini.'
            ], 403);
        }

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:kelas,kode,' . $kela->id,
            'mahasiswa_ids' => 'array',
        ]);

        $kela->update([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
        ]);

        // update relasi mahasiswa jika dikirim
        if (isset($data['mahasiswa_ids'])) {
            $kela->mahasiswa()->sync($data['mahasiswa_ids']);
        }

        return response()->json($kela->load('mahasiswa'));
    }


    // Delete kelas
    public function destroy(Request $request, Kelas $kela)
    {
        // Pastikan hanya dosen pemilik kelas yang bisa hapus
        if ($kela->dosen_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus kelas ini.'
            ], 403);
        }

        $kela->delete();

        return response()->json([
            'message' => 'Kelas berhasil dihapus.'
        ]);
    }

    /**
 * GET kelas yang BELUM diikuti mahasiswa (untuk pilihan KRS)
 */
public function available(Request $request)
{
    $user = $request->user();

    $kelas = Kelas::with('dosen')
        ->whereDoesntHave('mahasiswa', fn ($q) => $q->where('users.id', $user->id))
        ->get();

    return response()->json($kelas);
}

/**
 * POST mahasiswa mendaftarkan diri ke kelas (self-enroll KRS)
 */
public function enroll(Request $request, Kelas $kela)
{
    $user = $request->user();

    $sudahTerdaftar = $kela->mahasiswa()->where('users.id', $user->id)->exists();

    if ($sudahTerdaftar) {
        return response()->json([
            'message' => 'Anda sudah terdaftar di kelas ini.'
        ], 422);
    }

    $kela->mahasiswa()->attach($user->id);

    return response()->json([
        'message' => 'Berhasil mendaftar ke kelas.',
        'kelas' => $kela->load('dosen')
    ], 201);
}
}
