@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-4">
        <h4 class="fs-xl fw-bold m-0">Satış Raporları</h4>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Geri Dön</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-success-subtle border-success border-opacity-25 text-center h-100">
                <div class="card-body">
                    <h3 class="text-success mb-1">{{ number_format($totalSalesAmount, 2) }} ₺</h3>
                    <p class="text-muted mb-0">Toplam Gerçekleşen Ciro</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary-subtle border-primary border-opacity-25 text-center h-100">
                <div class="card-body">
                    <h3 class="text-primary mb-1">%{{ $winRate }}</h3>
                    <p class="text-muted mb-0">Kazanma Oranı (Win-Rate)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info-subtle border-info border-opacity-25 text-center h-100">
                <div class="card-body">
                    <h3 class="text-info mb-1">{{ number_format($avgDealSize, 2) }} ₺</h3>
                    <p class="text-muted mb-0">Ortalama Deal (Fırsat) Büyüklüğü</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex justify-content-around align-items-center">
                    <div>
                        <h4 class="text-success mb-0">{{ $wonCount }}</h4>
                        <small class="text-muted">Kazanılan</small>
                    </div>
                    <div>
                        <h4 class="text-danger mb-0">{{ $lostCount }}</h4>
                        <small class="text-muted">Kaybedilen</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Kazanılan / Kaybedilen Analizi</h5>
        </div>
        <div class="card-body d-flex justify-content-center">
            <div style="width: 300px; height: 300px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Kazanılan', 'Kaybedilen'],
            datasets: [{
                data: [{{ $wonCount }}, {{ $lostCount }}],
                backgroundColor: ['#22c55e', '#ef4444'],
            }]
        }
    });
</script>
@endsection
