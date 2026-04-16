<?php

namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\CompanyApiSetting;
use Illuminate\Http\Request;

class ApiSettingController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company ?? Company::first();
        $apiSettings = $company ? $company->apiSettings->keyBy('provider') : collect();

        return view('settings.api-setting', compact('company', 'apiSettings'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company ?? Company::first();
        if (!$company) {
            abort(404, 'Firma bulunamadı.');
        }

        $request->validate([
            'ai.api_key' => 'nullable|string|max:2000',
            'ai.engine' => 'nullable|in:openai,gemini',
            'ai.enabled' => 'sometimes|boolean',

            'instagram.app_id' => 'nullable|string|max:255',
            'instagram.app_secret' => 'nullable|string|max:255',
            'instagram.access_token' => 'nullable|string|max:2000',
            'instagram.business_account_id' => 'nullable|string|max:255',
            'instagram.verify_token' => 'nullable|string|max:255',
            'instagram.webhook_url' => 'nullable|url|max:1000',
            'instagram.enabled' => 'sometimes|boolean',

            'whatsapp.business_account_id' => 'nullable|string|max:255',
            'whatsapp.phone_number_id' => 'nullable|string|max:255',
            'whatsapp.access_token' => 'nullable|string|max:2000',
            'whatsapp.verify_token' => 'nullable|string|max:255',
            'whatsapp.graph_version' => 'nullable|string|max:20',
            'whatsapp.webhook_url' => 'nullable|url|max:1000',
            'whatsapp.enabled' => 'sometimes|boolean',
        ]);

        $providers = ['instagram', 'whatsapp', 'ai'];

        foreach ($providers as $provider) {
            $data = $request->input($provider, []);
            
            $enabled = isset($data['enabled']) && $data['enabled'] == '1';
            unset($data['enabled']);

            CompanyApiSetting::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'provider' => $provider
                ],
                [
                    'enabled' => $enabled,
                    'settings' => $enabled ? $data : [], 
                ]
            );
        }

        return redirect()->back()->with('success', 'API ayarlarınız başarıyla güncellendi. İstekler seçilen yapay zeka servisine ve platforma gönderilecektir.');
    }
}
