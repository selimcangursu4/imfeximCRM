<?php

namespace App\Services;

use App\Models\KnowledgeBase;
use App\Models\Message;
use Illuminate\Support\Facades\Http;

class AiChatService
{
    /**
     * Mesajı seçili AI platformuna gönderir, Bilgi Bankasını okur ve cevap üretir.
     */
    public function generateResponse($conversation, $incomingMessageText)
    {
        $company = $conversation->company ?? \App\Models\Company::first();
        if (!$company) return null;

        // Firma API ayarlarını çek
        $apiSetting = \App\Models\CompanyApiSetting::where('provider', 'ai')
            ->where('company_id', $company->id)
            ->first();

        // Ayar yoksa veya pasifse, eski openai kaydı var mı diye fallback yap (Eski sürüm uyumluluğu)
        if (!$apiSetting || !$apiSetting->enabled) {
            $apiSetting = \App\Models\CompanyApiSetting::where('provider', 'openai')
                ->where('company_id', $company->id)
                ->first();
        }

        if (!$apiSetting || !$apiSetting->enabled) {
             \Log::warning('[AI] Yapay zeka kapalı veya API anahtarı girilmemiş.');
             return null;
        }

        $engine = data_get($apiSetting->settings, 'engine', 'openai');
        $apiKey = data_get($apiSetting->settings, 'api_key');

        if (!$apiKey) {
            \Log::warning("[AI] $engine için API Key eksik. Otomatik cevap üretilemedi.");
            return null;
        }

        // 1. Bilgi Bankası (Knowledge Base) Verilerini Çek
        $kbDocs = KnowledgeBase::where('is_active', true)->get();
        $systemContext = "Sen professiyonel bir müşteri temsilcisisin. Sadece aşağıdaki bilgilere dayanarak kullanıcılara Türkçe cevap vermelisin. Eğer bilgi aşağıda yoksa, yetkili birime aktaracağını söyle ve konuyu uzatma. Cevapların direkt kullanıcıya (müşteriye) dönük doğal olmalı.\n\n[BİLGİ BANKASI]:\n";
        
        foreach ($kbDocs as $doc) {
            $systemContext .= "- {$doc->title}: {$doc->content}\n";
        }

        // 2. Önceki konuşma geçmişini çek (Son 6 mesaj)
        $history = Message::where('conversation_id', $conversation->id)
            ->orderBy('id', 'desc')
            ->take(6)
            ->get()
            ->reverse();

        // Sağlayıcıya Göre Formatı Belirle ve API İsteği At
        if ($engine === 'gemini') {
            return $this->callGemini($apiKey, $systemContext, $history, $incomingMessageText);
        } else {
            return $this->callOpenAI($apiKey, $systemContext, $history, $incomingMessageText);
        }
    }

    private function callOpenAI($apiKey, $systemContext, $history, $incomingMessageText)
    {
        $messagesArray = [
            ['role' => 'system', 'content' => $systemContext]
        ];

        foreach ($history as $msg) {
            if (!$msg->body) continue;
            $role = $msg->sender_type === 'customer' ? 'user' : 'assistant';
            $messagesArray[] = ['role' => $role, 'content' => $msg->body];
        }

        $messagesArray[] = ['role' => 'user', 'content' => $incomingMessageText];
        $messagesArray = array_values(array_unique($messagesArray, SORT_REGULAR));

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => $messagesArray,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            } else {
                \Log::error('[AI] OpenAI API Hatası', $response->json());
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('[AI] OpenAI Sistem Hatası', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function callGemini($apiKey, $systemContext, $history, $incomingMessageText)
    {
        // Gemini API Format payload
        $contents = [];

        foreach ($history as $msg) {
            if (!$msg->body) continue;
            $role = $msg->sender_type === 'customer' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $msg->body]]
            ];
        }

        // Yeni Gelen Mesaj
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $incomingMessageText]]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey, [
                'system_instruction' => [
                    'parts' => [['text' => $systemContext]]
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 500,
                ]
            ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            } else {
                \Log::error('[AI] Gemini API Hatası', $response->json());
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('[AI] Gemini Sistem Hatası', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
