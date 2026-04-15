@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center mb-3">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Müşteri Profili</h4>
            </div>
            <div class="text-end d-flex gap-2">
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
                                <h5 class="mb-0" data-field="name">{{ $customer->name }}</h5>
                                <p class="text-muted mb-0 fs-xs" data-field="company">{{ $customer->company ?? 'Bireysel' }}</p>
                            </div>
                        </div>
                        
                        <div class="pt-2">
                            <h6 class="text-uppercase fs-xxs fw-bold text-muted mb-3 ls-wider">İletişim Bilgileri</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="ti ti-mail fs-lg text-primary"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-muted mb-0 fs-xs">E-posta</p>
                                    <h6 class="mb-0 fs-sm text-truncate" data-field="email">{{ $customer->email ?? '-' }}</h6>
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
                            <p class="text-muted mb-0 fs-xs">Sistem Kayıt Tarihi: <strong>{{ $customer->created_at->format('d.m.Y H:i') }}</strong></p>
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
                                <div class="col-md-3">
                                    <label class="form-label fs-xs fw-bold">Tip</label>
                                    <select name="type" class="form-select form-select-sm" required>
                                        <option value="Telefon Görüşmesi">Telefon Görüşmesi</option>
                                        <option value="E-posta">E-posta</option>
                                        <option value="Toplantı">Toplantı</option>
                                        <option value="Not">Not</option>
                                        <option value="Ziyaret">Ziyaret</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fs-xs fw-bold">Açıklama</label>
                                    <input type="text" name="description" class="form-control form-control-sm" placeholder="Aktivite detayı yazın..." required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Ekle</button>
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
                                            <td><span class="badge bg-info-subtle text-info fs-xxs">{{ $activity->type }}</span></td>
                                            <td class="fs-sm">{{ $activity->description }}</td>
                                            <td class="fs-xs">{{ optional($activity->user)->name ?? 'Sistem' }}</td>
                                            <td class="text-end">
                                                <button class="btn btn-link link-danger p-0 delete-activity" data-id="{{ $activity->id }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="5" class="text-center py-4 text-muted">Henüz bir aktivite kaydı bulunmuyor.</td>
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
        $(document).ready(function() {
            const customerId = {{ $customer->id }};

            // Müşteri Güncelleme (AJAX)
            $('#ajaxCustomerUpdateForm').on('submit', function(e) {
                e.preventDefault();
                const btn = $(this).find('button[type="submit"]');
                const originalText = btn.text();
                btn.prop('disabled', true).text('Güncelleniyor...');

                $.ajax({
                    url: `/customers/${customerId}/ajax-update`,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#customerEditModal').modal('hide');
                            
                            // Sayfadaki alanları güncelle
                            $('[data-field="name"]').text(response.customer.name);
                            $('[data-field="email"]').text(response.customer.email || '-');
                            $('[data-field="phone"]').text(response.customer.phone || '-');
                            $('[data-field="company"]').text(response.customer.company || 'Bireysel');
                            $('[data-field="address"]').text(response.customer.address || '-');
                        }
                    },
                    error: function() {
                        alert('Güncelleme sırasında bir hata oluştu.');
                    },
                    complete: function() {
                        btn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Aktivite Ekleme (AJAX)
            $('#activityForm').on('submit', function(e) {
                e.preventDefault();
                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true);

                $.ajax({
                    url: `/customers/${customerId}/activities`,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
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
                        }
                    },
                    error: function() {
                        alert('Aktivite eklenirken bir hata oluştu.');
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });

            // Aktivite Silme (AJAX)
            $(document).on('click', '.delete-activity', function() {
                if (!confirm('Bu aktiviteyi silmek istediğinize emin misiniz?')) return;
                
                const id = $(this).data('id');
                const row = $(`tr[data-activity-id="${id}"]`);

                $.ajax({
                    url: `/activities/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            row.fadeOut(function() {
                                $(this).remove();
                                if ($('#activitiesTable tbody tr').length === 0) {
                                    $('#activitiesTable tbody').append('<tr class="empty-row"><td colspan="5" class="text-center py-4 text-muted">Henüz bir aktivite kaydı bulunmuyor.</td></tr>');
                                }
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
