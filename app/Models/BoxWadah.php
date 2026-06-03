<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoxWadah extends Model
{
    protected $table = 'box_wadah';

    protected $appends = [
        'status_box'
    ];

    protected $fillable = [
        'kode_box',
        'jumlah_box',
        'status'
    ];

    public function getStatusBoxAttribute()
    {
        return $this->status;
    }
}
