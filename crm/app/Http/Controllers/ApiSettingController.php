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

        $data = $request->validate([
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

        foreach (['instagram', 'whatsapp'] as $provider) {
            CompanyApiSetting::updateOrCreate(
                ['company_id' => $company->id, 'provider' => $provider],
                [
                    'settings' => $request->input($provider, []),
                    'enabled' => $request->boolean($provider . '.enabled'),
                ]
            );
        }

        return back()->with('success', 'Meta API ayarları kaydedildi.');
    }
}
