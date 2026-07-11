<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Dashboard Dosen -> "Upload Materi Baru" & Dashboard Mahasiswa -> Tab "Materi" */
class MateriController extends Controller
{
    public function store(Request $request, Kelas $kela)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'required|file|max:51200', // 50MB
        ]);

        $path = $request->file('file')->store('materi', 'public');

        $materi = $kela->materi()->create([
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'tipe' => str_contains($request->file('file')->getMimeType(), 'video') ? 'video' : 'pdf',
        ]);

        return response()->json($materi, 201);
    }

    // Tombol "Download" di dashboard mahasiswa
    public function download(Materi $materi)
    {
        return Storage::disk('public')->download($materi->file_path, $materi->file_name);
    }
}
