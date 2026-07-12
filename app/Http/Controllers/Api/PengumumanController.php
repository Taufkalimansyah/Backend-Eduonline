<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    // GET /api/pengumuman
    public function index()
    {
        return Pengumuman::with('pembuat')
            ->latest()
            ->get();
    }

    // POST /api/pengumuman
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255|unique:pengumuman,judul',
            'isi' => 'required|string',
            'tanggal' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data['pembuat_id'] = auth()->id();

        $pengumuman = Pengumuman::create($data);

        return response()->json([
            'message' => 'Pengumuman berhasil dibuat',
            'data' => $pengumuman
        ],201);
    }

    // GET /api/pengumuman/{id}
    public function show(Pengumuman $pengumuman)
    {
        return $pengumuman->load('pembuat');
    }

    // PUT /api/pengumuman/{id}
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $pengumuman->update($data);

        return response()->json([
            'message' => 'Pengumuman berhasil diupdate',
            'data' => $pengumuman
        ]);
    }

    // DELETE /api/pengumuman/{id}
    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return response()->json([
            'message' => 'Pengumuman berhasil dihapus'
        ]);
    }
}