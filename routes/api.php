<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CutiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Route
Route::post('/login', [AuthController::class, 'login']);
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Protected Routes (Harus Login)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route untuk Employee memantau status 
    Route::get('/my-leaves', [CutiController::class, 'index']);
    // Route untuk Employee mengajukan cuti
    Route::post('/leave-requests', [CutiController::class, 'store']);
    // Route untuk Employee memperbarui pengajuan cuti
    Route::put('leaves/{id}', [CutiController::class, 'update']);
    // Route untuk Employee menghapus pengajuan cuti
    Route::delete('leaves/{id}', [CutiController::class, 'destroy']);
});
// Protected Routes untuk Admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // route untuk Admin melihat semua pengajuan cuti
    Route::get('/admin/leaves', [CutiController::class, 'allRequests']);
    // route untuk Admin memperbarui status pengajuan cuti
    Route::patch('/admin/leaves/{id}/status', [CutiController::class, 'updateStatus']);
});
