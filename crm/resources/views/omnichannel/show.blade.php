@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Omnichannel Gelen Kutusu</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item active">Omnichannel</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 mb-3 mb-xl-0">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Görüşmeler</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush overflow-auto" style="max-height: 76vh;">
                            @forelse($conversations as $item)
                                <a href="{{ route('omnichannel.show', $item) }}" class="list-group-item list-group-item-action d-flex flex-column {{ $item->id === $conversation->id ? 'active' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="fw-semibold">{{ optional($item->customer)->name ?? 'Anonim' }}</div>
                                        <small class="text-muted">{{ $item->updated_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="small text-truncate">{{ optional($item->latestMessage)->body ? \Illuminate\Support\Str::limit($item->latestMessage->body, 55) : 'Yeni görüşme' }}</div>
                                </a>
                            @empty
                                <div class="p-3 text-center text-muted">Henüz bir görüşme bulunmuyor.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-3 mb-xl-0">
                <div class="card h-100">
                    <!-- Sohbet Başlık Bandı -->
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <div>
                            <h6 class="mb-0 fw-bold">{{ optional($conversation->customer)->name ?? 'Anonim Müşteri' }}</h6>
                            <small class="text-muted">
                                <i data-lucide="message-circle" class="icon-xs me-1"></i>
                                {{ optional($conversation->channel)->provider ?? 'Bilinmiyor' }}
                            </small>
                        </div>

                        <!-- AI Toggle Bandı -->
                        <div class="d-flex align-items-center gap-2">
                            <div id="aiStatusBanner" class="d-flex align-items-center gap-2 px-3 py-1 rounded-pill border {{ $conversation->is_ai_active ? 'border-success bg-success-subtle' : 'border-warning bg-warning-subtle' }}">
                                <i data-lucide="{{ $conversation->is_ai_active ? 'cpu' : 'user' }}" id="aiStatusIcon" class="icon-sm {{ $conversation->is_ai_active ? 'text-success' : 'text-warning' }}"></i>
                                <span id="aiStatusText" class="fw-medium fs-xs {{ $conversation->is_ai_active ? 'text-success' : 'text-warning' }}">
                                    {{ $conversation->is_ai_active ? 'AI Asistan Aktif' : 'Manuel Mod (Personel)' }}
                                </span>
                                <div class="form-check form-switch mb-0 ms-1">
                                    <input class="form-check-input" type="checkbox" id="aiToggle"
                                        {{ $conversation->is_ai_active ? 'checked' : '' }}
                                        onchange="toggleAiStatus()"
                                        title="{{ $conversation->is_ai_active ? 'AI\'yı Durdur' : 'AI\'yı Başlat' }}">
                                </div>
                            </div>
                            <span class="badge bg-success">{{ ucfirst($conversation->status) }}</span>
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column p-3">
                        <div class="chat-messages flex-grow-1 overflow-auto mb-3" style="max-height: 64vh;">
                            @forelse($conversation->messages as $message)
                                <div class="d-flex mb-3 {{ $message->direction === 'outgoing' ? 'justify-content-end' : '' }}">
                                    <div class="px-3 py-2 rounded-3 {{ $message->direction === 'outgoing' ? 'bg-primary text-white' : 'bg-light text-dark' }}" style="max-width: 80%;">
                                        <p class="mb-1">{{ $message->body }}</p>
                                        <small class="text-muted">{{ $message->created_at->format('d.m.Y H:i') }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">Bu görüşmede henüz mesaj bulunmuyor.</div>
                            @endforelse
                        </div>

                        <form action="{{ route('omnichannel.message.store', $conversation) }}" method="post">
                            @csrf
                            <div class="input-group">
                                <textarea class="form-control" name="body" rows="2" placeholder="Mesajınızı yazın..." required></textarea>
                                <button class="btn btn-primary" type="submit">Gönder</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Müşteri Kartı</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="mb-1">{{ optional($conversation->customer)->name ?? 'Anonim' }}</h6>
                            <p class="text-muted mb-1">{{ optional($conversation->customer)->email ?? '-' }}</p>
                            <p class="text-muted mb-0">{{ optional($conversation->customer)->phone ?? '-' }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Firma</strong></p>
                            <p class="text-muted mb-0">{{ optional(optional($conversation->customer)->companyRelation)->name ?? optional($conversation->customer)->company ?? '-' }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Adres</strong></p>
                            <p class="text-muted mb-0">{{ optional($conversation->customer)->address ?? '-' }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Toplam Konuşma</strong></p>
                            <p class="text-muted mb-0">{{ $conversation->customer ? $conversation->customer->conversations->count() : 0 }}</p>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>En Son Aktivite</strong></p>
                            <p class="text-muted mb-0">{{ $conversation->updated_at->diffForHumans() }}</p>
                        </div>

                        @if($conversation->customer)
                            <a href="{{ route('customers.show', $conversation->customer) }}" class="btn btn-outline-primary w-100">Müşteri Detayı</a>
                        @else
                            <button type="button" class="btn btn-outline-secondary w-100" disabled>Şu anda müşteri yok</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function toggleAiStatus() {
    let toggle = document.getElementById('aiToggle');
    let isActive = toggle.checked ? 1 : 0;

    // Eğer AI'yı kapatıyorsa bir onay sor
    if (!isActive) {
        Swal.fire({
            title: 'Yapay Zekayı Durdur?',
            text: 'Bu sohbette AI otomatik cevap üretmeyi durduracak. Personel olarak devralabilirsiniz.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Evet, AI\'yı Durdur',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                sendToggleRequest(isActive);
            } else {
                // Geri al - kullanıcı vazgeçti
                toggle.checked = true;
            }
        });
    } else {
        sendToggleRequest(isActive);
    }
}

function sendToggleRequest(isActive) {
    $.ajax({
        url: '{{ route("omnichannel.toggle-ai", $conversation) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            is_ai_active: isActive
        },
        success: function(response) {
            let banner = document.getElementById('aiStatusBanner');
            let statusText = document.getElementById('aiStatusText');
            let statusIcon = document.getElementById('aiStatusIcon');

            if (isActive) {
                // AI Aktif görseli
                banner.className = 'd-flex align-items-center gap-2 px-3 py-1 rounded-pill border border-success bg-success-subtle';
                statusText.className = 'fw-medium fs-xs text-success';
                statusText.textContent = 'AI Asistan Aktif';
                statusIcon.setAttribute('data-lucide', 'cpu');
                statusIcon.className = 'icon-sm text-success';
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'AI Asistan tekrar devreye alındı', showConfirmButton: false, timer: 2000 });
            } else {
                // Manuel Mod görseli
                banner.className = 'd-flex align-items-center gap-2 px-3 py-1 rounded-pill border border-warning bg-warning-subtle';
                statusText.className = 'fw-medium fs-xs text-warning';
                statusText.textContent = 'Manuel Mod (Personel)';
                statusIcon.setAttribute('data-lucide', 'user');
                statusIcon.className = 'icon-sm text-warning';
                Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'AI durduruldu. Siz devralabilirsiniz.', showConfirmButton: false, timer: 2500 });
            }

            // İkonları yenile (lucide)
            if (typeof lucide !== 'undefined') lucide.createIcons();
        },
        error: function() {
            Swal.fire('Hata', 'AI durumu güncellenirken bir hata oluştu.', 'error');
            // Toggle'ı geri al
            document.getElementById('aiToggle').checked = !document.getElementById('aiToggle').checked;
        }
    });
}
</script>
@endsection
