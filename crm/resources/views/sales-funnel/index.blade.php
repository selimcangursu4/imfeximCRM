@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center mb-3">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Satış Hunisi</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0 d-none d-md-inline-flex">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item active">Satış Hunisi</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card overflow-hidden">
                    <div class="card-body p-0">
                        <div class="funnel-container p-4" style="background-color: #f8f9fa;">
                            @foreach($statuses as $status)
                                @php
                                    $customers = $funnelData[$status];
                                    $count = $customers->count();
                                    
                                    $color = '#6c757d'; // Default
                                    if ($status === \App\Models\Customer::STATUS_WON) $color = '#28a745';
                                    elseif ($status === \App\Models\Customer::STATUS_LOST) $color = '#dc3545';
                                    elseif ($status === \App\Models\Customer::STATUS_LEAD) $color = '#0d6efd';
                                    elseif ($status === \App\Models\Customer::STATUS_CONTACTED) $color = '#0dcaf0';
                                    elseif ($status === \App\Models\Customer::STATUS_QUALIFIED) $color = '#ffc107';
                                    elseif ($status === \App\Models\Customer::STATUS_NEED_ANALYSIS) $color = '#6610f2';
                                    elseif ($status === \App\Models\Customer::STATUS_PROPOSAL) $color = '#fd7e14';
                                    elseif ($status === \App\Models\Customer::STATUS_NEGOTIATION) $color = '#e83e8c';
                                    elseif ($status === \App\Models\Customer::STATUS_WAITING) $color = '#20c997';
                                @endphp
                                
                                <div class="funnel-stage mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="stage-label py-2 px-3 rounded-start text-white fw-bold d-flex align-items-center" style="background-color: {{ $color }}; min-width: 180px;">
                                            <span class="fs-xs me-2 opacity-75">#{{ $loop->iteration }}</span>
                                            {{ $status }}
                                        </div>
                                        <div class="flex-grow-1 ms-2 border-top border-2 border-dashed" style="border-color: {{ $color }}66 !important;"></div>
                                        <div class="ms-2">
                                            <span class="badge rounded-pill fs-xs px-3" style="background-color: {{ $color }}22; color: {{ $color }}; border: 1px solid {{ $color }}44;">
                                                {{ $count }} Müşteri
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @if($count > 0)
                                        <div class="row g-2 ps-4">
                                            @foreach($customers as $customer)
                                                <div class="col-md-4 col-xl-3">
                                                    <a href="{{ route('customers.show', $customer) }}" class="card mb-0 h-100 shadow-sm border-0 link-reset hover-lift">
                                                        <div class="card-body p-2">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-xs me-2">
                                                                    <span class="avatar-title rounded-circle bg-light text-primary fs-xs">
                                                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                                                    </span>
                                                                </div>
                                                                <div class="overflow-hidden">
                                                                    <h6 class="fs-xs mb-0 text-truncate">{{ $customer->name }}</h6>
                                                                    <p class="text-xs text-muted mb-0 text-truncate">{{ $customer->company ?? 'Bireysel' }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="ps-4 text-muted fs-xs font-italic">Henüz bu aşamada müşteri bulunmuyor.</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .text-xs { font-size: 0.75rem; }
        .fs-xxs { font-size: 0.65rem; }
    </style>
@endsection
