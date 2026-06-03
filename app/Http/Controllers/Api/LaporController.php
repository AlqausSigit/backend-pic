<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/lapor",
     *     tags={"ATLAS"},
     *     summary="Lapor Wadah",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Berhasil")
     * )
     */
    public function store(Request $request)
{
    $request->validate([
        'box_id' => 'required',
        'keterangan' => 'required',
        'status' => 'required|in:rusak,hilang'
    ]);

    return response()->json([
        'message' => 'Laporan berhasil dikirim',
        'data' => $request->all()
    ]);
}
}
