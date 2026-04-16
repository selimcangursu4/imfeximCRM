@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center mb-3">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Telegram API Ayarları</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0 ms-3 d-none d-md-inline-flex">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Ayarlar</a></li>
                    <li class="breadcrumb-item active">Telegram</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Bot Konfigürasyonu</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('settings.telegram.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Telegram Bot Token</label>
                                <input type="text" name="bot_token" class="form-control" value="{{ $settings['bot_token'] ?? '' }}" placeholder="Örn: 123456789:ABCDefGhIJKlmNoPQRstUVwxYz">
                                <div class="form-text fs-xs text-muted">@BotFather üzerinden aldığınız token.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Bot Kullanıcı Adı</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" name="bot_username" class="form-control" value="{{ $settings['bot_username'] ?? '' }}" placeholder="my_awesome_bot">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enabled" value="1" id="telegramEnabled" {{ ($telegramSetting->enabled ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="telegramEnabled">
                                        Telegram Entegrasyonunu Aktif Et
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Ayarları Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card bg-info-subtle border-info">
                    <div class="card-header bg-transparent border-bottom border-info-subtle">
                        <h5 class="card-title mb-0 text-info">Webhook Kurulumu</h5>
                    </div>
                    <div class="card-body">
                        <p class="fs-sm">Telegram Botunuzun mesajları bu sisteme iletebilmesi için aşağıdaki Webhook URL'sini Telegram'a kaydetmeniz gerekmektedir.</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-info">Webhook URL (Sisteme Ait)</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-white" id="webhookUrl" value="{{ $webhookUrl }}" readonly>
                                <button class="btn btn-info text-white" type="button" onclick="copyWebhook()">Kopyala</button>
                            </div>
                        </div>

                        <div class="alert alert-light border-dashed">
                            <h6 class="fw-bold fs-xs text-uppercase mb-2">Nasıl Kurulur?</h6>
                            <ol class="fs-xs mb-0 ps-3">
                                <li>Önce yukarıdaki <b>Bot Token</b>'ı kaydedin.</li>
                                <li>Token kaydedildikten sonra oluşan <b>Webhook URL</b>'sini kopyalayın.</li>
                                <li>Aşağıdaki URL yapısını kullanarak tarayıcınızdan bir istek atın (URL'yi kendinize göre düzenleyin):</li>
                                <li class="mt-2">
                                    <code class="d-block bg-white p-2 border rounded">
                                        https://api.telegram.org/bot{TOKEN}/setWebhook?url={WEBHOOK_URL}
                                    </code>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyWebhook() {
            var copyText = document.getElementById("webhookUrl");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            
            Swal.fire({
                icon: 'success',
                title: 'Kopyalandı!',
                text: 'Webhook URL panoya kopyalandı.',
                timer: 1500,
                showConfirmButton: false
            });
        }
    </script>
@endsection
