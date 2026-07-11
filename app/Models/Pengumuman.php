<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasUuids;

    protected $table = 'pengumuman';
    protected $fillable = ['author_id', 'judul', 'isi'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
