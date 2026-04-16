@extends('partials.master')

@section('content')
    <div class="container-fluid">

        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Gelen Kutusu</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item active">Gelen Kutusu</li>
                </ol>
            </div>
        </div>

        <div class="outlook-box gap-1">
            <div class="offcanvas-lg offcanvas-start outlook-left-menu outlook-left-menu-lg" tabindex="-1" id="chatSidebaroffcanvas">
                <div class="card h-100 mb-0">

                    <div class="card-header p-3 d-block">
                        <div class="d-flex gap-2">
                            <div class="app-search flex-grow-1">
                                <input data-chat-search type="text" class="form-control bg-light-subtle border-light" placeholder="Mesaj Ara...">
                                <i data-lucide="search" class="app-search-icon text-muted"></i>
                            </div>
                        </div>
                    </div>
                    <div id="chat-sidebar" class="card-body p-2" style="height: calc(100% - 100px);" data-simplebar>
                        <div id="sidebar-conversations" class="list-group list-group-flush chat-list">
                            @include('omnichannel.partials._sidebar_list')
                        </div>

                    </div> <!-- end card-body-->

                </div> <!-- end card-->
            </div>

            <div class="card h-100 mb-0 flex-grow-1">
                <div class="card-header">
                    <div class="d-lg-none d-inline-flex gap-2">
                        <button class="btn btn-default btn-icon" type="button" data-bs-toggle="offcanvas" data-bs-target="#chatSidebaroffcanvas" aria-controls="chatSidebaroffcanvas">
                            <i class="ti ti-menu-2 fs-lg"></i>
                        </button>
                    </div>

                    <div class="flex-grow-1">
                        <h5 class="mb-1 lh-base fs-lg">
                            <a data-chat-username href="#!" class="link-reset">{{ optional(optional($selectedConversation)->customer)->name ?? 'Sohbet Seçiniz' }}</a>
                        </h5>
                        <p class="mb-0 lh-sm text-muted" style="padding-top: 1px;">
                            <small class="ti ti-circle-filled text-success me-1"></small>
                            {{ $selectedConversation ? 'Aktif' : 'Beklemede' }}
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @if($selectedConversation)
                        {{-- AI Toggle Bandı --}}
                        <div id="aiStatusBanner" class="d-flex align-items-center gap-2 px-3 py-1 rounded-pill border {{ $selectedConversation->is_ai_active ? 'border-success bg-success-subtle' : 'border-warning bg-warning-subtle' }}">
                            <i data-lucide="{{ $selectedConversation->is_ai_active ? 'cpu' : 'user' }}" id="aiStatusIcon" class="icon-sm {{ $selectedConversation->is_ai_active ? 'text-success' : 'text-warning' }}"></i>
                            <span id="aiStatusText" class="fw-medium d-none d-md-inline" style="font-size:11px;" >
                                {{ $selectedConversation->is_ai_active ? 'AI Aktif' : 'Manuel Mod' }}
                            </span>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="aiToggle"
                                    {{ $selectedConversation->is_ai_active ? 'checked' : '' }}
                                    onchange="toggleAiStatus({{ $selectedConversation->id }})"
                                    title="Yapay Zekayı Aç / Kapat">
                            </div>
                        </div>

                        <div class="dropdown">
                            <button type="button" class="btn btn-default btn-icon" data-bs-toggle="dropdown" aria-expanded="false" title="More">
                                <i class="ti ti-dots-vertical fs-lg"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="javascript:void(0);" onclick="openCustomerModal({{ $selectedConversation->customer_id }})"><i class="ti ti-user me-2"></i> Müşteri Kartı</a></li>
                            </ul>
                        </div>
                        @endif
                    </div>

                </div>

                <div id="chat-container" data-chat class="card-body pt-0 pb-0" data-simplebar style="max-height: calc(100vh - 370px); padding-bottom: 140px !important;">

                    @if($selectedConversation)
                        @forelse($selectedConversation->messages as $message)
                            <div class="d-flex align-items-start gap-2 my-3 chat-item {{ $message->direction === 'outgoing' ? 'justify-content-end text-end' : '' }}">
                                @if($message->direction !== 'outgoing')
                                    @php
                                        $msgProviderColor = '#f1f1f1';
                                        $msgTextColor = '#333';
                                        if (optional($selectedConversation->channel)->provider === 'whatsapp') {
                                            $msgProviderColor = '#25D366';
                                            $msgTextColor = '#fff';
                                        } elseif (optional($selectedConversation->channel)->provider === 'instagram') {
                                            $msgProviderColor = '#E1306C';
                                            $msgTextColor = '#fff';
                                        } elseif (optional($selectedConversation->channel)->provider === 'telegram') {
                                            $msgProviderColor = '#0088cc';
                                            $msgTextColor = '#fff';
                                        }
                                    @endphp
                                    <span class="avatar avatar-sm flex-shrink-0">
                                        <span class="avatar-title rounded-circle" style="background-color: {{ $msgProviderColor }} !important; color: {{ $msgTextColor }} !important;">
                                            {{ strtoupper(substr(optional($selectedConversation->customer)->name ?? 'U', 0, 1)) }}
                                        </span>
                                    </span>
                                @endif
                                <div>
                                    <div class="chat-message py-2 px-3 {{ $message->direction === 'outgoing' ? 'bg-info-subtle text-dark' : (optional($selectedConversation->channel)->provider === 'telegram' ? 'bg-primary-subtle text-dark border border-primary-subtle' : 'bg-warning-subtle') }} rounded">
                                        {{ $message->body }}
                                    </div>
                                    <div class="text-muted fs-xs mt-1"><i class="ti ti-clock"></i> {{ $message->created_at->format('h:i a') }}</div>
                                </div>
                                @if($message->direction === 'outgoing')
                                    @php
                                        $uParts = explode(' ', auth()->user()->name);
                                        $uInitials = strtoupper(substr($uParts[0], 0, 1) . (count($uParts) > 1 ? substr(end($uParts), 0, 1) : ''));
                                    @endphp
                                    <span class="avatar avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary text-white rounded-circle">
                                            {{ $uInitials }}
                                        </span>
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">Bu görüşmede henüz mesaj bulunmuyor.</div>
                        @endforelse
                    @else
                        <div class="text-center text-muted py-5">Soldan bir sohbet seçerek mesajlaşmayı başlatın.</div>
                    @endif

                </div> <!-- end card-body -->

                <div class="card-footer bg-body-secondary border-top border-dashed border-bottom-0 position-absolute bottom-0 w-100">
                    @if($selectedConversation)
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-list-check fs-lg"></i> Hazır Mesajlar
                                </button>
                                <ul class="dropdown-menu dropdown-menu-start shadow-lg border-light" style="max-height: 300px; overflow-y: auto; width: 250px;">
                                    @forelse($quickReplies as $reply)
                                        <li>
                                            <a class="dropdown-item py-2 border-bottom border-light-subtle" href="javascript:void(0);" onclick="useQuickReply(`{{ addslashes($reply->content) }}`)">
                                                <div class="fw-bold fs-xs text-primary mb-1">{{ $reply->title }}</div>
                                                <div class="text-muted text-truncate fs-xxs">{{ Str::limit($reply->content, 40) }}</div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="px-3 py-2 text-muted fs-xs">Hazır mesaj bulunamadı.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <form id="chat-form" action="{{ route('omnichannel.message.store', $selectedConversation) }}" method="post">
                            @csrf
                            <div class="d-flex gap-2">
                                <div class="app-search flex-grow-1">
                                    <textarea id="chat-textarea" data-chat-input class="form-control py-2 bg-light-subtle border-light" name="body" rows="2" placeholder="Mesajınızı Yazın..." required></textarea>
                                    <i data-lucide="message-square" class="app-search-icon text-muted"></i>
                                </div>
                                <button type="submit" class="btn btn-primary">Mesaj Gönder <i class="ti ti-send-2 ms-1 fs-xl"></i></button>
                            </div>
                            <span data-error class="d-none text-danger mt-2"></span>
                        </form>

                        <script>
                            function useQuickReply(content) {
                                const textarea = document.getElementById('chat-textarea');
                                if (textarea) {
                                    textarea.value = content;
                                    textarea.focus();
                                    // Trigger input event for any listeners (like auto-expand or character counts)
                                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                            }
                        </script>
                    @else
                        <div class="text-center py-3 text-muted">Bir sohbet seçin veya yeni sohbet başlatın.</div>
                    @endif
                </div>

            </div> <!-- end card-->
        </div> <!-- end outlook-box-->

      

        <div class="modal fade" id="videoCallModal" tabindex="-1" aria-labelledby="videoCallModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content bg-dark text-white">

                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="videoCallModalLabel">Starting Video Call</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                        <div class="mb-4">
                            <img src="assets/images/users/user-3.jpg" class="rounded-circle shadow" alt="User Photo" width="150" height="150">
                        </div>
                        <h3 class="fw-semibold mb-1">Alex Johnson</h3>
                        <p class="text-muted mb-4">Connecting to call...</p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light d-flex align-items-center gap-2"><i class="ti ti-video"></i> Camera On</button>
                            <button type="button" class="btn btn-light d-flex align-items-center gap-2"><i class="ti ti-microphone"></i> Mic On</button>
                            <button type="button" class="btn btn-danger d-flex align-items-center gap-2" data-bs-dismiss="modal"><i class="ti ti-phone-ringing"></i> End Call</button>
                        </div>
                    </div>

                    <div class="modal-footer border-0 justify-content-center">
                        <span class="text-muted fst-italic">Make sure your devices are connected before starting the call</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="audioCallModal" tabindex="-1" aria-labelledby="audioCallModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content bg-dark text-white">

                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="audioCallModalLabel">Starting Audio Call</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                        <div class="mb-4">
                            <img src="assets/images/users/user-3.jpg" class="rounded-circle shadow" alt="User Photo" width="120" height="120">
                        </div>
                        <h4 class="fw-semibold mb-1">Alex Johnson</h4>
                        <p class="text-muted mb-4">Calling...</p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light d-flex align-items-center gap-2"><i class="ti ti-microphone"></i> Mic On</button>
                            <button type="button" class="btn btn-light d-flex align-items-center gap-2"><i class="ti ti-headphones"></i> Speaker On</button>
                            <button type="button" class="btn btn-danger d-flex align-items-center gap-2" data-bs-dismiss="modal"><i class="ti ti-phone-call"></i> End Call</button>
                        </div>
                    </div>

                    <div class="modal-footer border-0 justify-content-center">
                        <span class="text-muted fst-italic">Ensure your microphone is working properly</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Müşteri Bilgileri Modalı -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-dark">
                <div class="modal-header border-dashed">
                    <h5 class="modal-title" id="customerModalLabel"><i class="ti ti-user-circle me-1"></i> Müşteri Profili</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <div class="avatar-xl rounded-circle border border-4 border-light shadow-sm bg-primary-subtle d-inline-flex align-items-center justify-content-center text-primary fw-bold fs-1" id="modalCustomerInitials">
                            M
                        </div>
                    </div>
                    <h4 class="fw-bold mb-1" id="modalCustomerNameText">Müşteri Adı</h4>
                    <p class="text-muted d-flex align-items-center justify-content-center gap-2 mb-4">
                        <i class="ti ti-mail fs-lg"></i> <span id="modalCustomerEmailText">Belirtilmedi</span>
                    </p>
                    
                    <ul class="list-group list-group-flush text-start border-top">
                        <li class="list-group-item d-flex align-items-center px-0 py-3">
                            <i class="ti ti-phone fs-lg text-primary me-3 bg-primary-subtle p-2 rounded"></i>
                            <div>
                                <h6 class="mb-0 fs-xs text-muted">Telefon / İletişim</h6>
                                <div class="fw-medium" id="modalCustomerPhoneText">Belirtilmedi</div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center px-0 py-3">
                            <i class="ti ti-building fs-lg text-info me-3 bg-info-subtle p-2 rounded"></i>
                            <div>
                                <h6 class="mb-0 fs-xs text-muted">Firma Adı</h6>
                                <div class="fw-medium" id="modalCustomerCompanyText">Belirtilmedi</div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center px-0 py-3">
                            <i class="ti ti-map-pin fs-lg text-warning me-3 bg-warning-subtle p-2 rounded"></i>
                            <div>
                                <h6 class="mb-0 fs-xs text-muted">Adres Bilgisi</h6>
                                <div class="fw-medium" id="modalCustomerAddressText">Belirtilmedi</div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer bg-light-subtle justify-content-center border-top-0 pt-0">
                    <a href="#" id="modalCustomerProfileLink" class="btn btn-primary w-100 rounded-pill">
                        <i class="ti ti-arrow-right"></i> Müşteri Yönetim Paneline Git
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatContainer = document.querySelector('#chat-container');
            const chatForm = document.querySelector('form[action*="omnichannel/message"]');
            const chatInput = document.querySelector('[data-chat-input]');
            const simpleBar = SimpleBar.instances.get(chatContainer);
            
            let lastMessageId = {{ $selectedConversation && $selectedConversation->messages->isNotEmpty() ? $selectedConversation->messages->last()->id : 0 }};
            const conversationId = {{ $selectedConversation ? $selectedConversation->id : 'null' }};
            
            @php
                $uParts = explode(' ', auth()->user()->name);
                $uInitials = strtoupper(substr($uParts[0], 0, 1) . (count($uParts) > 1 ? substr(end($uParts), 0, 1) : ''));
                $cInitials = strtoupper(substr(optional(optional($selectedConversation)->customer)->name ?? 'U', 0, 1));
                $isWhatsApp = optional(optional($selectedConversation)->channel)->provider === 'whatsapp';
            @endphp
            
            const userInitials = '{{ $uInitials }}';
            const customerInitials = '{{ $cInitials }}';
            const provider = '{{ optional(optional($selectedConversation)->channel)->provider }}';

            function scrollToBottom() {
                if (simpleBar) {
                    const scrollElement = simpleBar.getScrollElement();
                    scrollElement.scrollTop = scrollElement.scrollHeight;
                }
            }

            // İlk açılışta en alta kaydır
            setTimeout(scrollToBottom, 100);

            if (chatForm) {
                chatForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const body = chatInput.value;
                    if (!body.trim()) return;

                    const formData = new FormData(chatForm);
                    
                    fetch(chatForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            chatInput.value = '';
                            appendMessage(data.message);
                            scrollToBottom();
                            lastMessageId = data.message.id;
                        }
                    })
                    .catch(error => console.error('Error sending message:', error));
                });
            }

            function appendMessage(msg) {
                const isOutgoing = msg.direction === 'outgoing';
                
                let avatarBg = 'background-color: var(--bs-primary); color: #fff;';
                if (!isOutgoing) {
                    if (provider === 'whatsapp') avatarBg = 'background-color: #25D366 !important; color: #fff !important;';
                    else if (provider === 'instagram') avatarBg = 'background-color: #E1306C !important; color: #fff !important;';
                    else avatarBg = 'background-color: #f1f1f1; color: #333 !important;';
                }

                const initials = isOutgoing ? userInitials : customerInitials;

                const html = `
                    <div class="d-flex align-items-start gap-2 my-3 chat-item ${isOutgoing ? 'justify-content-end text-end' : ''}" data-message-id="${msg.id}">
                        ${!isOutgoing ? `
                            <span class="avatar avatar-sm flex-shrink-0">
                                <span class="avatar-title text-white rounded-circle" style="${avatarBg}">
                                    ${initials}
                                </span>
                            </span>
                        ` : ''}
                        <div>
                            <div class="chat-message py-2 px-3 ${isOutgoing ? 'bg-info-subtle text-dark' : 'bg-warning-subtle'} rounded">
                                ${msg.body}
                            </div>
                            <div class="text-muted fs-xs mt-1"><i class="ti ti-clock"></i> ${msg.created_at_human}</div>
                        </div>
                        ${isOutgoing ? `
                            <span class="avatar avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary text-white rounded-circle">
                                    ${initials}
                                </span>
                            </span>
                        ` : ''}
                    </div>
                `;
                
                const contentEl = chatContainer.querySelector('.simplebar-content');
                const emptyMsg = contentEl.querySelector('.text-center.text-muted.py-5');
                if (emptyMsg) emptyMsg.remove();
                
                contentEl.insertAdjacentHTML('beforeend', html);
            }

            function syncMessages() {
                if (!conversationId) return;

                fetch(`/omnichannel/${conversationId}/messages?last_id=${lastMessageId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            // Eğer mesaj zaten JS tarafından eklenmediyse ekle (outgoing mesajlar için çift eklemeyi önlemek için)
                            if (!document.querySelector(`[data-message-id="${msg.id}"]`)) {
                                appendMessage(msg);
                                lastMessageId = msg.id;
                            }
                        });
                        scrollToBottom();
                    }
                })
                .catch(error => console.error('Error syncing messages:', error))
                .finally(() => {
                    setTimeout(syncMessages, 3000);
                });
            }

            if (conversationId) {
                setTimeout(syncMessages, 3000);
            }

            // --- Sidebar Sync & Search ---
            const searchInput = document.querySelector('[data-chat-search]');
            const sidebarContainer = document.querySelector('#sidebar-conversations');
            
            let searchTimeout = null;

            window.syncSidebar = function() {
                const query = searchInput.value;
                const url = `{{ route('omnichannel.sidebar.sync') }}?search=${encodeURIComponent(query)}&selected_id=${conversationId || ''}`;
                
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    sidebarContainer.innerHTML = html;
                })
                .catch(error => console.error('Sidebar sync error:', error));
            }

            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(syncSidebar, 300);
            });

            // Her 7 saniyede bir sohbet listesini güncelle
            setInterval(syncSidebar, 7000);

            // --- Customer Modal Logic ---
            window.openCustomerModal = function(id) {
                fetch(`/omnichannel/customer/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const c = data.customer;
                        document.getElementById('modalCustomerInitials').textContent = c.name.charAt(0).toUpperCase();
                        document.getElementById('modalCustomerNameText').textContent = c.name;
                        document.getElementById('modalCustomerEmailText').textContent = c.email || 'Belirtilmedi';
                        document.getElementById('modalCustomerPhoneText').textContent = c.phone || 'Belirtilmedi';
                        document.getElementById('modalCustomerCompanyText').textContent = c.company || 'Belirtilmedi';
                        document.getElementById('modalCustomerAddressText').textContent = c.address || 'Belirtilmedi';
                        document.getElementById('modalCustomerProfileLink').href = `/customers/${c.id}`;
                        
                        const modal = new bootstrap.Modal(document.getElementById('customerModal'));
                        modal.show();
                    }
                });
            };

        });

        // AI Toggle
        window.toggleAiStatus = function(conversationId) {
            let toggle = document.getElementById('aiToggle');
            let isActive = toggle.checked ? 1 : 0;

            if (!isActive) {
                Swal.fire({
                    title: 'Yapay Zekayı Durdur?',
                    text: 'Bu sohbette AI otomatik cevap üretmeyi durduracak.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: "Evet, AI'yı Durdur",
                    cancelButtonText: 'Vazgeç'
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendAiToggle(conversationId, isActive);
                    } else {
                        toggle.checked = true;
                    }
                });
            } else {
                sendAiToggle(conversationId, isActive);
            }
        };

        function sendAiToggle(conversationId, isActive) {
            fetch(`/omnichannel/${conversationId}/toggle-ai`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ is_ai_active: isActive })
            })
            .then(r => r.json())
            .then(data => {
                const banner = document.getElementById('aiStatusBanner');
                const statusText = document.getElementById('aiStatusText');
                const statusIcon = document.getElementById('aiStatusIcon');

                if (isActive) {
                    banner.className = 'd-flex align-items-center gap-2 px-3 py-1 rounded-pill border border-success bg-success-subtle';
                    statusText.className = 'fw-medium d-none d-md-inline text-success';
                    statusText.style.fontSize = '11px';
                    statusText.textContent = 'AI Aktif';
                    statusIcon.setAttribute('data-lucide', 'cpu');
                    statusIcon.className = 'icon-sm text-success';
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'AI Asistan devreye alındı', showConfirmButton: false, timer: 2000 });
                } else {
                    banner.className = 'd-flex align-items-center gap-2 px-3 py-1 rounded-pill border border-warning bg-warning-subtle';
                    statusText.className = 'fw-medium d-none d-md-inline text-warning';
                    statusText.style.fontSize = '11px';
                    statusText.textContent = 'Manuel Mod';
                    statusIcon.setAttribute('data-lucide', 'user');
                    statusIcon.className = 'icon-sm text-warning';
                    Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'AI durduruldu. Siz devralabilirsiniz.', showConfirmButton: false, timer: 2500 });
                }

                if (typeof lucide !== 'undefined') lucide.createIcons();
            })
            .catch(() => {
                Swal.fire('Hata', 'AI durumu güncellenirken sorun oluştu.', 'error');
                document.getElementById('aiToggle').checked = !document.getElementById('aiToggle').checked;
            });
        }
    </script>
@endsection
