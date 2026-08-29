<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@bookeasy.id'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'reminder_hours' => 2,
            ]
        );

        // Default schedules (Senin-Jumat 09:00-17:00, Sabtu-Minggu tutup)
        for ($day = 0; $day < 7; $day++) {
            Schedule::firstOrCreate(
                ['day_of_week' => $day],
                [
                    'start_time' => $day < 5 ? '09:00' : null,
                    'end_time'   => $day < 5 ? '17:00' : null,
                    'is_closed'  => $day >= 5,
                ]
            );
        }

        // Sample bookings
        Booking::factory()->count(5)->create();
    }
}
