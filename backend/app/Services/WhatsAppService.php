<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService {
    protected string $token;
    protected string $apiUrl = 'https://api.fonnte.com/send';

    public function __construct() {
        $this->token = config('services.fonnte.token');
    }

    // Eksekusi pengiriiman pesan HTTP POST ke API Fonnte.
    public function sendMessage(string $target, string $message): bool {
        if (empty($this->token)) {
            Log::warning('Fonnte token is missing. Message not sent.');
            return false;

        }

        $response = Http::withheaders([
            'Authorization' => $this->token,
        ]) ->timeout(30)
        ->connectTimeout(15)
            ->post($this->apiUrl, [
            'target' => $target,
            'message' => $message,
            'countryCode' => '62',
        ]);


        // Fonnte mengembalikan JSON {"status": true} jika benar benar sukses
        if ($response->successful() && $response->json('status') === true) {
            return true;
        }

        
        Log::error('Fonnte API Failed: ' . $response->body());
        return false;
    }

    // Format temlat teks untuk konfirmasi pemesanan sukses.
    public function sendBookingConfirmation(string $name, string $phone, string $date, string $time): bool {
        $text = "Halo {$name}! \n\nBooking Anda telah berhasil dikonfirmasi.\n Tanggal: {$date}\n Jam: {$time}\n\nTerima Kasih telah memilih laynan kami!";

        return $this->sendMessage($phone, $text);
    }

    // format template teks unutk pengingat jadwal ( reminder)
    public function sendReminder(string $name, string $phone, String $time): bool {
        $text = "Halo {$name}! \n\n Sekedar mengingatkan bahwa jadwal reservasi Anda adalah hari ini pada jam {$time}. \n\nKami tunggu kedatangannya!";
        return $this->sendMessage($phone, $text);
    }

}
?>