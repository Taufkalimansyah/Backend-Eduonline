<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NilaiAkhir extends Model
{
    use HasUuids;

    protected $table = 'nilai_akhir';
    protected $fillable = ['kelas_id', 'mahasiswa_id', 'nilai_tugas', 'nilai_kuis', 'nilai_ujian'];
    protected $appends = ['rata_rata']; // TAMBAHKAN INI

    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function mahasiswa() { return $this->belongsTo(User::class, 'mahasiswa_id'); }

    public function getRataRataAttribute(): ?float
    {
        $vals = array_filter([$this->nilai_tugas, $this->nilai_kuis, $this->nilai_ujian], fn ($v) => $v !== null);
        return count($vals) ? round(array_sum($vals) / count($vals), 1) : null;
    }
}