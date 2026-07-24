<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use Illuminate\Http\Request;

/** Dashboard Dosen -> kelola sesi absensi per kelas */
class AbsensiController extends Controller
{
    // Dosen: daftar sesi absensi (bukan per mahasiswa)
    public function index(Request $request, Kelas $kela)
    {
        $userId = $request->user()->id;

        $sesi = $kela->absensi()
            ->sesi()
            ->withCount([
                'isian as hadir_count' => fn ($q) => $q->where('status', 'hadir'),
                'isian as izin_count' => fn ($q) => $q->where('status', 'izin'),
                'isian as alpha_count' => fn ($q) => $q->where('status', 'alpha'),
            ])
            ->with(['isian' => fn ($q) => $q->where('mahasiswa_id', $userId)])
            ->orderBy('tanggal_mulai', 'desc')
            ->get()
            ->map(function ($item) {
                $item->status_saya = optional($item->isian->first())->status;
                unset($item->isian);
                return $item;
            });

        return response()->json($sesi);
    }

    // Dosen: buat sesi baru (tanpa pilih mahasiswa/status)
    public function store(Request $request, Kelas $kela)
    {
        $data = $request->validate([
            'pertemuan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
        ]);

        $sesi = $kela->absensi()->create($data);

        return response()->json($sesi, 201);
    }

    // Dosen: edit detail sesi
    public function update(Request $request, Absensi $absensi)
    {
        $data = $request->validate([
            'pertemuan' => 'sometimes|string|max:255',
            'tanggal_mulai' => 'sometimes|date',
            'tanggal_selesai' => 'sometimes|date|after_or_equal:tanggal_mulai',
            'waktu_mulai' => 'sometimes|date_format:H:i',
            'waktu_selesai' => 'sometimes|date_format:H:i',
        ]);

        $absensi->update($data);

        return response()->json($absensi);
    }

    public function destroy(Absensi $absensi)
    {
        $absensi->delete(); // cascadeOnDelete otomatis hapus semua isian mahasiswa

        return response()->json(['message' => 'Sesi absensi berhasil dihapus']);
    }

    // Mahasiswa: isi kehadiran untuk sesi tertentu
    public function isi(Request $request, Absensi $absensi)
    {
        $data = $request->validate([
            'status' => 'required|in:hadir,izin,alpha',
        ]);

        $isian = Absensi::updateOrCreate(
            [
                'sesi_id' => $absensi->id,
                'mahasiswa_id' => $request->user()->id,
            ],
            [
                'kelas_id' => $absensi->kelas_id,
                'pertemuan' => $absensi->pertemuan,
                'tanggal_mulai' => $absensi->tanggal_mulai,
                'tanggal_selesai' => $absensi->tanggal_selesai,
                'waktu_mulai' => $absensi->waktu_mulai,
                'waktu_selesai' => $absensi->waktu_selesai,
                'status' => $data['status'],
            ]
        );

        return response()->json($isian, 201);
    }

    // Dosen: detail isian mahasiswa untuk satu sesi
    public function detail(Absensi $absensi)
    {
        $isian = $absensi->isian()
            ->with('mahasiswa:id,name,nim')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'nama' => $item->mahasiswa->name ?? '-',
                'nim' => $item->mahasiswa->nim ?? '-',
                'status' => $item->status,
            ]);

        return response()->json([
            'pertemuan' => $absensi->pertemuan,
            'kelas' => optional($absensi->kelas)->nama,
            'isian' => $isian,
        ]);
    }
}