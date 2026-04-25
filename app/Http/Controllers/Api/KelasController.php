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

    public function store(Request $request) {
        return Kelas::create($request->all());
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
