<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Booking;
use App\Models\User;
use App\Jobs\SendBookingReminder;
use Carbon\Carbon;

Schedule::call(function () {
    // Ambil pengaturan pengingat admin 
    $admin = User::first();
    $hours = $admin ? $admin->reminder_horus : 2;

    $today = Carbon::today()->toDateString();
    $now = Carbon::now()->format('H:1');
    $targetTime =Carbon::now()->addHours($hours)->format('H:i');

    $bookings = Booking::where('booking_date', $today)
        ->where('time_slot', '>=', $now)
        ->where('time_slot', '<=', $targetTime)
        ->where('reminder_sent', false)
        ->get();

    foreach ($bookings as $booking) {
        SendBookingReminder::dispatch($booking);
    }
})->everyFifteenMinutes();
