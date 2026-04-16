@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-4">
        <h4 class="fs-xl fw-bold m-0">Satış Hunisi (Funnel) Analizi</h4>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Geri Dön</a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-warning-subtle">
            <h5 class="card-title text-warning-emphasis mb-0">Huni Sızdırmazlık Testi (Drop-off Analizi)</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Müşterilerinizin hangi aşamada en çok kaybolduğunu (Düştüğünü) buradan analiz edebilirsiniz.</p>
            
            <div class="d-flex justify-content-center">
                <div style="width: 500px; height: 300px;">
                    <canvas id="funnelChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const funnelLabels = {!! json_encode(array_keys($funnelStats)) !!};
    const funnelData = {!! json_encode(array_values($funnelStats)) !!};

    const ctx = document.getElementById('funnelChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: funnelLabels,
            datasets: [{
                label: 'Aşamadaki Müşteri Sayısı',
                data: funnelData,
                backgroundColor: '#f59e0b',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y', // Yatay bar grafiği huni gibi durur
            scales: {
                x: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
