<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Booking;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendBookingReminder implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

   
    public function handle(WhatsAppService $waService): void
    {
        $success = $waService->sendReminder(
            $this->booking->customer_name,
            $this->booking->customer_phone,
            $this->booking->time_slot
        );

        if ($success) {
            $this->booking->update(['reminder_sent' => true]);
        }
    }
}
