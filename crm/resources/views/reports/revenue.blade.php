@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-4">
        <h4 class="fs-xl fw-bold m-0">Gelir ve Tahmin (Forecast)</h4>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Geri Dön</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-success text-center h-100 border-2">
                <div class="card-body">
                    <div class="avatar-md bg-success-subtle text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i data-lucide="check-circle" class="fs-2"></i>
                    </div>
                    <h2 class="text-success mb-1">{{ number_format($actualRevenue, 2) }} ₺</h2>
                    <p class="text-muted fw-bold mb-0">Toplam Gerçekleşen (Kazanılan) Gelir</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-primary text-center h-100 border-dashed border-2">
                <div class="card-body">
                    <div class="avatar-md bg-primary-subtle text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i data-lucide="help-circle" class="fs-2"></i>
                    </div>
                    <h2 class="text-primary mb-1">{{ number_format($expectedRevenue, 2) }} ₺</h2>
                    <p class="text-muted fw-bold mb-0">Pipeline'da Bekleyen (Tahmini) Gelir (Forecast)</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
