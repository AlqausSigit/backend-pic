<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuHarian;
use App\Services\FoodDetectionService;
use App\Services\NutritionService;
use OpenApi\Annotations as OA;

class MenuHarianController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/menu/upload",
     *     tags={"Menu"},
     *     security={{"bearerAuth":{}}},
     *     summary="Upload foto makanan",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"foto"},
     *                 @OA\Property(property="foto", type="string", format="binary"),
     *                 @OA\Property(property="transaksi_id", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Data menu tersimpan")
     * )
     */
    public function upload(Request $request)
    {
        $request->validate([
            'foto' => 'required|image'
        ]);

        $path = $request->file('foto')->store('menu', 'public');

        $food = app(FoodDetectionService::class)->detect($path);
        $gizi = app(NutritionService::class)->get($food);

        $data = MenuHarian::create([
            'user_id' => auth()->id(),
            'transaksi_id' => $request->transaksi_id,
            'foto' => $path,
            'nama_menu' => $food,
            'kalori' => $gizi['kalori'],
            'protein' => $gizi['protein'],
            'lemak' => $gizi['lemak'],
            'karbohidrat' => $gizi['karbohidrat'],
            'tanggal' => now()
        ]);

        return response()->json([
            'message' => 'Deteksi berhasil',
            'data' => $data
        ]);
    }
}
