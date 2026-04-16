<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyApiSetting;
use Illuminate\Http\Request;

class AiSettingController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company ?? Company::first();
        $aiSetting = CompanyApiSetting::where('provider', 'ai')
            ->where('company_id', $company->id)
            ->first();

        return view('settings.ai-setting', compact('company', 'aiSetting'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company ?? Company::first();
        if (!$company) {
            abort(404, 'Firma bulunamadı.');
        }

        $request->validate([
            'ai_engine'  => 'nullable|in:openai,gemini',
            'ai_api_key' => 'nullable|string|max:2000',
            'ai_enabled' => 'sometimes|boolean',
            'ai_behavior'=> 'nullable|string|max:10000',
            'ai_rules'   => 'nullable|array',
            'ai_rules.*' => 'nullable|string|max:500',
        ]);

        $enabled = $request->boolean('ai_enabled', false);

        // Boş kuralları filtrele
        $rules = collect($request->input('ai_rules', []))
            ->map(fn($r) => trim($r))
            ->filter()
            ->values()
            ->toArray();

        // Mevcut ayarları al (kısmi kaydetmelerde var olan verileri korumak için)
        $existing = CompanyApiSetting::where('provider', 'ai')
            ->where('company_id', $company->id)
            ->first();

        $currentSettings = $existing ? ($existing->settings ?? []) : [];

        // Yeni değerlerle birleştir (null gelenlerde mevcut değeri koru)
        $newSettings = array_merge($currentSettings, [
            'engine'   => $request->filled('ai_engine') ? $request->ai_engine : ($currentSettings['engine'] ?? 'openai'),
            'api_key'  => $request->filled('ai_api_key') ? $request->ai_api_key : ($currentSettings['api_key'] ?? ''),
            'behavior' => $request->input('ai_behavior', $currentSettings['behavior'] ?? ''),
            'rules'    => $rules,
        ]);

        CompanyApiSetting::updateOrCreate(
            [
                'company_id' => $company->id,
                'provider'   => 'ai'
            ],
            [
                'enabled'  => $enabled,
                'settings' => $newSettings,
            ]
        );

        return redirect()->back()->with('success', '✅ Yapay Zeka ayarları ve davranış kuralları başarıyla kaydedildi!');
    }
}
