<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasUuids;

    protected $table = 'tugas';
    protected $fillable = ['kelas_id', 'judul', 'instruksi', 'deadline'];
    protected $casts = ['deadline' => 'datetime'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }
}
