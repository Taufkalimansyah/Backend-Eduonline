<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NilaiAkhir;
use Illuminate\Http\Request;

/** Dashboard Mahasiswa -> "Nilai Akhir (Gradebook)" */
class NilaiAkhirController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            NilaiAkhir::with('kelas')
                ->where('mahasiswa_id', $request->user()->id)
                ->get()
        );
    }

    // Dipanggil dari Grading Center dosen untuk update rekap nilai_akhir per kelas
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mahasiswa_id' => 'required|exists:users,id',
            'nilai_tugas' => 'nullable|integer|min:0|max:100',
            'nilai_kuis' => 'nullable|integer|min:0|max:100',
            'nilai_ujian' => 'nullable|integer|min:0|max:100',
        ]);

        $nilai = NilaiAkhir::updateOrCreate(
            ['kelas_id' => $data['kelas_id'], 'mahasiswa_id' => $data['mahasiswa_id']],
            $data
        );

        return response()->json($nilai);
    }
}
