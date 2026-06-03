<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Kelas;
use App\Models\BoxWadah;

class TransaksiMbg extends Model
{
    protected $table = 'transaksi_mbgs';

    protected $fillable = [
        'user_id',
        'kelas_id',
        'box_id',
        'jumlah_porsi',
        'tanggal',
        'jam_ambil',
        'jam_kembali',
        'status',
        'catatan',
        'foto_makanan',
        'kalori_ai',
        'waste_level',
        'rating',
        'sisa_makanan',
        'komentar_siswa'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function box()
    {
        return $this->belongsTo(BoxWadah::class, 'box_id');
    }

    public function menu()
    {
        return $this->hasOne(MenuHarian::class, 'transaksi_id');
    }
}
