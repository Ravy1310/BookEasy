<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $table = 'booking';

    // menentukan kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'time_slot',
        'booking_date',
        'reminder_sent',
    ];

    // memastikan tipe data kembalian ( casting) sesuai dengan database
    protected $casts =[
        'reminder_sent' => 'boolean',
        'booking_date' => 'date',
    ];
}
