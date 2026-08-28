<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // kita hanya mengembalikan data yang benar-benar dibutuhkan oleh forntend
        // menyembunyikan Id data asli, created_at, updated_at, dan kolom lainnya yang tidak dibutuhkan
        return [
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'time_slot' => $this->time_slot,
            'booking_date' => $this->booking_date,
        ];
    }
}
