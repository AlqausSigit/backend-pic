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
        'kode_box' => 'required|string|unique:box_wadah',
        'jumlah_box' => 'required|integer|min:1'
    ]);

    $box = BoxWadah::create([
        'kode_box' => $request->kode_box,
        'jumlah_box' => $request->jumlah_box,
        'status' => $request->status ?? $request->status_box ?? 'tersedia'
    ]);

    return response()->json([
        'message' => 'Box berhasil ditambahkan',
        'data' => $box
    ]);
}

    public function update(Request $request, $id) {
        $box = BoxWadah::findOrFail($id);
        $data = $request->all();

        if (isset($data['status_box']) && !isset($data['status'])) {
            $data['status'] = $data['status_box'];
        }

        unset($data['status_box']);

        $box->update($data);
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
