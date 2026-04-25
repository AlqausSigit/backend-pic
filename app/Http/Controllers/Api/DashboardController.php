<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\BoxWadah;
use App\Models\TransaksiMbg;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "L5 Swagger OpenApi description",
    title: "Backend PIC API Documentation"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "apiKey",
    name: "Authorization",
    in: "header"
)]
class DashboardController extends Controller
{
    public function admin()
    {
        return response()->json([
            'total_user' => User::count(),
            'total_transaksi' => \App\Models\TransaksiMbg::count(),
            'total_kalori' => \App\Models\MenuHarian::sum('kalori')
        ]);
    }

    public function siswa()
    {
        $userId = auth()->id();

        return response()->json([
            'menu_hari_ini' => \App\Models\MenuHarian::where('user_id', $userId)
                ->whereDate('tanggal', now())
                ->get(),

            'total_kalori' => \App\Models\MenuHarian::where('user_id', $userId)
                ->sum('kalori')
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/gizi",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     summary="Grafik gizi siswa",
     *     @OA\Response(response=200, description="Data grafik")
     * )
     */
    public function gizi()
    {
        $data = \Illuminate\Support\Facades\DB::table('menu_harian')
            ->selectRaw('DATE(tanggal) as tanggal,
                SUM(kalori) as kalori,
                SUM(protein) as protein,
                SUM(lemak) as lemak,
                SUM(karbohidrat) as karbohidrat')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return response()->json($data);
    }
}
