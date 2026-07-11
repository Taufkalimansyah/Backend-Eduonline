<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasUuids;

    protected $table = 'absensi';
    protected $fillable = ['kelas_id', 'mahasiswa_id', 'tanggal', 'status'];
    protected $casts = ['tanggal' => 'date'];

    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function mahasiswa() { return $this->belongsTo(User::class, 'mahasiswa_id'); }
}
