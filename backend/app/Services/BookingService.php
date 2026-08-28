<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\UniqueConstraintViolationException; // Import Exception bawaan DB

class BookingService
{
    public function createBooking(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            // 1. Seragamkan format tanggal untuk amannya (YYYY-MM-DD)
            $bookingDate = date('Y-m-d', strtotime($data['booking_date']));

            // Pake whereDate agar lebih kebal dari isu format jam bawaan database di environment testing
            $existing = Booking::whereDate('booking_date', $bookingDate)
                ->where('time_slot', $data['time_slot'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'time_slot' => 'Yah, slot ini baru saja dipesan orang lain. Coba pilih jam lain ya.'
                ]);
            }

            // 2. Sabuk pengaman ganda: Jika entah bagaimana lolos (misal saat testing), 
            // tangkap error dari database dan ubah jadi error validasi (422) yang ramah untuk frontend[cite: 3, 6].
            try {
                return Booking::create([
                    'customer_name'  => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'time_slot'      => $data['time_slot'],
                    'booking_date'   => $bookingDate,
                ]);
            } catch (UniqueConstraintViolationException $e) {
                throw ValidationException::withMessages([
                    'time_slot' => 'Yah, slot ini baru saja dipesan orang lain. Coba pilih jam lain ya.'
                ]);
            }
        });
    }
}