<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalMenuController extends Controller
{
    public function index()
    {
        $jadwal = DB::table('jadwal_menu')->orderBy('tanggal', 'desc')->get();
        return response()->json($jadwal);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_menu' => 'required|string',
            'kalori' => 'nullable|integer',
            'protein' => 'nullable|integer'
        ]);

        $id = DB::table('jadwal_menu')->insertGetId([
            'tanggal' => $request->tanggal,
            'nama_menu' => $request->nama_menu,
            'kalori' => $request->kalori,
            'protein' => $request->protein,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Jadwal menu berhasil ditambahkan',
            'id' => $id
        ], 201);
    }

    public function hariIni()
    {
        $menu = DB::table('jadwal_menu')
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        return response()->json($menu);
    }
}
