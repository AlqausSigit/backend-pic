<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class AktivitasController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/aktivitas",
     *     tags={"ATLAS"},
     *     summary="Riwayat Aktivitas",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Berhasil")
     * )
     */
    public function index()
    {
        $log = DB::table('log_aktivitas')->orderBy('created_at', 'desc')->get();
        return response()->json($log);
    }
}
