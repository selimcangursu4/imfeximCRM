@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-4">
        <h4 class="fs-xl fw-bold m-0">Kanal ve Pazarlama Raporu</h4>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Geri Dön</a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-dark-subtle">
            <h5 class="card-title text-dark mb-0">İletişim Kanalı Performansı (Omnichannel Entegrasyon)</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Müşterilerinizin markanızla en çok hangi platformda (WhatsApp, Instagram vb.) etkileşime geçtiğini analiz edin.</p>
            
            <div class="d-flex justify-content-center">
                <div style="width: 500px; height: 300px;">
                    <canvas id="marketingChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const mLabels = {!! json_encode(array_keys($channelStats)) !!}.map(l => l.toUpperCase());
    const mData = {!! json_encode(array_values($channelStats)) !!};

    const ctx = document.getElementById('marketingChart').getContext('2d');
    new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: mLabels,
            datasets: [{
                label: 'Sohbet Sayısı',
                data: mData,
                backgroundColor: [
                    'rgba(16, 185, 129, 0.7)', // WhatsApp Green
                    'rgba(236, 72, 153, 0.7)', // Instagram Pink
                    'rgba(59, 130, 246, 0.7)',  // Default Blue
                    'rgba(245, 158, 11, 0.7)'   // Warning Orange
                ],
            }]
        }
    });
</script>
@endsection
