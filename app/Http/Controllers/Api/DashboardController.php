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
    /**
     * @OA\Get(
     *     path="/api/dashboard/admin",
     *     tags={"ATLAS"},
     *     summary="Dashboard",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Berhasil")
     * )
     */
    public function admin()
    {
        $today = now()->toDateString();
        $classes = Kelas::all();
        $totalSiswa = (int) $classes->sum('jumlah_siswa');
        if ($totalSiswa === 0) {
            $totalSiswa = User::where('role', 'siswa')->count();
        }

        $todayTxs = TransaksiMbg::whereDate('tanggal', $today)->get();
        $sudahMengambil = (int) $todayTxs->sum('jumlah_porsi');
        $belumMengambil = max(0, $totalSiswa - $sudahMengambil);
        $rantangKembali = (int) $todayTxs
            ->where('status', 'dikembalikan')
            ->sum('jumlah_porsi');

        $chartGizi = $classes->map(function ($c) use ($todayTxs) {
            $classTx = $todayTxs->firstWhere('kelas_id', $c->id);
            $sudah = $classTx ? (int) $classTx->jumlah_porsi : 0;
            $total = (int) $c->jumlah_siswa;
            return [
                'kelas' => $c->nama_kelas,
                'total' => $total,
                'sudah' => $sudah,
                'belum' => max(0, $total - $sudah),
            ];
        })->values();

        $sudahPercent = $totalSiswa > 0 ? (int) round(($sudahMengambil / $totalSiswa) * 100) : 0;

        return response()->json([
            'totalSiswa' => $totalSiswa,
            'sudahMengambil' => $sudahMengambil,
            'belumMengambil' => $belumMengambil,
            'rantangKembali' => $rantangKembali,
            'chartRingkasan' => [
                'sudahMengambilPercent' => $sudahPercent,
                'belumMengambilPercent' => 100 - $sudahPercent,
                'sudahMengambilCount' => $sudahMengambil,
                'belumMengambilCount' => $belumMengambil,
            ],
            'chartGizi' => $chartGizi,
            'total_user' => User::count(),
            'total_transaksi' => TransaksiMbg::count(),
        ]);
    }

    public function siswa()
    {
        $userId = auth()->id();

        // 1. Total Diambil
        $totalAmbil = TransaksiMbg::where('user_id', $userId)
            ->whereIn('status', ['diambil', 'dikembalikan'])
            ->count();

        // 2. Kalori Minggu Ini
        $kaloriMinggu = (int) \App\Models\MenuHarian::where('user_id', $userId)
            ->whereBetween('tanggal', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString()
            ])
            ->sum('kalori');

        // 3. Health Score
        $txs = TransaksiMbg::where('user_id', $userId)
            ->whereIn('status', ['diambil', 'dikembalikan'])
            ->get();
        if ($txs->isEmpty()) {
            $healthScore = 0;
        } else {
            $scores = $txs->map(function ($t) {
                if ($t->sisa_makanan === 'habis') return 100;
                if ($t->sisa_makanan === 'separuh') return 50;
                if ($t->sisa_makanan === 'utuh') return 10;
                return 90; // default/fallback
            });
            $healthScore = (int) round($scores->average());
        }

        // 4. Hari Ini Nutrition (Karbohidrat, Protein, Lemak)
        $todayNutrition = \App\Models\MenuHarian::where('user_id', $userId)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        // 5. Menu Hari Ini (for display)
        $todayMenu = \Illuminate\Support\Facades\DB::table('jadwal_menu')
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        // 6. History logs
        $history = [];
        foreach ($txs as $t) {
            $formattedDate = \Carbon\Carbon::parse($t->tanggal)->locale('id')->isoFormat('D MMMM YYYY');
            if ($t->tanggal === now()->toDateString()) {
                $formattedDate = 'Hari ini';
            } elseif ($t->tanggal === now()->subDay()->toDateString()) {
                $formattedDate = 'Kemarin';
            }

            if ($t->jam_ambil) {
                $history[] = [
                    'type' => 'ambil',
                    'label' => 'Ambil MBG',
                    'time' => substr($t->jam_ambil, 0, 5),
                    'date' => $formattedDate,
                    'timestamp' => $t->tanggal . ' ' . $t->jam_ambil
                ];
            }

            if ($t->rating) {
                $history[] = [
                    'type' => 'feedback',
                    'label' => 'Beri Umpan Balik (Rating: ' . $t->rating . '/5)',
                    'time' => $t->updated_at->format('H:i'),
                    'date' => $formattedDate,
                    'timestamp' => $t->updated_at->toDateTimeString()
                ];
            }

            if ($t->jam_kembali) {
                $history[] = [
                    'type' => 'kembali',
                    'label' => 'Kembalikan wadah',
                    'time' => substr($t->jam_kembali, 0, 5),
                    'date' => $formattedDate,
                    'timestamp' => $t->tanggal . ' ' . $t->jam_kembali
                ];
            }
        }

        // Sort history by timestamp descending
        usort($history, function ($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });

        return response()->json([
            'total_ambil' => $totalAmbil,
            'kalori_minggu' => $kaloriMinggu,
            'health_score' => $healthScore,
            'today_nutrition' => [
                'karbohidrat' => $todayNutrition ? (int) $todayNutrition->karbohidrat : 0,
                'protein' => $todayNutrition ? (int) $todayNutrition->protein : 0,
                'lemak' => $todayNutrition ? (int) $todayNutrition->lemak : 0,
                'serat' => $todayNutrition ? 18 : 0, // default fiber estimate
            ],
            'menu_hari_ini' => $todayMenu ? [
                'menu' => $todayMenu->nama_menu,
                'kalori' => $todayMenu->kalori ? $todayMenu->kalori . ' kkal' : '0 kkal',
            ] : null,
            'history' => $history
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
