@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center mb-3">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Müşteri Profili</h4>
            </div>
            <div class="text-end d-flex gap-2">
                @if(is_null($customer->assigned_user_id))
                    <button type="button" class="btn btn-warning assign-to-me-btn" data-id="{{ $customer->id }}">
                        <i class="ti ti-user-plus me-1"></i> Müşteriyi Kendime Ata
                    </button>
                @endif
                <a href="{{ route('customers.chat', $customer) }}" class="btn btn-success">
                    <i class="ti ti-message-2 me-1"></i> Sohbeti Aç
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerEditModal">
                    <i class="ti ti-edit me-1"></i> Bilgileri Güncelle
                </button>
                <ol class="breadcrumb m-0 py-0 ms-3 d-none d-md-flex align-items-center">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Müşteri Yönetimi</a></li>
                    <li class="breadcrumb-item active">{{ $customer->name }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <!-- Sol Kolon: Profil Bilgileri -->
            <div class="col-xl-4 col-lg-5">
                <div class="card mb-4" id="customer-profile-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg me-3">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-2">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0 d-flex align-items-center gap-2">
                                    <span data-field="name">{{ $customer->name }}</span>
                                    @php
                                        $statusClass = 'bg-secondary';
                                        if ($customer->status === \App\Models\Customer::STATUS_WON)
                                            $statusClass = 'bg-success';
                                        elseif ($customer->status === \App\Models\Customer::STATUS_LOST)
                                            $statusClass = 'bg-danger';
                                        elseif ($customer->status === \App\Models\Customer::STATUS_LEAD)
                                            $statusClass = 'bg-primary';
                                        elseif ($customer->status === \App\Models\Customer::STATUS_CONTACTED)
                                            $statusClass = 'bg-info';
                                        elseif ($customer->status === \App\Models\Customer::STATUS_QUALIFIED)
                                            $statusClass = 'bg-warning text-dark';
                                    @endphp
                                    <span
                                        class="badge {{ $statusClass }} fs-xxs fw-bold text-uppercase funnel-status-badge">{{ $customer->status ?? 'Lead' }}</span>
                                </h5>
                                <p class="text-muted mb-0 fs-xs" data-field="company">{{ $customer->company ?? 'Bireysel' }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-2">
                            <h6 class="text-uppercase fs-xxs fw-bold text-muted mb-3 ls-wider">İletişim Bilgileri</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="ti ti-hash fs-lg text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 fs-xs">Müşteri Numarası</p>
                                    <h6 class="mb-0 fs-sm">#{{ $customer->id }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="ti ti-world fs-lg text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 fs-xs">Veri Kaynağı</p>
                                    <div class="d-flex gap-1 mt-1">
                                        @php
                                            $providers = $customer->conversations->pluck('channel.provider')->unique();
                                        @endphp
                                        @forelse($providers as $provider)
                                            @php
                                                $badgeClass = 'bg-secondary-subtle text-secondary';
                                                if ($provider === 'whatsapp')
                                                    $badgeClass = 'bg-success-subtle text-success';
                                                elseif ($provider === 'instagram')
                                                    $badgeClass = 'bg-danger-subtle text-danger';
                                                elseif ($provider === 'telegram')
                                                    $badgeClass = 'bg-info-subtle text-info';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} fs-xxs text-uppercase">{{ $provider }}</span>
                                        @empty
                                            <h6 class="mb-0 fs-sm">-</h6>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="ti ti-mail fs-lg text-primary"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-muted mb-0 fs-xs">E-posta</p>
                                    <h6 class="mb-0 fs-sm text-truncate" data-field="email">{{ $customer->email ?? '-' }}
                                    </h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="ti ti-phone fs-lg text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 fs-xs">Telefon / PSID</p>
                                    <h6 class="mb-0 fs-sm" data-field="phone">{{ $customer->phone ?? '-' }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="ti ti-map-pin fs-lg text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 fs-xs">Adres</p>
                                    <h6 class="mb-0 fs-sm" data-field="address">{{ $customer->address ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-top mt-3">
                            <p class="text-muted mb-0 fs-xs">Sistem Kayıt Tarihi:
                                <strong>{{ $customer->created_at->format('d.m.Y H:i') }}</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Dosyalar & Ekler</h5>
                        <button class="btn btn-light btn-sm"><i class="ti ti-plus"></i></button>
                    </div>
                    <div class="card-body">
                        @if($customer->attachments->isEmpty())
                            <div class="text-center py-3">
                                <i class="ti ti-folder-off fs-1 text-muted opacity-25 d-block mb-2"></i>
                                <p class="text-muted fs-xs mb-0">Bu müşteri için henüz belge yüklenmedi.</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush mx-n3 mt-n3">
                                @foreach($customer->attachments as $attachment)
                                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                                        <div class="bg-info-subtle text-info p-2 rounded">
                                            <i class="ti ti-file"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="mb-0 fs-sm text-truncate">{{ $attachment->filename }}</h6>
                                            <small class="text-muted fs-xxs">{{ $attachment->created_at->diffForHumans() }}</small>
                                        </div>
                                        <i class="ti ti-download text-muted"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sağ Kolon: Aktiviteler ve Sohbetler -->
            <div class="col-xl-8 col-lg-7">
                <!-- Aktivite Giriş Formu -->
                <div class="card mb-4">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Müşteri Aktiviteleri</h5>
                    </div>
                    <div class="card-body">
                        <form id="activityForm" class="mb-4 bg-light p-3 rounded">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-xs fw-bold">Tip</label>
                                    <select name="type" class="form-select form-select-sm" required>
                                        <option value="Telefon Görüşmesi">Telefon Görüşmesi</option>
                                        <option value="E-posta">E-posta</option>
                                        <option value="Toplantı">Toplantı</option>
                                        <option value="Not">Not</option>
                                        <option value="Ziyaret">Ziyaret</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-xs fw-bold">Huni Durumu (Güncelle)</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">Değiştirme ({{ $customer->status }})</option>
                                        @foreach(\App\Models\Customer::getStatuses() as $status)
                                            <option value="{{ $status }}" {{ $customer->status == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-xs fw-bold">Açıklama</label>
                                    <textarea type="text" name="description" rows="7" class="form-control form-control-sm"
                                        placeholder="Aktivite detayı yazın..." required></textarea>
                                </div>
                                <div class="col-md-12 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Aktivite Ekle</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-sm align-middle" id="activitiesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 150px;">Tarih</th>
                                        <th style="width: 150px;">Tip</th>
                                        <th>Açıklama</th>
                                        <th style="width: 120px;">Personel</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->activities->sortByDesc('created_at') as $activity)
                                        <tr data-activity-id="{{ $activity->id }}">
                                            <td class="fs-xs">{{ $activity->created_at->format('d.m.Y H:i') }}</td>
                                            <td><span class="badge bg-info-subtle text-info fs-xxs">{{ $activity->type }}</span>
                                            </td>
                                            <td class="fs-sm">{{ $activity->description }}</td>
                                            <td class="fs-xs">{{ optional($activity->user)->name ?? 'Sistem' }}</td>
                                            <td class="text-end">
                                                <button class="btn btn-link link-danger p-0 delete-activity"
                                                    data-id="{{ $activity->id }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="5" class="text-center py-4 text-muted">Henüz bir aktivite kaydı
                                                bulunmuyor.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Müşteri Düzenleme Modalı -->
    <div class="modal fade" id="customerEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Müşteri Bilgilerini Güncelle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="ajaxCustomerUpdateForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Ad Soyad</label>
                            <input type="text" class="form-control" name="name" value="{{ $customer->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">E-posta</label>
                            <input type="email" class="form-control" name="email" value="{{ $customer->email }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Telefon / PSID</label>
                            <input type="text" class="form-control" name="phone" value="{{ $customer->phone }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Firma</label>
                            <input type="text" class="form-control" name="company" value="{{ $customer->company }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Adres</label>
                            <textarea class="form-control" name="address" rows="3">{{ $customer->address }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            const customerId = {{ $customer->id }};

            // Müşteri Güncelleme (AJAX)
            $('#ajaxCustomerUpdateForm').on('submit', function (e) {
                e.preventDefault();
                const btn = $(this).find('button[type="submit"]');
                const originalText = btn.text();
                btn.prop('disabled', true).text('Güncelleniyor...');

                $.ajax({
                    url: `/customers/${customerId}/ajax-update`,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#customerEditModal').modal('hide');

                            // Sayfadaki alanları güncelle
                            $('[data-field="name"]').text(response.customer.name);
                            $('[data-field="email"]').text(response.customer.email || '-');
                            $('[data-field="phone"]').text(response.customer.phone || '-');
                            $('[data-field="company"]').text(response.customer.company || 'Bireysel');
                            $('[data-field="address"]').text(response.customer.address || '-');
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Güncelleme sırasında bir hata oluştu.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: msg
                        });
                    },
                    complete: function () {
                        btn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Aktivite Ekleme (AJAX)
            $('#activityForm').on('submit', function (e) {
                e.preventDefault();
                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true);

                $.ajax({
                    url: `/customers/${customerId}/activities`,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            // Değerleri resetten önce al
                            const newStatus = $('#activityForm select[name="status"]').val();

                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('.empty-row').remove();
                            const a = response.activity;
                            const html = `
                                                    <tr data-activity-id="${a.id}">
                                                        <td class="fs-xs">${a.created_at}</td>
                                                        <td><span class="badge bg-info-subtle text-info fs-xxs">${a.type}</span></td>
                                                        <td class="fs-sm">${a.description}</td>
                                                        <td class="fs-xs">${a.user_name}</td>
                                                        <td class="text-end">
                                                            <button class="btn btn-link link-danger p-0 delete-activity" data-id="${a.id}">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                `;
                            $('#activitiesTable tbody').prepend(html);
                            $('#activityForm')[0].reset();

                            // Durum güncellenmişse sayfayı yenile (renk değişimleri için)
                            if (newStatus) {
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Aktivite eklenirken bir hata oluştu.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: msg
                        });
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                    }
                });
            });

            // Aktivite Silme (AJAX)
            $(document).on('click', '.delete-activity', function () {
                const id = $(this).data('id');
                const row = $(`tr[data-activity-id="${id}"]`);

                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu aktivite kaydı kalıcı olarak silinecektir!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet, sil!',
                    cancelButtonText: 'Vazgeç'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/activities/${id}`,
                            method: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Silindi!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    row.fadeOut(function () {
                                        $(this).remove();
                                        if ($('#activitiesTable tbody tr').length === 0) {
                                            $('#activitiesTable tbody').append('<tr class="empty-row"><td colspan="5" class="text-center py-4 text-muted">Henüz bir aktivite kaydı bulunmuyor.</td></tr>');
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
            });

            // Müşteriyi Kendime Ata (AJAX)
            $(document).on('click', '.assign-to-me-btn', function () {
                const id = $(this).data('id');
                const btn = $(this);

                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu müşteriyi kendi üzerinize atayacaksınız. Satış süreçleri sizin sorumluluğunuzda olacak.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet, Üzerime Al!',
                    cancelButtonText: 'Vazgeç'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).text('Atanıyor...');
                        $.ajax({
                            url: `/customers/${id}/assign`,
                            method: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Başarılı!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                }
                            },
                            error: function (xhr) {
                                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Atama sırasında bir hata oluştu.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata',
                                    text: msg
                                });
                                btn.prop('disabled', false).html('<i class="ti ti-user-plus me-1"></i> Müşteriyi Kendime Ata');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection