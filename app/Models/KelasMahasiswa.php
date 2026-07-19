<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class KelasMahasiswa extends Pivot
{
    use HasUuids;

    protected $table = 'kelas_mahasiswa';
}