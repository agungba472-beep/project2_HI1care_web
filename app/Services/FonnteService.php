<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class FonnteService
{
    /**
     * Mengirim pesan WhatsApp via Fonnte API.
     *
     * @param int|null $userId ID User tujuan untuk mengambil nomor HP.
     * @param string $message Pesan yang akan dikirim.
     * @return bool True jika berhasil, false jika gagal.
     */
    public static function sendMessage($userId, $message)
    {
        try {
            $token = env('FONNTE_TOKEN', 'HQz7iTB2pkwTpEUNB4cW');
            if (empty($token) || !$userId) {
                return false;
            }

            $user = User::find($userId);
            if (!$user || empty($user->no_hp)) {
                return false;
            }

            $target = $user->no_hp;

            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Fonnte Error: ' . $e->getMessage());
            return false;
        }
    }
}
