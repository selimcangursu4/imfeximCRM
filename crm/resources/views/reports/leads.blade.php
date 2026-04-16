@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-4">
        <h4 class="fs-xl fw-bold m-0">Müşteri & Lead Raporu</h4>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Geri Dön</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center h-100 border-info">
                <div class="card-body">
                    <h2 class="text-info mb-1">{{ $todayLeads }}</h2>
                    <p class="text-muted mb-0">Bugün Gelen Lead</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="mb-1">{{ $weekLeads }}</h3>
                    <p class="text-muted mb-0">Bu Hafta Lead</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h3 class="mb-1">{{ $monthLeads }}</h3>
                    <p class="text-muted mb-0">Bu Ay Lead</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success-subtle border-success border-opacity-25 text-center h-100">
                <div class="card-body">
                    <h3 class="text-success mb-1">%{{ $conversionRate }}</h3>
                    <p class="text-muted mb-0">Lead -> Müşteri Sinerjisi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info-subtle">
            <h5 class="card-title text-info-emphasis mb-0">Kaynak Analizi (Nereden Geliyorlar?)</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <div style="width: 400px; height: 300px;">
                    <canvas id="sourceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const lbls = {!! json_encode(array_keys($sourceStats)) !!}.map(l => l ? l : 'Manuel');
    const dta = {!! json_encode(array_values($sourceStats)) !!};

    const ctx = document.getElementById('sourceChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: lbls,
            datasets: [{
                data: dta,
                backgroundColor: ['#0ea5e9', '#6366f1', '#ec4899', '#f59e0b', '#10b981'],
            }]
        }
    });
</script>
@endsection
