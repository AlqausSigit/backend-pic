<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index() {
        return Kelas::all();
    }

    /**
     * @OA\Post(
     *     path="/api/kelas",
     *     tags={"ATLAS"},
     *     summary="Tambah Kelas",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=201, description="Berhasil")
     * )
     */
    public function store(Request $request) {
        $request->validate([
            'nama_kelas' => 'required|string',
            'jurusan' => 'nullable|string',
            'jumlah_siswa' => 'nullable|integer',
            'wali_kelas' => 'nullable|string'
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'jurusan' => $request->jurusan ?? '-',
            'jumlah_siswa' => $request->jumlah_siswa ?? 0,
            'wali_kelas' => $request->wali_kelas ?? $request->waliKelas
        ]);

        return response()->json([
            'message' => 'Kelas berhasil ditambahkan',
            'data' => $kelas
        ], 201);
    }

    public function update(Request $request, $id) {
        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());
        return $kelas;
    }

    public function destroy($id) {
        Kelas::destroy($id);
        return response()->json(['message' => 'deleted']);
    }

    public function show($id)
{
    $kelas = Kelas::find($id);

    if (!$kelas) {
        return response()->json([
            'message' => 'Kelas tidak ditemukan'
        ], 404);
    }

    return response()->json($kelas);
}
}
