<?php

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('validasi gagal jika field wajib kosong(error 422)', function () {
    // 1. Skenario 1: Coba Kirim data kosong[cite: 2]
    $response = $this->postJson('/api/bookings', []);

    //harusnya ditolak dengan status 422 dan pesan error untuk semua kolom
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['customer_name', 'customer_phone', 'time_slot', 'booking_date']);

});

test('Berhasil melakukan booking jadwal kosong (happy path)', function () {
    //Skenario 2: Data lengkap dan valid[cite: 2]
    $response = $this->postJson('/api/bookings', [
        'customer_name' => 'Siti',
        'customer_phone' => '08123456789',
        'time_slot'     => '10:00',
        'booking_date'  => '2026-08-30'
    ]);

    //harusnya suskes (201 Created)
    $response->assertStatus(201)
            ->assertJsonPath('success', true);

    //memastikan data benar benar masuk database
    expect(Booking::where('customer_name', 'Siti')->exists())->toBeTrue();
});

test('Menolak booking pada slot yang sudah terisi (conflict case)', function () {
    //Skenario 3: Slot sudah ada yang punya[cite: 2]

    Booking::factory()->create([
        'time_slot' => '14:00',
        'booking_date' => '2026-08-30'
    ]);

    //Seseorang mencoba jam 14:00 di tanggal yang sama
    $response = $this->postJson('/api/bookings', [
        'customer_name' => 'Joko',
        'customer_phone' => '08987654321',
        'time_slot' => '14:00',
        'booking_date' => '2026-08-30'
    ]);

    //Seharusnya ditolak dengan error validasi di bagian time_slot
    $response->assertStatus(422)
            ->assertJsonValidationErrors(['time_slot']);
});

test('mencegah double booking berurutan (concerrency case)', function () {
    //Skenario 4: Simulasi request ganda ke slot yang sama persis
    $payload = [
        'customer_name' => 'Andi',
        'customer_phone' => '08111111111',
        'time_slot' => '15:00',
        'booking_date' => '2026-08-30'
    ];

    //request pertama (Andi) masuk dan sukes
    $this->postJson('/api/bookings', $payload)->assertStatus(201);

    //Request kedua (orang lain pakai bot/klick double) masuk sekian milidetik kemudian
    //Harusnya langsung ditolak karena slot sudah dikunci oleh transaksi pertama
    $this->postJson('/api/bookings', $payload)->assertStatus(422);

    //memastikan di database benar benar hanya ada 1 data (andi saja)
    expect(Booking::where('time_slot', '15:00')->count())->toBe(1);
});
