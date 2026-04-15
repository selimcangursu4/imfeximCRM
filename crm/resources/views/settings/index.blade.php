@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Ayarlar</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item active">API Ayarları</li>
                </ol>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Meta Developer API Ayarları</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Burada firmanıza ait Instagram ve WhatsApp Meta Developer bilgilerini girin. Bu bilgiler her firmanın kendi paneline özel olarak kaydedilecektir.</p>

                <form action="{{ route('settings.api.store') }}" method="post">
                    @csrf

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">Instagram API Bilgileri</h6>
                                <p class="text-muted mb-0">Meta platformundan elde ettiğiniz Instagram API ayarlarını burada saklayın.</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="instagram_enabled" name="instagram[enabled]" value="1" {{ optional($apiSettings->get('instagram'))->enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="instagram_enabled">Aktif</label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">App ID</label>
                                <input type="text" name="instagram[app_id]" class="form-control" value="{{ old('instagram.app_id', optional($apiSettings->get('instagram'))->settings['app_id'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">App Secret</label>
                                <input type="text" name="instagram[app_secret]" class="form-control" value="{{ old('instagram.app_secret', optional($apiSettings->get('instagram'))->settings['app_secret'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Access Token</label>
                                <input type="text" name="instagram[access_token]" class="form-control" value="{{ old('instagram.access_token', optional($apiSettings->get('instagram'))->settings['access_token'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Business Account ID</label>
                                <input type="text" name="instagram[business_account_id]" class="form-control" value="{{ old('instagram.business_account_id', optional($apiSettings->get('instagram'))->settings['business_account_id'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Verify Token</label>
                                <input type="text" name="instagram[verify_token]" class="form-control" value="{{ old('instagram.verify_token', optional($apiSettings->get('instagram'))->settings['verify_token'] ?? '') }}">
                                <small class="text-muted">Meta webhook doğrulaması için bu tokeni Meta Dashboard'a da yazın.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Webhook URL</label>
                                <input type="url" name="instagram[webhook_url]" class="form-control" value="{{ old('instagram.webhook_url', optional($apiSettings->get('instagram'))->settings['webhook_url'] ?? '') }}">
                                <small class="text-muted">Örnek: https://yourdomain.com/webhooks/meta</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">WhatsApp API Bilgileri</h6>
                                <p class="text-muted mb-0">Meta Developer üzerinden alınan WhatsApp Business API ve webhook bilgilerini girin.</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="whatsapp_enabled" name="whatsapp[enabled]" value="1" {{ optional($apiSettings->get('whatsapp'))->enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="whatsapp_enabled">Aktif</label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Business Account ID</label>
                                <input type="text" name="whatsapp[business_account_id]" class="form-control" value="{{ old('whatsapp.business_account_id', optional($apiSettings->get('whatsapp'))->settings['business_account_id'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number ID</label>
                                <input type="text" name="whatsapp[phone_number_id]" class="form-control" value="{{ old('whatsapp.phone_number_id', optional($apiSettings->get('whatsapp'))->settings['phone_number_id'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Access Token</label>
                                <input type="text" name="whatsapp[access_token]" class="form-control" value="{{ old('whatsapp.access_token', optional($apiSettings->get('whatsapp'))->settings['access_token'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Verify Token</label>
                                <input type="text" name="whatsapp[verify_token]" class="form-control" value="{{ old('whatsapp.verify_token', optional($apiSettings->get('whatsapp'))->settings['verify_token'] ?? '') }}">
                                <small class="text-muted">Meta webhook doğrulaması için bu tokeni burada tanımlayın.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Graph API Version</label>
                                <input type="text" name="whatsapp[graph_version]" class="form-control" value="{{ old('whatsapp.graph_version', optional($apiSettings->get('whatsapp'))->settings['graph_version'] ?? 'v17.0') }}">
                                <small class="text-muted">Boş bırakırsanız varsayılan v17.0 kullanılır.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Webhook URL</label>
                                <input type="url" name="whatsapp[webhook_url]" class="form-control" value="{{ old('whatsapp.webhook_url', optional($apiSettings->get('whatsapp'))->settings['webhook_url'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection