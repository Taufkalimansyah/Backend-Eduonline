<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasUuids;

    protected $table = 'materi';
    protected $fillable = ['kelas_id', 'judul', 'deskripsi', 'file_path', 'file_name', 'tipe'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
