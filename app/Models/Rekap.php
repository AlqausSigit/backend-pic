<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekap extends Model
{
    protected $table = 'rekap';

    protected $fillable = [
        'tanggal',
        'total_porsi',
        'total_kelas'
    ];
}