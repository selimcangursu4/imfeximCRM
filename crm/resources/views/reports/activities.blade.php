@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-4">
        <h4 class="fs-xl fw-bold m-0">Aktivite Performans Raporu</h4>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">Geri Dön</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card bg-success-subtle text-center h-100">
                <div class="card-body">
                    <h3 class="text-success mb-1">{{ $completedTasks }}</h3>
                    <p class="text-muted mb-0">Tamamlanan Görev / Hatırlatma</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-warning-subtle text-center h-100">
                <div class="card-body">
                    <h3 class="text-warning mb-1">{{ $pendingTasks }}</h3>
                    <p class="text-muted mb-0">Bekleyen / Açık Görev</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Satışçı / Temsilci Bazlı Performans (Liderlik Tablosu)</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Temsilci</th>
                        <th>Tamamlanan Efor</th>
                        <th>Bekleyen Efor</th>
                        <th>Performans Puanı</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($repStats as $rep)
                        @php
                            $total = $rep->completed_tasks + $rep->pending_tasks;
                            $score = $total > 0 ? round(($rep->completed_tasks / $total) * 100) : 0;
                            $color = $score > 70 ? 'success' : ($score > 40 ? 'warning' : 'danger');
                        @endphp
                        <tr>
                            <td class="fw-medium">{{ $rep->name }}</td>
                            <td><span class="badge bg-success-subtle text-success">{{ $rep->completed_tasks }}</span></td>
                            <td><span class="badge bg-warning-subtle text-warning">{{ $rep->pending_tasks }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $score }}%;"></div>
                                    </div>
                                    <span class="fs-xs text-muted">{{ $score }} Puan</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
