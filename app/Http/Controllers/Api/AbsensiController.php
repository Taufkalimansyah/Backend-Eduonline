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
    $absensi = $kela->absensi()
        ->with('mahasiswa:id,name,nim')
        ->orderBy('tanggal', 'desc')
        ->get();

    return response()->json($absensi);
}

public function update(Request $request, $id)
{
    $data = $request->validate([
        'tanggal' => 'sometimes|date',
        'status' => 'sometimes|in:hadir,izin,alpha',
    ]);

    $absensi = \App\Models\Absensi::findOrFail($id);
    $absensi->update($data);

    return response()->json($absensi);
}

public function destroy($id)
{
    \App\Models\Absensi::findOrFail($id)->delete();
    return response()->json(['message' => 'Absensi berhasil dihapus']);
}
}
