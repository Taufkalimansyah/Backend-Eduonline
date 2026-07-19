<?php

use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\NilaiAkhirController;
use App\Http\Controllers\Api\PengumpulanTugasController;
use App\Http\Controllers\Api\PengumumanController;
use App\Http\Controllers\Api\TugasController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — EduOnline LMS
|--------------------------------------------------------------------------
| Semua route (kecuali /login) dilindungi Sanctum. React mengirim
| header "Authorization: Bearer <token>" pada setiap fetch/axios call.
| Middleware 'role:xxx' membatasi akses sesuai role di setiap dashboard.
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Kelas — dipakai Dashboard Mahasiswa & Dosen
    Route::get('/classes', [KelasController::class, 'index']);
    Route::get('/classes/{kela}', [KelasController::class, 'show']);
    Route::post('/classes', [KelasController::class, 'store'])->middleware('role:dosen');
    route::put('/classes/{kela}', [KelasController::class, 'update'])->middleware('role:dosen');
    route::delete('/classes/{kela}', [KelasController::class, 'destroy'])->middleware('role:dosen');

    // Materi — upload oleh dosen, download oleh mahasiswa
    Route::get('/materials', [MateriController::class, 'index']);
    Route::get('/materials/{materi}', [MateriController::class, 'show']);
    Route::post('/classes/{kela}/materials', [MateriController::class, 'store'])->middleware('role:dosen');
    Route::put('/materials/{materi}', [MateriController::class, 'update'])->middleware('role:dosen');
    Route::delete('/materials/{materi}', [MateriController::class, 'destroy'])->middleware('role:dosen');
    Route::get('/materials/{materi}/download', [MateriController::class, 'download']);

    // Tugas (assignment) — dibuat dosen
    Route::post('/classes/{kela}/assignments', [TugasController::class, 'store'])->middleware('role:dosen');

    // Pengumpulan tugas — submit oleh mahasiswa, dinilai oleh dosen
    Route::get('/assignments', [TugasController::class, 'index']);
    Route::get('/assignments/{tuga}', [TugasController::class, 'show']);
    Route::post('/classes/{kela}/assignments', [TugasController::class, 'store'])->middleware('role:dosen');
    Route::put('/assignments/{tuga}', [TugasController::class, 'update'])->middleware('role:dosen');
    Route::delete('/assignments/{tuga}', [TugasController::class, 'destroy'])->middleware('role:dosen');
    Route::post('/assignments/{tugas}/submissions', [PengumpulanTugasController::class, 'store'])->middleware('role:mahasiswa');
    Route::get('/submissions', [PengumpulanTugasController::class, 'indexForDosen'])->middleware('role:dosen');
    Route::put('/submissions/{pengumpulan_tugas}', [PengumpulanTugasController::class, 'grade'])->middleware('role:dosen');
    Route::get('/submissions/{pengumpulan_tugas}/download', [PengumpulanTugasController::class, 'download']);

    // Nilai akhir (gradebook) — mahasiswa lihat, dosen upsert
    Route::get('/grades', [NilaiAkhirController::class, 'index'])->middleware('role:mahasiswa');
    Route::post('/grades', [NilaiAkhirController::class, 'upsert'])->middleware('role:dosen');

    // Absensi — dosen kelola & lihat rekap
    Route::get('/classes/{kela}/attendance', [AbsensiController::class, 'index']);
    Route::post('/classes/{kela}/attendance', [AbsensiController::class, 'store'])->middleware('role:dosen');
<<<<<<< HEAD
    Route::put('/attendance/{absensi}', [AbsensiController::class, 'update'])->middleware('role:dosen');
    Route::delete('/attendance/{absensi}', [AbsensiController::class, 'destroy'])->middleware('role:dosen');
=======
<<<<<<< HEAD
    Route::put('/attendance/{absensi}', [AbsensiController::class, 'update'])->middleware('role:dosen');
    Route::delete('/attendance/{absensi}', [AbsensiController::class, 'destroy'])->middleware('role:dosen');
=======
>>>>>>> main
    
>>>>>>> main

    // Pengumuman — admin buat, semua role baca
    Route::get('/announcements', [PengumumanController::class, 'index']);
    Route::post('/announcements', [PengumumanController::class, 'store']);
    Route::get('/announcements/{pengumuman}', [PengumumanController::class, 'show']);
    Route::put('/announcements/{pengumuman}', [PengumumanController::class, 'update']);
    Route::delete('/announcements/{pengumuman}', [PengumumanController::class, 'destroy']);

    // User CRUD — khusus admin
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
});
