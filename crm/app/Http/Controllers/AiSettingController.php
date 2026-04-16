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
            'ai_engine' => 'required|in:openai,gemini',
            'ai_api_key' => 'required|string|max:2000',
            'ai_enabled' => 'sometimes|boolean',
        ]);

        $enabled = $request->boolean('ai_enabled', false);

        CompanyApiSetting::updateOrCreate(
            [
                'company_id' => $company->id,
                'provider' => 'ai'
            ],
            [
                'enabled' => $enabled,
                'settings' => [
                    'engine' => $request->ai_engine,
                    'api_key' => $request->ai_api_key,
                ],
            ]
        );

        return redirect()->back()->with('success', 'Yapay Zeka API ayarları başarıyla kaydedildi!');
    }
}
