<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoxWadah;
use Illuminate\Http\Request;

class BoxWadahController extends Controller
{
    public function index() {
        return BoxWadah::all();
    }

    public function store(Request $request)
{
    // VALIDASI
    $request->validate([
        'jumlah_box' => 'required|integer|min:1'
    ]);

    $box = BoxWadah::create([
        'jumlah_box' => $request->jumlah_box,
        'status_box' => 'tersedia'
    ]);

    return response()->json([
        'message' => 'Box berhasil ditambahkan',
        'data' => $box
    ]);
}

    public function update(Request $request, $id) {
        $box = BoxWadah::findOrFail($id);
        $box->update($request->all());
        return $box;
    }

    public function destroy($id) {
        BoxWadah::destroy($id);
        return response()->json(['message' => 'deleted']);
    }

    public function show($id) {
        $box = BoxWadah::find($id);

        if (!$box) {
            return response()->json([
                'message' => 'Box tidak ditemukan'
            ], 404);
        }

        return response()->json($box);
    }
}
