<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiMbg;
use App\Models\BoxWadah;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    // ======================
    // 📋 LIST DATA
    // ======================
    public function index()
    {
        return TransaksiMbg::with(['kelas','user','box'])->get();
    }

    // ======================
    // 🍱 AMBIL MBG
    // ======================
    public function ambil(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // 🔒 VALIDASI
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'jumlah_porsi' => 'required|integer|min:1'
        ]);

        // ❌ CEK DOUBLE
        $cek = TransaksiMbg::where('kelas_id', $request->kelas_id)
            ->whereDate('tanggal', today())
            ->exists();

        if ($cek) {
            return response()->json([
                'message' => 'Kelas sudah mengambil hari ini!'
            ], 400);
        }

        // 📦 AMBIL BOX TERSEDIA
        $box = BoxWadah::where('status_box', 'tersedia')->first();

        if (!$box) {
            return response()->json([
                'message' => 'Box tidak tersedia'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // ✅ SIMPAN TRANSAKSI
            $trx = TransaksiMbg::create([
                'user_id' => $user->id,
                'kelas_id' => $request->kelas_id,
                'box_id' => $box->id,
                'jumlah_porsi' => $request->jumlah_porsi,
                'tanggal' => now()->toDateString(),
                'jam_ambil' => now()->toTimeString(),
                'status' => 'diambil'
            ]);

            // 🔄 UPDATE BOX
            $box->update([
                'status_box' => 'dipakai'
            ]);

            // 🧾 LOG AKTIVITAS
            DB::table('log_aktivitas')->insert([
                'user_id' => $user->id,
                'aktivitas' => 'User '.$user->nama.' mengambil MBG untuk kelas ID '.$request->kelas_id,
                'tanggal' => now()->toDateString(),
                'waktu' => now()->toTimeString(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Berhasil ambil MBG',
                'data' => $trx
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal mengambil MBG',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ======================
    // 🔄 KEMBALIKAN BOX
    // ======================
    public function kembali($id)
    {
        $trx = TransaksiMbg::with('box')->findOrFail($id);

        // ❌ CEK SUDAH DIKEMBALIKAN
        if ($trx->status == 'dikembalikan') {
            return response()->json([
                'message' => 'Box sudah dikembalikan sebelumnya'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // ✅ UPDATE TRANSAKSI
            $trx->update([
                'jam_kembali' => now()->toTimeString(),
                'status' => 'dikembalikan'
            ]);

            // 🔄 BALIKIN BOX
            $trx->box->update([
                'status_box' => 'tersedia'
            ]);

            // 🧾 LOG AKTIVITAS
            DB::table('log_aktivitas')->insert([
                'user_id' => $trx->user_id,
                'aktivitas' => 'User ID '.$trx->user_id.' mengembalikan box ID '.$trx->box_id,
                'tanggal' => now()->toDateString(),
                'waktu' => now()->toTimeString(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Berhasil dikembalikan'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal mengembalikan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ======================
    // 📊 MONITORING
    // ======================
    public function monitoring()
    {
        $data = TransaksiMbg::with('kelas')
            ->whereDate('tanggal', today())
            ->get();

        return response()->json([
            'total_kelas_sudah_ambil' => $data->unique('kelas_id')->count(),
            'total_porsi' => $data->sum('jumlah_porsi'),
            'data' => $data
        ]);
    }

    // ======================
    // 📈 REKAP DATA
    // ======================
    public function rekap(Request $request)
    {
        $query = TransaksiMbg::query();

        if ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $data = $query
            ->selectRaw('kelas_id, COUNT(*) as total_transaksi, SUM(jumlah_porsi) as total_porsi')
            ->groupBy('kelas_id')
            ->with('kelas')
            ->get();

        $total_semua = [
            'total_transaksi' => $query->count(),
            'total_porsi' => $query->sum('jumlah_porsi')
        ];

        return response()->json([
            'message' => 'Data rekap berhasil diambil',
            'summary' => $total_semua,
            'data' => $data
        ]);
    }

    // ======================
    // 📄 DOWNLOAD PDF
    // ======================
    public function downloadpdf(Request $request)
    {
        $query = TransaksiMbg::query();

        if ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $data = $query
            ->selectRaw('kelas_id, COUNT(*) as total_transaksi, SUM(jumlah_porsi) as total_porsi')
            ->groupBy('kelas_id')
            ->with('kelas')
            ->get();

        $total_semua = [
            'total_transaksi' => $query->count(),
            'total_porsi' => $query->sum('jumlah_porsi')
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rekap', compact('data', 'total_semua'));
        
        return $pdf->download('rekap_mbg.pdf');
    }
}