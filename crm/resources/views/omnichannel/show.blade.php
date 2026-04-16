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
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">{{ optional($conversation->customer)->name ?? 'Anonim Müşteri' }}</h5>
                                <small class="text-muted">{{ optional($conversation->channel)->provider ?? 'Platform yok' }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="aiToggle" {{ $conversation->is_ai_active ? 'checked' : '' }} onchange="toggleAiStatus()">
                                    <label class="form-check-label mb-0" for="aiToggle">
                                        <span class="badge {{ $conversation->is_ai_active ? 'bg-primary' : 'bg-secondary' }}" id="aiStatusBadge">
                                            {{ $conversation->is_ai_active ? 'AI Aktif' : 'AI Pasif (Manuel)' }}
                                        </span>
                                    </label>
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
    let isActive = document.getElementById('aiToggle').checked ? 1 : 0;
    
    $.ajax({
        url: '{{ route("omnichannel.toggle-ai", $conversation) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            is_ai_active: isActive
        },
        success: function(response) {
            let badge = $('#aiStatusBadge');
            if(isActive) {
                badge.removeClass('bg-secondary').addClass('bg-primary').text('AI Aktif');
            } else {
                badge.removeClass('bg-primary').addClass('bg-secondary').text('AI Pasif (Manuel)');
            }
        }
    });
}
</script>
@endsection
