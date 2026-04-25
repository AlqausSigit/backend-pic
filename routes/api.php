<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\BoxWadahController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LaporController;

/*
|--------------------------------------------------------------------------
| 🔓 PUBLIC
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| 🔒 PROTECTED (SANCTUM)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // AUTH
    Route::post('/logout', [AuthController::class, 'logout']);

    // ADMIN ROUTES
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'admin']);
        Route::get('/dashboard/gizi', [DashboardController::class, 'gizi']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('kelas', KelasController::class);
        Route::apiResource('box', BoxWadahController::class);
        Route::get('/transaksi', [TransaksiController::class, 'index']);
        Route::get('/monitoring', [TransaksiController::class, 'monitoring']);
        Route::get('/rekap', [TransaksiController::class, 'rekap']);
        Route::get('/downloadpdf', [TransaksiController::class, 'downloadpdf']);
    });

    // SISWA / PETUGAS ROUTES
    Route::middleware('role:siswa,petugas,admin')->group(function () {
        Route::get('/dashboard/siswa', [DashboardController::class, 'siswa']);
        Route::post('/menu/upload', [\App\Http\Controllers\Api\MenuHarianController::class, 'upload']);
        Route::post('/transaksi/ambil', [TransaksiController::class, 'ambil']);
        Route::put('/transaksi/{id}/kembali', [TransaksiController::class, 'kembali']);
        Route::post('/lapor', [LaporController::class, 'store']); // ✅ sudah clean
    });
});
