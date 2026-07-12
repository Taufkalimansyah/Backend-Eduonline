<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengumuman extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'isi',
        'pembuat_id',
        'tanggal',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }
}