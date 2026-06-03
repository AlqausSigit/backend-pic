<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\BoxWadahController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JadwalMenuController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\LaporController;
use App\Http\Controllers\Api\GuruController;

/*
|--------------------------------------------------------------------------
| 🔓 PUBLIC
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/dashboard/rekap', [TransaksiController::class, 'rekap']);
Route::get('/downloadpdf', [TransaksiController::class, 'downloadpdf']);


/*
|--------------------------------------------------------------------------
| 🔒 PROTECTED (SANCTUM)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // AUTH
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ADMIN & PETUGAS ROUTES (CRUD)
    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'admin']);
        Route::get('/dashboard/gizi', [DashboardController::class, 'gizi']);
        
        // Dashboard Stats
        Route::get('/dashboard/total-siswa', [DashboardController::class, 'admin']);
        Route::get('/dashboard/sudah-mengambil', [DashboardController::class, 'admin']);
        Route::get('/dashboard/belum-mengambil', [DashboardController::class, 'admin']);
        Route::get('/dashboard/rantang-kembali', [DashboardController::class, 'admin']);
        
        // Manajemen Pengguna & Kelas
        Route::apiResource('users', UserController::class);
        Route::get('/cek-siswa', [UserController::class, 'cekSiswa']);
        Route::apiResource('kelas', KelasController::class);
        Route::apiResource('guru', GuruController::class);
        
        // Manajemen Wadah
        Route::apiResource('wadah', BoxWadahController::class);
        Route::put('/wadah/{id}/status', [BoxWadahController::class, 'update']);
        Route::apiResource('box', BoxWadahController::class); // legacy
        
        // Jadwal Menu (Advanced)
        Route::apiResource('jadwal-menu', JadwalMenuController::class);
        
        // Presensi Siswa
        Route::post('/presensi', [PresensiController::class, 'store']);
        Route::get('/presensi', [PresensiController::class, 'index']);
        
        Route::get('/mbg/ringkasan', [TransaksiController::class, 'index']);
        Route::get('/mbg/per-kelas', [TransaksiController::class, 'rekap']);
        Route::delete('/mbg/ambil/{id}', [TransaksiController::class, 'kembali']);
        Route::delete('/mbg/kembali/{id}', [TransaksiController::class, 'kembali']);
        
        Route::get('/transaksi', [TransaksiController::class, 'index']);
        Route::get('/monitoring', [TransaksiController::class, 'monitoring']);
        Route::get('/rekap', [TransaksiController::class, 'rekap']);
        Route::get('/downloadpdf', [TransaksiController::class, 'downloadpdf']);
        Route::get('/export/excel', [ExportController::class, 'exportExcel']);
        
        // Aktivitas & Notifikasi
        Route::get('/aktivitas', [\App\Http\Controllers\Api\AktivitasController::class, 'index']);
        Route::get('/notifikasi', [\App\Http\Controllers\Api\AktivitasController::class, 'index']);
        
        // Sistem
        Route::get('/sistem', [\App\Http\Controllers\Api\SistemController::class, 'backup']);
        Route::post('/sistem/backup', [\App\Http\Controllers\Api\SistemController::class, 'backup']);
        Route::post('/backup', [\App\Http\Controllers\Api\SistemController::class, 'backup']); // legacy
        Route::put('/sistem/update', [\App\Http\Controllers\Api\SistemController::class, 'backup']);
        Route::delete('/sistem/backup/{id}', [\App\Http\Controllers\Api\SistemController::class, 'backup']);
    });

    // SISWA / PETUGAS ROUTES
    Route::middleware('role:siswa,petugas,admin')->group(function () {
        Route::get('/dashboard/siswa', [DashboardController::class, 'siswa']);
        Route::post('/lapor', [LaporController::class, 'store']);
        
        // MBG Ambil / Kembali (Semua Role Bisa)
        Route::post('/mbg/ambil', [TransaksiController::class, 'ambil']);
        Route::put('/mbg/ambil/{id}', [TransaksiController::class, 'kembali']);
        Route::post('/mbg/kembali', [TransaksiController::class, 'kembaliLegacy']);
        Route::post('/mbg/kembali/{id}', [TransaksiController::class, 'kembali']);
        Route::put('/mbg/kembali/{id}', [TransaksiController::class, 'kembali']);
        
        // AI Features (Advanced)
        Route::post('/mbg/waste-detect', [TransaksiController::class, 'detectWaste']);
        Route::post('/mbg/calorie-detect', [TransaksiController::class, 'detectCalorie']);
        
        // Legacy (supaya app sebelumnya tidak error)
        Route::post('/transaksi/ambil', [TransaksiController::class, 'ambil']);
        Route::put('/transaksi/{id}/kembali', [TransaksiController::class, 'kembali']);
        Route::post('/menu/upload', [\App\Http\Controllers\Api\MenuHarianController::class, 'upload']);
        
        // Transaksi Siswa & Feedback
        Route::post('/mbg/feedback/{transaksi_id}', [TransaksiController::class, 'feedback']);
        Route::get('/menu-hari-ini', [JadwalMenuController::class, 'hariIni']);
    });
});
