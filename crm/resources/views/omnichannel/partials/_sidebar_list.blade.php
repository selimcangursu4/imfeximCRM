@forelse($conversations as $item)
    <a href="{{ route('omnichannel.index', ['conversation' => $item->id]) }}" 
       data-chat-id="chat{{ $item->id }}" 
       class="list-group-item list-group-item-action d-flex gap-2 justify-content-between {{ isset($selectedConversation) && $selectedConversation->id === $item->id ? 'Aktif' : '' }}">
        <span class="d-flex justify-content-start align-items-center gap-2 overflow-hidden">
            <span class="avatar avatar-sm flex-shrink-0">
                @php
                    $providerColor = 'var(--bs-primary)';
                    if (optional($item->channel)->provider === 'whatsapp') $providerColor = '#25D366';
                    elseif (optional($item->channel)->provider === 'instagram') $providerColor = '#E1306C';
                    elseif (optional($item->channel)->provider === 'telegram') $providerColor = '#0088cc';
                @endphp
                <span class="avatar-title text-white rounded-circle" style="background-color: {{ $providerColor }} !important;">
                    @if (optional($item->channel)->provider === 'whatsapp')
                        <i class="ti ti-brand-whatsapp fs-5"></i>
                    @elseif (optional($item->channel)->provider === 'instagram')
                        <i class="ti ti-brand-instagram fs-5"></i>
                    @elseif (optional($item->channel)->provider === 'telegram')
                        <i class="ti ti-brand-telegram fs-5"></i>
                    @else
                        <i class="ti ti-message-circle fs-5"></i>
                    @endif
                </span>
            </span>
            <span class="overflow-hidden">
                <span data-chat-search-field class="text-nowrap fw-semibold fs-base mb-0 lh-base">{{ optional($item->customer)->name ?? 'Anonim' }}</span>
                <span class="text-muted d-block fs-xs mb-0 text-truncate">{{ optional($item->latestMessage)->body ? \Illuminate\Support\Str::limit($item->latestMessage->body, 50) : 'Yeni görüşme' }}</span>
            </span>
        </span>
        <span class="d-flex flex-column gap-1 justify-content-center flex-shrink-0 align-items-end">
            <span class="text-muted fs-xs">{{ $item->updated_at->diffForHumans() }}</span>
            @if($item->unread_messages_count > 0)
                <span class="badge text-bg-primary fs-xxs">{{ $item->unread_messages_count }}</span>
            @endif
        </span>
    </a>
@empty
    <div class="p-3 text-center text-muted">Henüz bir sohbet bulunmuyor.</div>
@endforelse
