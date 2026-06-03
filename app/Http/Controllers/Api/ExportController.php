<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiMbg;

class ExportController extends Controller
{
    public function exportExcel()
    {
        // Simple CSV export since installing full Excel lib might take time
        $fileName = 'rekap_mbg_' . date('Ymd') . '.csv';
        $tasks = TransaksiMbg::with(['user', 'kelas'])->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Nama Siswa', 'Kelas', 'Jumlah Porsi', 'Tanggal', 'Status', 'Rating', 'Sisa Makanan');

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                fputcsv($file, array(
                    $task->id,
                    $task->user->nama ?? '-',
                    $task->kelas->nama_kelas ?? '-',
                    $task->jumlah_porsi,
                    $task->tanggal,
                    $task->status,
                    $task->rating ?? '-',
                    $task->sisa_makanan ?? '-'
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
