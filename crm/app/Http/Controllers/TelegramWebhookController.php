<?php

namespace App\Http\Controllers;

use App\Models\CompanyApiSetting;
use App\Models\Conversation;
use App\Models\Customer;
use App\Models\Message;
use App\Models\MessageChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, $token)
    {
        Log::info('[Telegram Webhook] Payload geldi', ['token' => $token, 'payload' => $request->all()]);

        // Find the company by the bot token
        $apiSetting = CompanyApiSetting::where('provider', 'telegram')
            ->where('settings->bot_token', $token)
            ->first();

        if (!$apiSetting) {
            Log::warning('[Telegram Webhook] Gecersiz bot tokeni', ['token' => $token]);
            return response('Unauthorized', 401);
        }

        $payload = $request->all();
        $telegramMessage = $payload['message'] ?? null;

        if (!$telegramMessage || !isset($telegramMessage['text'])) {
            return response('OK', 200);
        }

        $chatId = $telegramMessage['chat']['id'];
        $senderName = trim(($telegramMessage['from']['first_name'] ?? '') . ' ' . ($telegramMessage['from']['last_name'] ?? ''));
        if (empty($senderName)) $senderName = $telegramMessage['from']['username'] ?? 'Telegram Kullanıcısı';
        
        $body = $telegramMessage['text'];

        $this->processMessage($apiSetting, $chatId, $senderName, $body, $payload);

        return response('OK', 200);
    }

    protected function processMessage($apiSetting, $chatId, $senderName, $body, $payload)
    {
        $company = $apiSetting->company;

        $channel = MessageChannel::firstOrCreate(
            [
                'company_id' => $company->id,
                'provider' => 'telegram',
                'name' => 'Telegram',
            ]
        );

        $customer = Customer::where('company_id', $company->id)
            ->where('phone', (string) $chatId) // We store telegram chat id in phone column for consistency
            ->first();

        if (!$customer) {
            $customer = Customer::create([
                'company_id' => $company->id,
                'phone' => (string) $chatId,
                'name' => $senderName,
            ]);
        }

        $conversation = Conversation::firstOrCreate(
            [
                'company_id' => $company->id,
                'message_channel_id' => $channel->id,
                'external_thread_id' => (string) $chatId,
            ],
            [
                'customer_id' => $customer->id,
                'subject' => 'Telegram Sohbeti',
                'status' => 'open',
            ]
        );

        $conversation->update([
            'updated_at' => now(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'company_id' => $company->id,
            'sender_type' => 'customer',
            'sender_id' => (string) $chatId,
            'direction' => 'incoming',
            'body' => $body,
            'payload' => $payload,
            'status' => 'received',
        ]);
    }
}
