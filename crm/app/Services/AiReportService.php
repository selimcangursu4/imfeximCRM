<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyApiSetting;
use Illuminate\Support\Facades\Http;

class AiReportService
{
    public function generateInsights($data)
    {
        $company = auth()->user()->company ?? Company::first();
        if (!$company) return "Firma bulunamadı.";

        $apiSetting = CompanyApiSetting::where('provider', 'ai')
            ->where('company_id', $company->id)
            ->first();

        if (!$apiSetting) {
            $apiSetting = CompanyApiSetting::where('provider', 'openai')
                ->where('company_id', $company->id)
                ->first();
        }

        $engine = $apiSetting ? data_get($apiSetting->settings, 'engine', 'openai') : 'openai';
        $apiKey = $apiSetting ? data_get($apiSetting->settings, 'api_key') : env('OPENAI_API_KEY');
        
        if (!$apiKey) {
            return "Sistemde $engine API Anahtarı bulunamadı. Lütfen Ayarlar > API Ayarları sekmesinden bir API anahtarı ekleyin.";
        }

        $systemPrompt = "Sen profesyonel bir CRM ve Satış Veri Analistisin. Sana verilen JSON formatındaki CRM istatistiklerini Türkçe olarak, şirketin durumunu özetleyecek şekilde yorumlamalısın. Cevabında mutlaka 3 ana madde olsun: \n1- Satış Düşüş/Yükseliş sebepleri ve Lead Hunisindeki kayıp durumları. \n2- Personel aktiviteleri veya müşteri kapatma sürelerine dair içgörüler. \n3- Tavsiyeler (Örn: Hangi kaynağa / kanala daha çok mesai harcanmalı). \nYorumların çok kısa, vurucu ve motive edici olsun.";

        if ($engine === 'gemini') {
            return $this->callGemini($apiKey, $systemPrompt, $data);
        } else {
            return $this->callOpenAI($apiKey, $systemPrompt, $data);
        }
    }

    private function callOpenAI($apiKey, $systemPrompt, $data)
    {
        $messagesArray = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => json_encode($data)]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => $messagesArray,
                'temperature' => 0.7,
                'max_tokens' => 800,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            } else {
                \Log::error('[AI Report] OpenAI API Hatası', $response->json());
                return "API yapılandırma veya limit hatası oluştu.";
            }
        } catch (\Exception $e) {
            \Log::error('[AI Report] Sistem Hatası', ['message' => $e->getMessage()]);
            return "Sunucu tarafında AI analizi yapılırken beklenmedik bir hata meydana geldi.";
        }
    }

    private function callGemini($apiKey, $systemPrompt, $data)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey, [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]]
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => json_encode($data)]]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ]
            ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            } else {
                \Log::error('[AI Report] Gemini API Hatası', $response->json());
                return "Gemini API yapılandırma veya limit hatası oluştu.";
            }
        } catch (\Exception $e) {
            \Log::error('[AI Report] Gemini Sistem Hatası', ['message' => $e->getMessage()]);
            return "Sunucu tarafında Gemini AI analizi yapılırken beklenmedik bir hata meydana geldi.";
        }
    }
}
