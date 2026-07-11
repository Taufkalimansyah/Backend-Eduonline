<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

/**
 * Dashboard Admin -> "Manajemen Pengumuman Global".
 * Endpoint index() dipanggil oleh dashboard dosen & mahasiswa untuk
 * menampilkan pengumuman secara langsung (polling ringan setiap kali
 * dashboard dibuka, bisa ditingkatkan ke WebSocket/Pusher untuk realtime).
 */
class PengumumanController extends Controller
{
    public function index()
    {
        return response()->json(Pengumuman::with('author:id,name')->latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        $pengumuman = Pengumuman::create([
            ...$data,
            'author_id' => $request->user()->id,
        ]);

        return response()->json($pengumuman, 201);
    }
}
