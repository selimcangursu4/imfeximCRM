@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-4">
        <h4 class="fs-xl fw-bold m-0 text-primary"><i data-lucide="cpu" class="icon-sm me-2"></i> Yapay Zeka (AI) Rapor Analisti</h4>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Geri Dön</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card border-primary border-2 shadow-lg">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <div class="spinner-grow spinner-grow-sm text-light me-2" role="status"></div>
                    <h5 class="card-title text-white mb-0">Şirketinizin Durum Analizi (Canlı)</h5>
                </div>
                <div class="card-body p-5">
                    @if(Str::contains($insightText, 'bulunamadı') || Str::contains($insightText, 'hata'))
                        <div class="alert alert-danger text-center">
                            <i data-lucide="alert-triangle" class="fs-1 mb-2 d-block mx-auto text-danger"></i>
                            {{ $insightText }}
                        </div>
                    @else
                        <div class="ai-generated-content lh-lg fs-md text-dark">
                            {!! nl2br(e($insightText)) !!}
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light text-muted fs-xs text-center">
                    Verileriniz ChatGPT entegrasyonu (OpenAI) üzerinden sadece analiz amacıyla anonim metrikler halinde işlenmiştir. İçgörüler tamamen mevcut veri tabanınıza dayanır.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
