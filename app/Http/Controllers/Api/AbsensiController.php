<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

/** Dashboard Dosen -> "Rekap Absensi Mahasiswa" (grafik/tabel presensi) */
class AbsensiController extends Controller
{
    public function index(Kelas $kela)
    {
        $rekap = $kela->absensi()
            ->selectRaw('mahasiswa_id, status, count(*) as jumlah')
            ->groupBy('mahasiswa_id', 'status')
            ->with('mahasiswa:id,name')
            ->get()
            ->groupBy('mahasiswa_id');

        return response()->json($rekap);
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
}
