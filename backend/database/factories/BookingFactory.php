<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'time_slot' => fake()->numberBetween(9, 16) . ':00',
            'booking_date' => fake()->dateTimeBetween('now', '+1 week')-> format('Y-m-d'),
            'reminder_sent' => false,
        ];
    }
}
