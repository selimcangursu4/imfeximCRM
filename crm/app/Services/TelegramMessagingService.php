<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramMessagingService
{
    public function sendTextMessage(Company $company, string $chatId, string $body): array
    {
        $settings = $this->telegramSettings($company);

        if (!$settings) {
            return [
                'success' => false,
                'message' => 'Telegram API ayarları tamamlanmamış veya aktif değil.',
            ];
        }

        $botToken = $settings['bot_token'] ?? null;

        if (!$botToken) {
            return [
                'success' => false,
                'message' => 'Telegram Bot Token eksik.',
            ];
        }

        $endpoint = sprintf('https://api.telegram.org/bot%s/sendMessage', $botToken);

        $response = Http::post($endpoint, [
            'chat_id' => $chatId,
            'text' => $body,
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'response' => $response->json(),
            ];
        }

        Log::error('[Telegram Message] API Hatası', [
            'chat_id' => $chatId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'success' => false,
            'message' => 'Telegram mesaj gönderimi başarısız oldu.',
            'response' => $response->json(),
        ];
    }

    public function telegramSettings(Company $company): ?array
    {
        $apiSetting = $company->apiSettings()->where('provider', 'telegram')->first();

        if (!$apiSetting || !$apiSetting->enabled) {
            return null;
        }

        return $apiSetting->settings;
    }
}
