<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request) {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));

        $bookings = Booking::where('booking_date', $date)
            ->select('id', 'customer_name', 'customer_phone', 'time_slot', 'booking_date', 'created_at')
            ->orderBy('time_slot')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'today_bookings' => $bookings,
                'total_bookings' => $bookings->count(),
                'date' => $date,
            ]
        ]);
    }
}
