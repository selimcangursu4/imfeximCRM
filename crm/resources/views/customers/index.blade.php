@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center mb-3">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Müşteri Yönetimi</h4>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    <i class="ti ti-plus me-1"></i> Yeni Müşteri Ekle
                </button>
                <ol class="breadcrumb m-0 py-0 ms-3 d-none d-md-inline-flex">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item active">Müşteriler</li>
                </ol>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$isAdmin)
        <div class="row mb-3">
            <div class="col-12">
                <div class="nav nav-pills p-1 bg-light rounded-pill d-inline-flex">
                    <a href="{{ route('customers.index', ['filter' => 'my']) }}" class="nav-link rounded-pill px-4 py-2 {{ $currentFilter == 'my' ? 'active' : '' }}">
                        <i class="ti ti-user-check me-1"></i> Müşterilerim
                    </a>
                    <a href="{{ route('customers.index', ['filter' => 'pool']) }}" class="nav-link rounded-pill px-4 py-2 {{ $currentFilter == 'pool' ? 'active' : '' }}">
                        <i class="ti ti-users me-1"></i> Havuzdaki Müşteriler
                    </a>
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Müşteri Listesi</h4>
                <span class="text-muted border bg-light px-2 py-1 rounded">Toplam {{ $customers->total() }} kayıt</span>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>İsim</th>
                                <th>Email</th>
                                <th>Telefon</th>
                                <th>Şirket</th>
                                <th>Oluşturulma</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-group-item">
                                                <div class="avatar avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <a href="{{ route('customers.show', $customer) }}" class="fw-bold link-reset">{{ $customer->name }}</a>
                                        </div>
                                    </td>
                                    <td>{{ $customer->email ?? '-' }}</td>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                    <td>{{ $customer->company ?? '-' }}</td>
                                    <td>{{ $customer->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('customers.chat', $customer) }}" class="btn btn-success btn-sm" title="Sohbete Başla">
                                            <i class="ti ti-message-2"></i>
                                        </a>
                                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-primary btn-sm ms-1" title="Detay / Profili Gör">
                                            <i class="ti ti-user"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <div class="mb-3">
                                            <i class="ti ti-inbox fs-1 text-muted opacity-50"></i>
                                        </div>
                                        Kayıtlı müşteri bulunamadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Müşteri Ekle Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-dashed">
                    <h5 class="modal-title">Yeni Müşteri Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Örn: Veli Yılmaz" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">E-posta Adresi</label>
                            <input type="email" name="email" class="form-control" placeholder="Örn: veli@ornek.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Telefon Numarası</label>
                            <input type="text" name="phone" class="form-control" placeholder="Örn: 05xx xxx xx xx">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Şirket Adı</label>
                            <input type="text" name="company" class="form-control" placeholder="Şirket ünvanı">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Adres</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Geçerli adres"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light-subtle">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
