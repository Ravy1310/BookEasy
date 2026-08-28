<?php

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// pastikan DB dibersihkan setiap kali test dijalankan
uses(TestCase::class, RefreshDatabase::class);

test('cuma satu booking yang berhasil kalau rebutan slot yang sama', function () {
    $service = new BookingService();

    $data = [
        "customer_name" => 'Budi Haryanto',
        'customer_phone' => '08123455678',
        'time_slot' => '10:00',
        'booking_date' => '2026-08-25',
    ];

    // pemanggilan pertama 
    $service->createBooking($data);

    //pemanggilan kedua 
    // seharusnya ditolak dan melempar ValidationException
    expect(fn() => $service-> createBooking($data))->toThrow(ValidationException::class);

    // memastikan hanya ada 1 data booking di database
    expect(Booking::count())->toBe(1);
});
