<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Tugas;
use Illuminate\Http\Request;

class TugasController extends Controller
{
    /**
     * GET semua tugas
     */
    public function index()
    {
        $tugas = Tugas::with('kelas')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($tugas);
    }

    /**
     * GET detail tugas
     */
    public function show(Tugas $tuga)
    {
        return response()->json(
            $tuga->load('kelas')
        );
    }

    /**
     * POST tambah tugas
     */
    public function store(Request $request, Kelas $kela)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'instruksi' => 'nullable|string',
            'deadline' => 'required|date',
        ]);

        $tugas = $kela->tugas()->create($data);

        return response()->json($tugas, 201);
    }

    /**
     * PUT update tugas
     */
    public function update(Request $request, Tugas $tuga)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'instruksi' => 'nullable|string',
            'deadline' => 'required|date',
        ]);

        $tuga->update($data);

        return response()->json($tuga);
    }

    /**
     * DELETE tugas
     */
    public function destroy(Tugas $tuga)
    {
        $tuga->delete();

        return response()->json([
            'message' => 'Tugas berhasil dihapus'
        ]);
    }
}