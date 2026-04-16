@extends('partials.master')
@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center mb-4">
            <h4 class="fs-xl fw-bold m-0">Gelişmiş Raporlar ve Analizler</h4>
        </div>

        <div class="row g-3">
            <!-- Satış Raporu -->
            <div class="col-md-3">
                <div class="card h-100 text-center border-primary border-opacity-25">
                    <div class="card-body">
                        <div
                            class="avatar-md bg-primary-subtle text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i data-lucide="trending-up" class="fs-2"></i>
                        </div>
                        <h5 class="card-title">Satış Raporları</h5>
                        <p class="text-muted fs-sm">Kazanılan/Kaybedilen fırsatlar ve genel satış verileri.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route('reports.sales') }}" class="btn btn-outline-primary w-100">Görüntüle</a>
                    </div>
                </div>
            </div>

            <!-- Huni Raporu -->
            <div class="col-md-3">
                <div class="card h-100 text-center border-warning border-opacity-25">
                    <div class="card-body">
                        <div
                            class="avatar-md bg-warning-subtle text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i data-lucide="filter" class="fs-2"></i>
                        </div>
                        <h5 class="card-title">Satış Hunisi Raporu</h5>
                        <p class="text-muted fs-sm">Aşamalar arası dönüşüm ve kayıp (Drop-off) analizi.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route('reports.funnel') }}" class="btn btn-outline-warning w-100">Görüntüle</a>
                    </div>
                </div>
            </div>



            <!-- Aktivite -->
            <div class="col-md-3">
                <div class="card h-100 text-center border-success border-opacity-25">
                    <div class="card-body">
                        <div
                            class="avatar-md bg-success-subtle text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i data-lucide="activity" class="fs-2"></i>
                        </div>
                        <h5 class="card-title">Aktivite Performans Analizi</h5>
                        <p class="text-muted fs-sm">Ekip bazlı günlük/haftalık aktivite ve görev başarı oranları.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route('reports.activities') }}" class="btn btn-outline-success w-100">Görüntüle</a>
                    </div>
                </div>
            </div>

            <!-- Zaman & Süre -->
            <div class="col-md-3">
                <div class="card h-100 text-center border-secondary border-opacity-25">
                    <div class="card-body">
                        <div
                            class="avatar-md bg-secondary-subtle text-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i data-lucide="clock" class="fs-2"></i>
                        </div>
                        <h5 class="card-title">Zaman & Süre Analizi</h5>
                        <p class="text-muted fs-sm">Ortalama kapanma süresi ve verimlilik ölçümleri.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route('reports.time') }}" class="btn btn-outline-secondary w-100">Görüntüle</a>
                    </div>
                </div>
            </div>

            <!-- Gelir & Tahmin -->
            <div class="col-md-3">
                <div class="card h-100 text-center border-danger border-opacity-25">
                    <div class="card-body">
                        <div
                            class="avatar-md bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i data-lucide="dollar-sign" class="fs-2"></i>
                        </div>
                        <h5 class="card-title">Gelir & Tahmin (Forecast)</h5>
                        <p class="text-muted fs-sm">Gerçekleşen ciro ve açık fırsatlardan beklenen tahmini kazanç.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route('reports.revenue') }}" class="btn btn-outline-danger w-100">Görüntüle</a>
                    </div>
                </div>
            </div>

            <!-- Pazarlama & Kanal -->
            <div class="col-md-3">
                <div class="card h-100 text-center border-dark border-opacity-25">
                    <div class="card-body">
                        <div
                            class="avatar-md bg-dark-subtle text-dark rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i data-lucide="share-2" class="fs-2"></i>
                        </div>
                        <h5 class="card-title">Pazarlama & Kanal Raporu</h5>
                        <p class="text-muted fs-sm">Hangi kanal (WhatsApp, Instagram vb.) daha kârlı analiz edin.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route('reports.marketing') }}" class="btn btn-outline-dark w-100">Görüntüle</a>
                    </div>
                </div>
            </div>

            <!-- AI Analiz -->
            <div class="col-md-3">
                <div class="card h-100 text-center border-primary">
                    <div class="card-body">
                        <div
                            class="avatar-md bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-lg">
                            <i data-lucide="cpu" class="fs-2"></i>
                        </div>
                        <h5 class="card-title text-primary fw-bold">Yapay Zeka Analisti</h5>
                        <p class="text-muted fs-sm">Sizin için tüm satış stratejinizi veriler ışığında yorumlatın.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route('reports.ai') }}" class="btn btn-primary w-100 shadow">AI Raporunu Çalıştır <i
                                data-lucide="zap" class="ms-1 icon-sm"></i></a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection