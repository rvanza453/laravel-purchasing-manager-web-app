<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $token;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
    }

    public function sendMessage($target, $message, $delay = null, $countryCode = '62')
    {
        if (empty($this->token)) {
            Log::warning('Fonnte Token is not set.');
            return false;
        }

        if (empty($target)) {
            Log::warning('Fonnte Target is empty.');
             return false;
        }

        try {
            $body = [
                'target' => $target,
                'message' => $message,
                'countryCode' => $countryCode,
            ];

            if ($delay) {
                $body['delay'] = $delay;
            }

            Log::info("Sending Fonnte WA to $target: $message");

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->withoutVerifying()
              ->timeout(30)
              ->post('https://api.fonnte.com/send', $body);

            if ($response->successful()) {
                Log::info('Fonnte Response: ' . $response->body());
                return $response->json();
            } else {
                Log::error('Fonnte Error: ' . $response->body());
                return false;
            }

        } catch (\Exception $e) {
             Log::error('Fonnte Exception: ' . $e->getMessage());
             return false;
        }
    }
}
