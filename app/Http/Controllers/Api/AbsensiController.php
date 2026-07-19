<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use Illuminate\Http\Request;

/** Dashboard Dosen -> "Rekap Absensi Mahasiswa" (grafik/tabel presensi) */
class AbsensiController extends Controller
{
    public function index(Kelas $kela)
    {
        $absensi = $kela->absensi()
            ->with('mahasiswa:id,name,nim')
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json($absensi);
    }

    public function store(Request $request, Kelas $kela)
    {
        $data = $request->validate([
            'mahasiswa_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,alpha',
        ]);

        $absensi = $kela->absensi()->create($data);

        return response()->json($absensi, 201);
    }

    public function update(Request $request, Absensi $absensi)
    {
        $data = $request->validate([
            'tanggal' => 'sometimes|date',
            'status' => 'sometimes|in:hadir,izin,alpha',
        ]);

        $absensi->update($data);

        return response()->json($absensi);
    }

    public function destroy(Absensi $absensi)
    {
        $absensi->delete();

        return response()->json(['message' => 'Absensi berhasil dihapus']);
    }
}