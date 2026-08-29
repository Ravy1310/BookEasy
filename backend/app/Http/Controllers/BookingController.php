<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Holiday;
use Carbon\Carbon;
use App\Http\Requests\StoreBookingRequest;
use App\Services\BookingService;
use App\Services\WhatsAppService;
use App\Http\Resources\BookingResource;

class BookingController extends Controller
{
    public function index(request $request) {

        $dateParam = $request->query('date', Carbon::today()->toDateString());
        $date = Carbon::parse($dateParam);

        // Verivikasi Hari Libur Nasional / Cuti
        $isHoliday = Holiday::where('date', $date->toDateString())->exists();
        if($isHoliday) {
            // Slot tidak muncul ( array kosong) jika hari tersebut adalah hari libur
            return response()->json([
                'success' => true,
                'data' => []
            ], 200);
        }

        // Verifikasi Jadwal Operasional Reguler
        // Carbonn dayOfWeekIso: 1 ( senin) - 7(minggu). Mapping kita: 0 (senin) - 6(minggu)
        $dayOfWeek = $date->dayOfWeekIso - 1;
        $schedule = Schedule::where('day_of_week', $dayOfWeek)->first();

        // Slot tidak muncul jika jadwal tidak di temukan atau admin menetapkan is_closed = true
        if(!$schedule || $schedule->is_closed) {
            return response()->json ([
                'success' => true,
                'data' => []
            ], 200);
        }

        // Bangun Kumpulan Slot Dinamis
        $slot = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        $bookedSlots = Booking::where('booking_date', $date->toDateString())
                            ->pluck('time_slot')
                            ->toArray();

        // Generate slot dengan interval 1 jam
        while($startTime < $endTime) {
            $timeString = $startTime->format('H:i');
            
            $slots[] = [
                'time_slot' => $timeString,
                'status' => in_array($timeString, $bookedSlots) ? 'booked' : 'available'
            ];

            $startTime->addHour();
        }

        return response()->json([
            'success' => true,
            'data' => $slots
        ], 200);

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

    public function store(Request $request, WhatsAppService $waService) {
        // validasi input dasar
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'time_slot' => 'required|date_format:H:i',
            'booking_date' => 'nullable|date',
        ]);

        $date = $validated['booking_date'] ?? Carbon::today()->toDateString();

        $booking = Booking::create([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'time_slot' => $validated['time_slot'],
            'booking_date' => $date,
        ]);

        // pemicu notifikasi WhatsApp otomatis ke pelanggan
        $waSent = $waService->sendBookingConfirmation(
            $booking->customer_name,
            $booking->customer_phone,
            \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y'),
            $booking->time_slot
        );

        return response()->json([
            'success' => true,
            'data' => $booking
        ], 201);
    }
}
