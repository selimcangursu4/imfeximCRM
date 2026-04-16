@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-4">
        <h4 class="fs-xl fw-bold m-0">Zaman ve Süre (Verimlilik) Analizi</h4>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Geri Dön</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-4">
            <div class="card bg-secondary text-white text-center h-100">
                <div class="card-body">
                    <h1 class="mb-1 fw-bold text-white">{{ $avgClosingTime }} Gün</h1>
                    <p class="mb-0 text-white-50">Ortalama Satış Kapanma Süresi</p>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Hızlı Kapanan Satışlar Modeli</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Bu tabloda satışa dönüşmüş olan (Kazanılmış) fırsatların sisteme girildiği tarih ile kazanıldığı tarih arasındaki geçen süre hesaplanmaktadır. Daha gelişmiş "aşama bekleme süresi" özellikleri ilerleyen versiyonlarda aktive edilecektir.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
