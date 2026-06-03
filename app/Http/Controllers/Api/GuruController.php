<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        return Guru::orderBy('id')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:guru,kode',
            'nama' => 'required|string',
            'kategori' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $guru = Guru::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'kategori' => $request->kategori ?? 'Guru',
            'status' => $request->status ?? 'Aktif'
        ]);

        return response()->json([
            'message' => 'Guru berhasil ditambahkan',
            'data' => $guru
        ], 201);
    }

    public function show($id)
    {
        return Guru::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'kode' => 'nullable|string|unique:guru,kode,' . $guru->id,
            'nama' => 'nullable|string',
            'kategori' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $guru->update($request->only(['kode', 'nama', 'kategori', 'status']));

        return $guru;
    }

    public function destroy($id)
    {
        Guru::destroy($id);

        return response()->json(['message' => 'deleted']);
    }
}
