@extends('partials.master')
@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Yapay Zeka API Ayarları</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Ayarlar</a></li>
                    <li class="breadcrumb-item active">Yapay Zeka API Ayarları</li>
                </ol>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i data-lucide="check-circle" class="me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center mt-4">
            <div class="col-xl-12">
                <div class="card border-primary border-opacity-25">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Yapay Zeka Servis Yapılandırması</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">CRM'inizdeki otomatik yanıt sistemi, bilgi bankasındaki dokümanlar
                            üzerinden okuyarak gelen her mesaja buradaki ayarlara göre cevap üretecektir.</p>

                        <form action="{{ route('settings.ai.store') }}" method="POST">
                            @csrf

                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Otomatik Yanıt Sistemi</h6>
                                    <p class="text-muted fs-sm mb-0">Bu anahtarı kapattığınızda tüm sohbetlerde yapay zeka
                                        yanıt üretmeyi durdurur.</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ai_enabled" name="ai_enabled"
                                        value="1" {{ optional($aiSetting)->enabled ? 'checked' : '' }}
                                        style="width:3rem; height:1.5rem;">
                                    <label class="form-check-label fw-medium ms-2" for="ai_enabled">
                                        {{ optional($aiSetting)->enabled ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label fw-medium">Yapay Zeka Motoru</label>
                                <select name="ai_engine" id="ai_engine" class="form-select form-select-sm"
                                    onchange="updatePlaceholder()">
                                    <option value="openai" {{ (optional($aiSetting)->settings['engine'] ?? '') == 'openai' ? 'selected' : '' }}>
                                        🤖 OpenAI (ChatGPT — gpt-3.5-turbo)
                                    </option>
                                    <option value="gemini" {{ (optional($aiSetting)->settings['engine'] ?? '') == 'gemini' ? 'selected' : '' }}>
                                        ✨ Google Gemini (gemini-pro)
                                    </option>
                                </select>
                                <small class="text-muted">Hangi yapay zeka firmasının modeli kullanılacağını seçin. API
                                    anahtarı seçilen firmadan alınmalıdır.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium">API Anahtarı</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i data-lucide="key" class="icon-sm"></i></span>
                                    <input type="password" name="ai_api_key" id="ai_api_key"
                                        class="form-control form-control-sm"
                                        value="{{ optional($aiSetting)->settings['api_key'] ?? '' }}"
                                        placeholder="OpenAI için: sk-..., Gemini için: AIza..." autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="toggleApiKeyVisibility()">
                                        <i data-lucide="eye" id="keyEyeIcon" class="icon-sm"></i>
                                    </button>
                                </div>
                                <small class="text-muted" id="apiKeyHint">
                                    OpenAI → <a href="https://platform.openai.com/api-keys"
                                        target="_blank">platform.openai.com/api-keys</a> |
                                    Gemini → <a href="https://aistudio.google.com/app/apikey"
                                        target="_blank">aistudio.google.com/app/apikey</a>
                                </small>
                            </div>

                            <div class="alert alert-info d-flex align-items-start gap-2">
                                <i data-lucide="info" class="text-info mt-1 flex-shrink-0"></i>
                                <div class="fs-sm">
                                    <strong>Nasıl Çalışır?</strong><br>
                                    Müşteri mesaj gönderdiğinde sistem önce <strong>Bilgi Bankası</strong>'ndaki aktif
                                    dokümanları okur, ardından bu API anahtarını kullanarak seçilen yapay zeka motoru
                                    üzerinden profesyonel ve alakalı bir yanıt oluşturur.
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">Geri Dön</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i data-lucide="save" class="icon-sm me-2"></i> Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function updatePlaceholder() {
            const engine = document.getElementById('ai_engine').value;
            const input = document.getElementById('ai_api_key');
            if (engine === 'gemini') {
                input.placeholder = 'Gemini API Key: AIza...';
            } else {
                input.placeholder = 'OpenAI API Key: sk-...';
            }
        }

        function toggleApiKeyVisibility() {
            const input = document.getElementById('ai_api_key');
            const icon = document.getElementById('keyEyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
@endsection