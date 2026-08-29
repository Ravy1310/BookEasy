<?php

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('dashboard return booking hari ini jika terautentikasi', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Booking::factory()->count(2)->create([
        'booking_date' => now()->format('Y-m-d'),
    ]);

    $response = $this->getJson('/api/dashboard');

    $response->assertStatus(200)
             ->assertJsonPath('success', true)
             ->assertJsonStructure([
                 'data' => [
                     'today_bookings',
                     'total_bookings',
                     'date',
                 ],
             ]);

    expect($response->json('data.total_bookings'))->toBe(2);
});

test('dashboard return 401 jika tidak ada token', function () {
    $response = $this->getJson('/api/dashboard');

    $response->assertStatus(401);
});

test('dashboard bisa filter berdasarkan query date', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Booking::factory()->create([
        'booking_date' => '2026-09-01',
        'time_slot' => '10:00',
    ]);

    Booking::factory()->create([
        'booking_date' => now()->format('Y-m-d'),
        'time_slot' => '11:00',
    ]);

    $response = $this->getJson('/api/dashboard?date=2026-09-01');

    $response->assertStatus(200)
             ->assertJsonPath('data.total_bookings', 1)
             ->assertJsonPath('data.date', '2026-09-01');
});

test('dashboard return data kosong jika tidak ada booking', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/dashboard');

    $response->assertStatus(200)
             ->assertJsonPath('data.total_bookings', 0)
             ->assertJsonPath('data.today_bookings', []);
});
