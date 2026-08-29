<?php

use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduleController;

// Health check
Route::get('/health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]));

//endpoint untuk mengambil jadwal tersedia
Route::get('/bookings', [BookingController::class, 'index']);

// endpoint untuk menyimpan booking baru
Route::post('/bookings', [BookingController::class, 'store']);

//public auth endpoint
Route::post('/auth/login', [AuthController::class, 'login']);

//protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
    Route::delete('/admin/bookings/{id}', [BookingController::class, 'destroy']);

    // Rute Manajemen Jadwal & Libur
    Route::get('/admin/schedules', [ScheduleController::class, 'getSchedules']);
    Route::put('/admin/schedules', [ScheduleController::class, 'updateSchedules']);
    Route::get('/admin/holidays', [ScheduleController::class, 'getHolidays']);
    Route::post('/admin/holidays', [ScheduleController::class, 'addHoliday']);
    Route::delete('/admin/holidays/{date}', [ScheduleController::class, 'removeHoliday']);
});