<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\NilaiAkhir;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Dashboard Mahasiswa -> Tab "Tugas" (submit file tugas, status jadi "Uploaded")
 * Dashboard Dosen -> "Ruang Penilaian" (list submission + input nilai/feedback)
 */
class PengumpulanTugasController extends Controller
{
    // Mahasiswa submit tugas
    public function store(Request $request, Tugas $tugas)
    {
        $request->validate(['file' => 'required|file|max:51200']);

        $path = $request->file('file')->store('tugas-mahasiswa', 'public');

        $submission = PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $tugas->id, 'mahasiswa_id' => $request->user()->id],
            [
                'file_path' => $path,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'status' => now()->gt($tugas->deadline) ? 'Terlambat' : 'Uploaded',
                'submitted_at' => now(),
            ]
        );

        return response()->json($submission, 201);
    }

    // Grading Center: list semua submission untuk kelas yang diampu dosen login
    public function indexForDosen(Request $request)
    {
        $submissions = PengumpulanTugas::with(['mahasiswa', 'tugas.kelas'])
            ->whereHas('tugas.kelas', fn ($q) => $q->where('dosen_id', $request->user()->id))
            ->latest('submitted_at')
            ->get();

        return response()->json($submissions);
    }

    // Dosen input nilai (0-100) + feedback -> update database + auto-sync ke NilaiAkhir
    public function grade(Request $request, PengumpulanTugas $pengumpulan_tugas)
    {
        $data = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $pengumpulan_tugas->update($data);

        // Auto-sync: hitung rata-rata semua tugas mahasiswa ini di kelas yang sama,
        // lalu simpan ke NilaiAkhir.nilai_tugas
        $pengumpulan_tugas->load('tugas');
        $kelasId = $pengumpulan_tugas->tugas->kelas_id;
        $mahasiswaId = $pengumpulan_tugas->mahasiswa_id;

        $rataRataTugas = PengumpulanTugas::whereHas('tugas', fn ($q) => $q->where('kelas_id', $kelasId))
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNotNull('nilai')
            ->avg('nilai');

        NilaiAkhir::updateOrCreate(
            ['kelas_id' => $kelasId, 'mahasiswa_id' => $mahasiswaId],
            ['nilai_tugas' => round($rataRataTugas)]
        );

        return response()->json($pengumpulan_tugas);
    }

    public function download(PengumpulanTugas $pengumpulan_tugas)
    {
        return Storage::disk('public')->download($pengumpulan_tugas->file_path, $pengumpulan_tugas->file_name);
    }

    // Mahasiswa lihat submission miliknya sendiri (untuk halaman Nilai)
    public function indexForMahasiswa(Request $request)
        {
            $submissions = PengumpulanTugas::with(['tugas.kelas'])
                ->where('mahasiswa_id', $request->user()->id)
                ->latest('submitted_at')
                ->get();

            return response()->json($submissions);
        }
}
