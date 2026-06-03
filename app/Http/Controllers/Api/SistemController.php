<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class SistemController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/backup",
     *     tags={"ATLAS"},
     *     summary="Backup Sistem",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Berhasil")
     * )
     */
    public function backup()
    {
        return response()->json(['message' => 'Proses backup sistem berhasil dijalankan.']);
    }
}
