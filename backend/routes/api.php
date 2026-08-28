<?php

use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//endpoint untuk mengambil jadwal tersedia
Route::get('/bookings', [BookingController::class, 'index']);

// endpoint untuk menyimpan booking baru
Route::post('/bookings', [BookingController::class, 'store']);