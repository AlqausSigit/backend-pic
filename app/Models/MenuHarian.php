<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuHarian extends Model
{
    protected $table = 'menu_harian';

    protected $fillable = [
        'user_id',
        'transaksi_id',
        'foto',
        'nama_menu',
        'kalori',
        'protein',
        'lemak',
        'karbohidrat',
        'tanggal'
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiMbg::class, 'transaksi_id');
    }
}
