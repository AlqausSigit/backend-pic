<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoxWadah extends Model
{
    protected $table = 'box_wadah';

    protected $fillable = [
    'jumlah_box',
    'status_box'
];
}
