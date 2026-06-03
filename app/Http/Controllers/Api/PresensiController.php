<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    public function index()
    {
        $presensi = DB::table('presensi_siswa')
            ->join('users', 'presensi_siswa.user_id', '=', 'users.id')
            ->select('presensi_siswa.*', 'users.nama as nama_siswa')
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return response()->json($presensi);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,absen,sakit,izin'
        ]);

        DB::table('presensi_siswa')->updateOrInsert(
            ['user_id' => $request->user_id, 'tanggal' => $request->tanggal],
            ['status' => $request->status, 'updated_at' => now(), 'created_at' => now()]
        );

        return response()->json(['message' => 'Data presensi berhasil disimpan']);
    }
}
