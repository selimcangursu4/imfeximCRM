<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyApiSetting;
use Illuminate\Http\Request;

class TelegramSettingController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company ?? Company::first();
        $telegramSetting = $company->apiSettings()->where('provider', 'telegram')->first();
        $settings = $telegramSetting ? $telegramSetting->settings : [];

        // Generate a webhook URL (tokenized for security)
        $webhookUrl = route('webhooks.telegram', ['token' => $settings['bot_token'] ?? 'BOT_TOKEN_BURAYA']);

        return view('settings.telegram-setting', compact('company', 'telegramSetting', 'settings', 'webhookUrl'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company ?? Company::first();
        if (!$company) {
            abort(404, 'Firma bulunamadı.');
        }

        $request->validate([
            'bot_token' => 'nullable|string|max:255',
            'bot_username' => 'nullable|string|max:255',
            'enabled' => 'sometimes|boolean',
        ]);

        CompanyApiSetting::updateOrCreate(
            ['company_id' => $company->id, 'provider' => 'telegram'],
            [
                'settings' => [
                    'bot_token' => $request->bot_token,
                    'bot_username' => $request->bot_username,
                ],
                'enabled' => $request->boolean('enabled'),
            ]
        );

        return back()->with('success', 'Telegram API ayarları kaydedildi.');
    }
}
