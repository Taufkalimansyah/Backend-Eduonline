<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasUuids;

    protected $table = 'absensi';

    protected $fillable = [
        'kelas_id',
        'sesi_id',
        'mahasiswa_id',
        'pertemuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu_mulai',
        'waktu_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function mahasiswa() { return $this->belongsTo(User::class, 'mahasiswa_id'); }

    // relasi ke baris sesi induk (kalau baris ini adalah isian mahasiswa)
    public function sesi() { return $this->belongsTo(Absensi::class, 'sesi_id'); }

    // relasi ke semua isian mahasiswa (kalau baris ini adalah sesi)
    public function isian() { return $this->hasMany(Absensi::class, 'sesi_id'); }

    public function scopeSesi($query) { return $query->whereNull('sesi_id'); }
}