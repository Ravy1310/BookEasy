<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'time_slot' => ['required', 'string', 'dateformat:H:i'],
            'booking_date' => ['required', 'date', 'date_format:Y-m-d'],

        ];
    }

    // customer pesan error agar ramah pengguna 
    public function messages(): array 
    {
        return [
            'customer_name.required' => 'nama pelanggan harus diisi',
            'customer_phone.reqiured' => 'nomor telepon pelanggan harus diisi',
            'time_slot.required' => 'jam sesi wajib diisi',
            'time_slot.date_format' => 'format jam tidak valid, gunakan format H:i (misal: 14:00)',
            'booking_date.required' => 'tanggal pemesanan harus diisi',
        ];
    }
}
