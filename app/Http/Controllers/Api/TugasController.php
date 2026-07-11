<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

/** Dashboard Dosen -> "Form Pembuat Tugas (Assignment Creator)" */
class TugasController extends Controller
{
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
}
