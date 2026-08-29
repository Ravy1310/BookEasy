<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Holiday;
use Carbon\Carbon;
use App\Services\WhatsAppService;

class BookingController extends Controller
{
    public function index(Request $request) {
        $dateParam = $request->query('date', Carbon::today()->toDateString());
        $date = Carbon::parse($dateParam);
        $dateStr = $date->toDateString();

        // Cek hari libur
        if (Holiday::where('date', $dateStr)->exists()) {
            return response()->json(['success' => true, 'data' => []], 200);
        }

        // Cek jadwal operasional
        $dayOfWeek = $date->dayOfWeekIso - 1;
        $schedule = Schedule::where('day_of_week', $dayOfWeek)
            ->select('start_time', 'end_time', 'is_closed')
            ->first();

        if (!$schedule || $schedule->is_closed) {
            return response()->json(['success' => true, 'data' => []], 200);
        }

        // Ambil booked slots dalam satu query
        $bookedSlots = Booking::where('booking_date', $dateStr)
            ->pluck('time_slot')
            ->toArray();

        // Generate slot
        $slots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        while ($startTime < $endTime) {
            $timeString = $startTime->format('H:i');
            $slots[] = [
                'time_slot' => $timeString,
                'status' => in_array($timeString, $bookedSlots) ? 'booked' : 'available'
            ];
            $startTime->addHour();
        }

        return response()->json(['success' => true, 'data' => $slots], 200);
    }

    public function store(Request $request, WhatsAppService $waService) {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'time_slot' => 'required|date_format:H:i',
            'booking_date' => 'nullable|date',
        ]);

        $date = $validated['booking_date'] ?? Carbon::today()->toDateString();

        // Cek slot sudah terisi (conflict check sebelum insert)
        $exists = Booking::where('booking_date', $date)
            ->where('time_slot', $validated['time_slot'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Slot waktu sudah terisi.'
            ], 409);
        }

        $booking = Booking::create([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'time_slot' => $validated['time_slot'],
            'booking_date' => $date,
        ]);

        // WhatsApp notification (fire & forget)
        try {
            $waService->sendBookingConfirmation(
                $booking->customer_name,
                $booking->customer_phone,
                Carbon::parse($booking->booking_date)->translatedFormat('d F Y'),
                $booking->time_slot
            );
        } catch (\Exception $e) {
            // WhatsApp gagal tidak membatalkan booking
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ], 201);
    }

    public function destroy($id) {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan.',
        ], 200);
    }
}
