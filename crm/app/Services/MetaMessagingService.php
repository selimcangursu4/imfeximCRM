<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;

class MetaMessagingService
{
    public function sendWhatsAppTextMessage(Company $company, string $recipientId, string $body): array
    {
        $settings = $this->whatsappSettings($company);

        if (! $settings) {
            return [
                'success' => false,
                'message' => 'WhatsApp API ayarları tamamlanmamış veya aktif değil.',
            ];
        }

        $phoneNumberId = $settings['phone_number_id'] ?? null;
        $accessToken = $settings['access_token'] ?? null;

        if (! $phoneNumberId || ! $accessToken) {
            return [
                'success' => false,
                'message' => 'WhatsApp telefon numarası kimliği veya erişim belirteci eksik.',
            ];
        }

        $graphVersion = $settings['graph_version'] ?? config('services.meta.graph_version', 'v17.0');
        $endpoint = sprintf('https://graph.facebook.com/%s/%s/messages', $graphVersion, $phoneNumberId);

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post($endpoint, [
                'messaging_product' => 'whatsapp',
                'to' => $recipientId,
                'type' => 'text',
                'text' => [
                    'body' => $body,
                ],
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'response' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'message' => 'WhatsApp mesaj gönderimi başarısız oldu.',
            'response' => $response->json(),
        ];
    }

    public function getInstagramProfile(Company $company, string $psid): array
    {
        $settings = $this->instagramSettings($company);

        if (! $settings) {
            return ['name' => 'Instagram Kullanıcısı'];
        }

        $accessToken = $settings['access_token'] ?? null;

        if (! $accessToken) {
            return ['name' => 'Instagram Kullanıcısı'];
        }

        // Instagram Profil Bilgisi Çekme
        // Endpoint: https://graph.facebook.com/v17.0/<PSID>?fields=name,username,profile_pic
        $endpoint = sprintf('https://graph.facebook.com/v17.0/%s', $psid);

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->get($endpoint, [
                    'fields' => 'name,username,profile_pic',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Eğer name boşsa username'i isim olarak ata
                if (empty($data['name']) && !empty($data['username'])) {
                    $data['name'] = $data['username'];
                }

                return $data;
            }

            \Log::error('[Instagram Profile] Meta API Hatası', [
                'psid' => $psid,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            \Log::error('[Instagram Profile] Exception Oluştu', [
                'psid' => $psid,
                'error' => $e->getMessage()
            ]);
        }

        return ['name' => 'Instagram Kullanıcısı'];
    }

    public function sendInstagramTextMessage(Company $company, string $psid, string $body): array
    {
        $settings = $this->instagramSettings($company);

        if (! $settings) {
            return [
                'success' => false,
                'message' => 'Instagram API ayarları tamamlanmamış veya aktif değil.',
            ];
        }

        $accessToken = $settings['access_token'] ?? null;

        if (! $accessToken) {
            return [
                'success' => false,
                'message' => 'Instagram erişim belirteci eksik.',
            ];
        }

        // Instagram Mesaj Gönderimi için PSID kullanılır
        // Endpoint: https://graph.facebook.com/v17.0/me/messages
        $endpoint = 'https://graph.facebook.com/v17.0/me/messages';

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post($endpoint, [
                'recipient' => ['id' => $psid],
                'message' => ['text' => $body],
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'response' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'message' => 'Instagram mesaj gönderimi başarısız oldu.',
            'response' => $response->json(),
        ];
    }

    public function whatsappSettings(Company $company): ?array
    {
        $apiSetting = $company->apiSettings()->where('provider', 'whatsapp')->first();

        if (! $apiSetting || ! $apiSetting->enabled) {
            return null;
        }

        return $apiSetting->settings;
    }

    public function instagramSettings(Company $company): ?array
    {
        $apiSetting = $company->apiSettings()->where('provider', 'instagram')->first();

        if (! $apiSetting || ! $apiSetting->enabled) {
            return null;
        }

        return $apiSetting->settings;
    }
}
