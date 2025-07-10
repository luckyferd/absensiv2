<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\JadwalController;

use App\Http\Controllers\SuratIzinController;
use App\Http\Controllers\AbsensiGuruController;
use App\Http\Controllers\AbsensiMuridController;
use App\Http\Controllers\DashboardGuruController;
use App\Http\Controllers\DashboardAdminController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::get('/dashboard-admin', [DashboardAdminController::class, 'index']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('kelas', KelasController::class);
});

/*
|--------------------------------------------------------------------------
| GURU
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:Guru'])->group(function () {
    Route::get('/dashboard-guru', [DashboardGuruController::class, 'index']);

    // Absensi oleh Guru
    Route::get('/guru/absensi-murid', [AbsensiMuridController::class, 'guruIndex']);
    Route::post('/guru/absensi-murid', [AbsensiMuridController::class, 'guruStoreBulk']);

    Route::apiResource('absensi-guru', AbsensiGuruController::class);

    // Verifikasi Surat Izin
    Route::post('/surat-izin/{id}/update-status', [SuratIzinController::class, 'updateStatus']);
});


// Surat izin (murid bisa store, guru bisa lihat)
// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::apiResource('surat-izin', SuratIzinController::class);
// });

// Route::middleware(['auth:sanctum', 'role:Murid'])->group(function () {
//     Route::get('/jadwal', [JadwalController::class, 'index']);          // 🔍 List semua jadwal
//     Route::post('/jadwal', [JadwalController::class, 'store']);         // ➕ Tambah jadwal
//     Route::get('/jadwal/{id}', [JadwalController::class, 'show']);      // 📄 Detail 1 jadwal (khusus guru pemilik)
//     Route::put('/jadwal/{id}', [JadwalController::class, 'update']);    // ✏️ Update jadwal
//     Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy']); // ❌ Hapus jadwal
// });


/*
|--------------------------------------------------------------------------
| MURID
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:Murid'])->group(function () {
    // Jadwal
    Route::get('/jadwal', [JadwalController::class, 'index']);
    Route::get('/jadwal/{id}', [JadwalController::class, 'show']);

    // Absensi diri sendiri
    Route::get('/murid/absensi', [AbsensiMuridController::class, 'index']);
    Route::get('/murid/absensi/{id}', [AbsensiMuridController::class, 'show']);
    Route::post('/murid/absensi', [AbsensiMuridController::class, 'store']);

    // Upload surat izin (termasuk foto/scan)
    Route::apiResource('surat-izin', SuratIzinController::class)->only(['index', 'store', 'show']);
});
