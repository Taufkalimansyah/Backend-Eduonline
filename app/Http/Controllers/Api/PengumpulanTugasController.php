<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
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

    // Dosen input nilai (0-100) + feedback -> langsung update database
    public function grade(Request $request, PengumpulanTugas $pengumpulan_tugas)
    {
        $data = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $pengumpulan_tugas->update($data);

        return response()->json($pengumpulan_tugas);
    }

    public function download(PengumpulanTugas $pengumpulan_tugas)
    {
        return Storage::disk('public')->download($pengumpulan_tugas->file_path, $pengumpulan_tugas->file_name);
    }
}
