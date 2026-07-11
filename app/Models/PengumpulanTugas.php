<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    use HasUuids;

    protected $table = 'pengumpulan_tugas';
    protected $fillable = [
        'tugas_id', 'mahasiswa_id', 'file_path', 'file_name',
        'status', 'submitted_at', 'nilai', 'feedback',
    ];
    protected $casts = ['submitted_at' => 'datetime'];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
