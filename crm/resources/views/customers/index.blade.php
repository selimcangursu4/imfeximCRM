@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Müşteri Yönetimi</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
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
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Müşteri Listesi</h4>
                <span class="text-muted">Toplam {{ $customers->total() }} kayıt</span>
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
                                    <td><a href="{{ route('customers.show', $customer) }}" class="link-reset">{{ $customer->name }}</a></td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                    <td>{{ $customer->company ?? '-' }}</td>
                                    <td>{{ $customer->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('customers.chat', $customer) }}" class="btn btn-success btn-sm">
                                            <i class="ti ti-message-2 me-1"></i> Sohbete Başla
                                        </a>
                                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-primary btn-sm ms-1">
                                            <i class="ti ti-user me-1"></i> Detay
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Kayıtlı müşteri bulunamadı.</td>
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
@endsection
