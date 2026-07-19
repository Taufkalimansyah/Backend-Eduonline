<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{

    /**
     * GET semua materi
     * Dashboard dosen / mahasiswa
     */
    public function index()
    {
        $materi = Materi::with('kelas')
            ->latest()
            ->get();

        return response()->json($materi);
    }


    /**
     * Detail materi
     */
    public function show(Materi $materi)
    {
        return response()->json(
            $materi->load('kelas')
        );
    }



    /**
     * Upload materi ke kelas tertentu
     */
    public function store(Request $request, Kelas $kela)
    {

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'required|file|max:51200',
        ]);


        $file = $request->file('file');


        $path = $file->store(
            'materi',
            'public'
        );


        $materi = $kela->materi()->create([

            'judul' => $data['judul'],

            'deskripsi' => $data['deskripsi'] ?? null,

            'file_path' => $path,

            'file_name' => $file->getClientOriginalName(),

            'tipe' => str_contains(
                $file->getMimeType(),
                'video'
            )
                ? 'video'
                : 'pdf',

        ]);


        return response()->json(
            $materi,
            201
        );
    }



    /**
     * Update materi
     */
    public function update(Request $request, Materi $materi)
    {

        $data = $request->validate([

            'judul' => 'required|string|max:255',

            'deskripsi' => 'nullable|string',

            'file' => 'nullable|file|max:51200',

        ]);



        /*
        jika upload file baru
        hapus file lama
        */
        if($request->hasFile('file')){


            if(
                Storage::disk('public')
                ->exists($materi->file_path)
            ){

                Storage::disk('public')
                ->delete($materi->file_path);

            }



            $file = $request->file('file');


            $path = $file->store(
                'materi',
                'public'
            );


            $data['file_path'] = $path;

            $data['file_name'] =
                $file->getClientOriginalName();


            $data['tipe'] =
                str_contains(
                    $file->getMimeType(),
                    'video'
                )
                ? 'video'
                : 'pdf';

        }


        $materi->update($data);


        return response()->json(
            $materi
        );
    }




    /**
     * Delete materi
     */
    public function destroy(Materi $materi)
    {

        // hapus file fisik
        if(
            Storage::disk('public')
            ->exists($materi->file_path)
        ){

            Storage::disk('public')
            ->delete($materi->file_path);

        }


        $materi->delete();


        return response()->json([
            'message'=>'Materi berhasil dihapus'
        ]);

    }





    /**
     * Download file
     */
    public function download(Materi $materi)
    {

        return Storage::disk('public')
            ->download(
                $materi->file_path,
                $materi->file_name
            );

    }

}