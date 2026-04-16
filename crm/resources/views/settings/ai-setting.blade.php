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
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <strong>Kayıt başarısız!</strong> Lütfen aşağıdaki hataları düzeltin:
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('settings.ai.store') }}" method="POST" id="aiSettingsForm">
        @csrf

        <div class="row mt-4 g-3">

            {{-- SOL KOLON: API Yapılandırması --}}
            <div class="col-xl-5">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i data-lucide="cpu" class="text-primary icon-sm"></i>
                        <h5 class="card-title mb-0">Servis Yapılandırması</h5>
                    </div>
                    <div class="card-body">
                        {{-- Aktif/Pasif Switch --}}
                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Otomatik Yanıt Sistemi</h6>
                                <p class="text-muted fs-sm mb-0">Bu anahtarı kapattığınızda tüm sohbetlerde yapay zeka yanıt üretmeyi durdurur.</p>
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

                        {{-- Motor Seçimi --}}
                        <div class="mb-3">
                            <label class="form-label fw-medium">Yapay Zeka Motoru</label>
                            <select name="ai_engine" id="ai_engine" class="form-select" onchange="updatePlaceholder()">
                                <option value="openai" {{ (optional($aiSetting)->settings['engine'] ?? '') == 'openai' ? 'selected' : '' }}>
                                    🤖 OpenAI (ChatGPT — gpt-3.5-turbo)
                                </option>
                                <option value="gemini" {{ (optional($aiSetting)->settings['engine'] ?? '') == 'gemini' ? 'selected' : '' }}>
                                    ✨ Google Gemini (gemini-pro)
                                </option>
                            </select>
                            <small class="text-muted">Hangi yapay zeka firmasının modeli kullanılacağını seçin.</small>
                        </div>

                        {{-- API Key --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">API Anahtarı</label>
                            <div class="input-group">
                                <span class="input-group-text"><i data-lucide="key" class="icon-sm"></i></span>
                                <input type="password" name="ai_api_key" id="ai_api_key" class="form-control"
                                    value="{{ optional($aiSetting)->settings['api_key'] ?? '' }}"
                                    placeholder="OpenAI için: sk-..., Gemini için: AIza..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleApiKeyVisibility()">
                                    <i data-lucide="eye" id="keyEyeIcon" class="icon-sm"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                OpenAI → <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com/api-keys</a> |
                                Gemini → <a href="https://aistudio.google.com/app/apikey" target="_blank">aistudio.google.com/app/apikey</a>
                            </small>
                        </div>

                        <div class="alert alert-info d-flex align-items-start gap-2 mb-0">
                            <i data-lucide="info" class="text-info mt-1 flex-shrink-0"></i>
                            <div class="fs-sm">
                                <strong>Nasıl Çalışır?</strong><br>
                                Müşteri mesaj gönderdiğinde sistem önce <strong>Bilgi Bankası</strong>'ndaki aktif dokümanları okur, ardından aşağıdaki kişilik ve kurallara göre yapay zeka motoru üzerinden bir yanıt oluşturur.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SAĞ KOLON: Davranış & Kurallar --}}
            <div class="col-xl-7">

                {{-- AI Persona & Davranış --}}
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i data-lucide="message-square" class="text-warning icon-sm"></i>
                        <h5 class="card-title mb-0">AI Davranışı (Sistem Komutu)</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fs-sm mb-3">AI'ın nasıl davranacağını, kim olduğunu ve nasıl konuşacağını buraya yazın. Bu metin AI'ın konuşma tarzını ve kişiliğini belirler.</p>
                        <textarea name="ai_behavior" id="ai_behavior" class="form-control" rows="6"
                            placeholder="Örn: Sen bir teknik servis müşteri temsilcisisin. Adın Wiky Asistan. Müşterilerle sıcakkanlı, samimi ve profesyonel bir şekilde konuş. İlk mesajda müşteriyi 'Merhaba, Ben Wiky Asistan, size nasıl yardımcı olabilirim?' gibi sıcak bir şekilde karşıla.">{{ optional($aiSetting)->settings['behavior'] ?? '' }}</textarea>
                        <small class="text-muted">Bu metin her sohbette AI'a verilen gizli "sistem talimatı" olarak gönderilir.</small>
                    </div>
                </div>

                {{-- Kurallar --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="list-checks" class="text-success icon-sm"></i>
                            <h5 class="card-title mb-0">Önemli Kurallar</h5>
                        </div>
                        <span class="badge bg-success-subtle text-success" id="ruleCount">
                            {{ count(optional($aiSetting)->settings['rules'] ?? []) }} kural
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fs-sm mb-3">Bu kurallar AI'ın cevap verirken uyması gereken temel ilkeleri tanımlar. Sıralamayı sürükleyerek değiştirebilir, silebilir veya yeni kural ekleyebilirsiniz.</p>

                        {{-- Kural Listesi --}}
                        <div id="rulesList">
                            @php $rules = optional($aiSetting)->settings['rules'] ?? []; @endphp
                            @forelse($rules as $i => $rule)
                                <div class="rule-item d-flex align-items-start gap-2 mb-2 p-2 rounded border bg-light-subtle">
                                    <span class="rule-number text-muted fw-bold mt-1" style="min-width:22px;">{{ $i+1 }}</span>
                                    <input type="text" name="ai_rules[]" class="form-control form-control-sm border-0 bg-transparent p-0" value="{{ $rule }}" style="outline:none; box-shadow:none;">
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 remove-rule" title="Sil">
                                        <i data-lucide="x" class="icon-xs"></i>
                                    </button>
                                </div>
                            @empty
                                <p class="text-muted fs-sm text-center py-3" id="emptyRulesMsg">
                                    <i data-lucide="list" class="icon-sm mb-1 d-block mx-auto"></i>
                                    Henüz kural eklenmedi. Aşağıdan ilk kuralı ekleyin.
                                </p>
                            @endforelse
                        </div>

                        {{-- Yeni Kural Ekle --}}
                        <div class="d-flex gap-2 mt-3">
                            <input type="text" id="newRuleInput" class="form-control form-control-sm"
                                placeholder="Yeni kural yazın ve ekleyin..."
                                onkeydown="if(event.key==='Enter'){event.preventDefault();addRule();}">
                            <button type="button" class="btn btn-sm btn-outline-success flex-shrink-0" onclick="addRule()">
                                <i data-lucide="plus" class="icon-xs me-1"></i> Ekle
                            </button>
                        </div>
                        <small class="text-muted fs-xs">Enter'a basarak veya "Ekle" butonuyla yeni kural ekleyebilirsiniz.</small>
                    </div>
                </div>
            </div>

        </div>

        {{-- Kaydet Butonu --}}
        <div class="d-flex justify-content-end gap-2 mt-3 mb-4">
            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">Geri Dön</a>
            <button type="submit" class="btn btn-primary px-5">
                <i data-lucide="save" class="icon-sm me-2"></i> Tüm Ayarları Kaydet
            </button>
        </div>

        </form>
    </div>
@endsection

@section('scripts')
<script>
    function updatePlaceholder() {
        const engine = document.getElementById('ai_engine').value;
        const input = document.getElementById('ai_api_key');
        input.placeholder = engine === 'gemini' ? 'Gemini API Key: AIza...' : 'OpenAI API Key: sk-...';
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

    function addRule() {
        const input = document.getElementById('newRuleInput');
        const text = input.value.trim();
        if (!text) return;

        // Boş mesajı kaldır
        const emptyMsg = document.getElementById('emptyRulesMsg');
        if (emptyMsg) emptyMsg.remove();

        const list = document.getElementById('rulesList');
        const index = list.querySelectorAll('.rule-item').length;

        const div = document.createElement('div');
        div.className = 'rule-item d-flex align-items-start gap-2 mb-2 p-2 rounded border bg-light-subtle';
        div.innerHTML = `
            <span class="rule-number text-muted fw-bold mt-1" style="min-width:22px;">${index + 1}</span>
            <input type="text" name="ai_rules[]" class="form-control form-control-sm border-0 bg-transparent p-0" value="${text.replace(/"/g, '&quot;')}" style="outline:none; box-shadow:none;">
            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 remove-rule" title="Sil">
                <i data-lucide="x" class="icon-xs"></i>
            </button>
        `;
        list.appendChild(div);

        input.value = '';
        updateRuleNumbers();
        lucide.createIcons();
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-rule')) {
            e.target.closest('.rule-item').remove();
            updateRuleNumbers();

            // Eğer kural kalmadıysa boş mesajı tekrar göster
            if (document.querySelectorAll('.rule-item').length === 0) {
                const list = document.getElementById('rulesList');
                const p = document.createElement('p');
                p.id = 'emptyRulesMsg';
                p.className = 'text-muted fs-sm text-center py-3';
                p.innerHTML = '<i data-lucide="list" class="icon-sm mb-1 d-block mx-auto"></i> Henüz kural eklenmedi. Aşağıdan ilk kuralı ekleyin.';
                list.appendChild(p);
                lucide.createIcons();
            }
        }
    });

    function updateRuleNumbers() {
        const items = document.querySelectorAll('.rule-item');
        items.forEach((item, i) => {
            const numSpan = item.querySelector('.rule-number');
            if (numSpan) numSpan.textContent = i + 1;
        });
        const badge = document.getElementById('ruleCount');
        if (badge) badge.textContent = items.length + ' kural';
    }
</script>
@endsection