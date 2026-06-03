<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return response()->json([
        'name' => 'ATLAS MBG API',
        'status' => 'ok',
    ]);
});

// GET semua user
Route::get('/users', [UserController::class, 'index']);

// GET user by id
Route::get('/users/{id}', [UserController::class, 'show']);

// POST tambah user
Route::post('/users', [UserController::class, 'store']);

// PUT update user
Route::put('/users/{id}', [UserController::class, 'update']);

// DELETE user
Route::delete('/users/{id}', [UserController::class, 'destroy']);
