<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use App\Http\Requests\StoreBookingRequest;
use App\Services\BookingService;
use App\Http\Resources\BookingResource;

class BookingController extends Controller
{
    public function index(request $request) {
        // ambil request tanggal, kalau kosong otomatis pakai hari ini
        $date = $request -> input('date', Carbon::now()-> format('Y-m-d'));

        // mencari tahu jam berapa saja yang sudah dipesan pada tanggal tersebut
        $bookedSlots = Booking::where('booking_date', $date)
        ->pluck('time_slot')
        ->toArray();

        // hardcore jam oprasional dulu 
        $operationalHours = [
            '09:00', '10:00', '11:00', '12:00',
            '13:00', '14:00', '15:00', '16:00',
        ];

        $data = [];
        foreach ($operationalHours as $slot) {
            $data[] = [
                'time_slot' => $slot,
                // kalau jam ini ada di dalam array $bookedSlots, berarti sudah dipesan
                'status' => in_array($slot, $bookedSlots) ? 'booked' : 'available',
            ];
        }

        // kembalikan data dalam bentuk JSON
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(StoreBookingRequest $requsest, BookingService $BookingService) {
        // request sudah otomatis divalidasi oleh StoreBookingRequest

        // Lempar data ke service agar di handle logic anti double booking
        $booking = $BookingService->createBooking($requsest->validated());

        // kalau sukses, kembalikan response JSON
        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat',
            'data' => new BookingResource($booking),
        ], 201);
    }
}
