<?php

namespace App\Http\Controllers;

use App\Models\CompanyApiSetting;
use App\Models\Conversation;
use App\Models\Customer;
use App\Models\Message;
use App\Models\MessageChannel;
use App\Services\MetaMessagingService;
use Illuminate\Http\Request;

class MetaWebhookController extends Controller
{
    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->verify($request);
        }

        return $this->receive($request);
    }

    protected function verify(Request $request)
    {
        // PHP extracts hub.mode as hub_mode from query string
        $mode      = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token     = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        \Log::info('[Webhook] Verify isteği geldi', [
            'mode'      => $mode,
            'token'     => $token,
            'challenge' => $challenge,
            'raw_query' => $request->all()
        ]);

        if ($mode !== 'subscribe' || ! $token || ! $challenge) {
            \Log::warning('[Webhook] Geçersiz parametreler');
            return response('Bad Request', 400);
        }

        // Her firmanın kendi verify_token'ı DB'de whatsapp veya instagram altında
        $apiSetting = CompanyApiSetting::whereIn('provider', ['whatsapp', 'instagram'])
            ->where('settings->verify_token', $token)
            ->first();

        if ($apiSetting) {
            \Log::info('[Webhook] Token eşleşti, challenge dönülüyor', [
                'provider'   => $apiSetting->provider,
                'company_id' => $apiSetting->company_id,
            ]);
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        \Log::error('[Webhook] Token eşleşmedi — DB\'de kayıtlı token bulunamadı', [
            'gelen_token' => $token,
        ]);
        return response('Unauthorized', 403);
    }

    protected function receive(Request $request)
    {
        $payload = $request->all();
        \Log::info('[Webhook] Payload geldi', ['payload' => $payload]);

        $object = data_get($payload, 'object');
        $entries = data_get($payload, 'entry', []);

        $messagingService = app(MetaMessagingService::class);

        if ($object === 'whatsapp_business_account') {
            $this->handleWhatsApp($entries);
        } elseif ($object === 'instagram') {
            $this->handleMessaging($entries, 'instagram', $messagingService);
        }

        return response('OK', 200);
    }

    protected function handleWhatsApp($entries)
    {
        foreach ($entries as $entry) {
            foreach (data_get($entry, 'changes', []) as $change) {
                $value = data_get($change, 'value', []);
                $messages = data_get($value, 'messages', []);
                $contacts = data_get($value, 'contacts', []);
                $metadata = data_get($value, 'metadata', []);
                $phoneNumberId = data_get($metadata, 'phone_number_id') ?: data_get($metadata, 'display_phone_number');

                if (empty($messages) || ! $phoneNumberId) {
                    continue;
                }

                $incomingMessage = $messages[0];
                $contact = $contacts[0] ?? [];
                $senderId = data_get($incomingMessage, 'from') ?: data_get($contact, 'wa_id');
                $senderName = data_get($contact, 'profile.name') ?: data_get($incomingMessage, 'profile.name', 'WhatsApp Kullanıcısı');
                $body = data_get($incomingMessage, 'text.body')
                    ?: data_get($incomingMessage, 'button.text')
                    ?: data_get($incomingMessage, 'interactive.list_reply.title')
                    ?: data_get($incomingMessage, 'interactive.button_reply.title');

                $apiSetting = CompanyApiSetting::where('provider', 'whatsapp')
                    ->where('settings->phone_number_id', (string) $phoneNumberId)
                    ->first();

                if (! $apiSetting) {
                    continue;
                }

                $this->processMessage(
                    $apiSetting,
                    'whatsapp',
                    $senderId,
                    $senderName,
                    $body,
                    $incomingMessage,
                    ['phone_number_id' => $phoneNumberId]
                );
            }
        }
    }

    protected function handleMessaging($entries, $provider, $messagingService)
    {
        foreach ($entries as $entry) {
            $recipientId = data_get($entry, 'id'); // Bu genellikle Business Account ID'dir
            $messagingList = data_get($entry, 'messaging', []);

            foreach ($messagingList as $messaging) {
                $senderId = data_get($messaging, 'sender.id');
                $messageData = data_get($messaging, 'message', []);
                $body = data_get($messageData, 'text');
                $isEcho = data_get($messaging, 'message.is_echo', false);

                if (! $body || ! $senderId || $isEcho) {
                    continue;
                }

                $apiSetting = CompanyApiSetting::where('provider', $provider)
                    ->where('settings->business_account_id', (string) $recipientId)
                    ->first();

                if (! $apiSetting) {
                    \Log::warning("[Webhook] {$provider} ayarı bulunamadı", ['recipientId' => $recipientId]);
                    continue;
                }

                $senderName = 'Instagram Kullanıcısı';
                if ($provider === 'instagram') {
                    $profile = $messagingService->getInstagramProfile($apiSetting->company, $senderId);
                    $senderName = $profile['name'] ?? 'Instagram Kullanıcısı';
                }

                $this->processMessage(
                    $apiSetting,
                    $provider,
                    $senderId,
                    $senderName,
                    $body,
                    $messaging,
                    ['business_account_id' => $recipientId]
                );
            }
        }
    }

    protected function processMessage($apiSetting, $provider, $senderId, $senderName, $body, $payload, $channelMeta)
    {
        $company = $apiSetting->company;

        $channel = MessageChannel::firstOrCreate(
            [
                'company_id' => $company->id,
                'provider' => $provider,
                'name' => ucfirst($provider),
            ],
            [
                'meta' => $channelMeta,
            ]
        );

        $customer = Customer::where('company_id', $company->id)
            ->where('phone', $senderId)
            ->first();

        if ($customer) {
            // Eğer mevcut isim "Instagram Kullanıcısı" ise ve yeni isim gerçek bir isimse güncelle
            if ($customer->name === 'Instagram Kullanıcısı' && $senderName !== 'Instagram Kullanıcısı') {
                $customer->update(['name' => $senderName]);
            }
        } else {
            $customer = Customer::create([
                'company_id' => $company->id,
                'phone' => $senderId,
                'name' => $senderName,
                'email' => null,
            ]);
        }

        $conversation = Conversation::firstOrCreate(
            [
                'company_id' => $company->id,
                'message_channel_id' => $channel->id,
                'external_thread_id' => $senderId,
            ],
            [
                'customer_id' => $customer->id,
                'subject' => ucfirst($provider),
                'status' => 'open',
            ]
        );

        $conversation->update([
            'customer_id' => $customer->id,
            'updated_at' => now(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'company_id' => $company->id,
            'sender_type' => 'customer',
            'sender_id' => $senderId,
            'direction' => 'incoming',
            'body' => $body,
            'payload' => $payload,
            'status' => 'received',
        ]);
    }
}
