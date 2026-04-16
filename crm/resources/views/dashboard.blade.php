@extends('partials.master')
@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Kontrol Paneli</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">İMFEXİM CRM</a></li>

                    <li class="breadcrumb-item active">Kontrol Paneli</li>
                </ol>
            </div>
        </div>

        <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1">
            <!-- Total Customers Widget -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="avatar fs-60 avatar-img-size flex-shrink-0">
                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-24">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-2 fw-normal">{{ number_format($totalCustomers) }}</h3>
                                <p class="mb-0 text-muted"><span>Toplam Müşteri</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Deals Widget -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="avatar fs-60 avatar-img-size flex-shrink-0">
                                <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-24">
                                    <i class="ti ti-target"></i>
                                </span>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-2 fw-normal">{{ number_format($activeDealsCount) }}</h3>
                                <p class="mb-0 text-muted"><span>Aktif Fırsatlar</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Sales -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="avatar fs-60 avatar-img-size flex-shrink-0">
                                <span class="avatar-title bg-success-subtle text-success rounded-circle fs-24">
                                    <i class="ti ti-currency-dollar"></i>
                                </span>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-2 fw-normal">₺{{ number_format($todaySales, 2) }}</h3>
                                <p class="mb-0 text-muted"><span>Bugünkü Satış</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="avatar fs-60 avatar-img-size flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24">
                                    <i class="ti ti-chart-bar"></i>
                                </span>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-2 fw-normal">₺{{ number_format($monthlySales, 2) }}</h3>
                                <p class="mb-0 text-muted"><span>Aylık Ciro</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Funnel Grafiği -->
                            <div class="col-xxl-4 col-xl-5 order-xl-1 order-xxl-0">
                                <div class="p-4 border-end border-dashed h-100">
                                    <h4 class="card-title mb-0">Huni Dağılımı</h4>
                                    <p class="text-muted fs-xs">
                                        Fırsatların aşamalara göre dağılımı. Açılış oranı: %{{ $conversionRate }}
                                    </p>
                                    <div class="row mt-4">
                                        <div class="col-lg-12">
                                            <div style="height: 300px;">
                                                <canvas id="crm-funnel-chart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Satış Grafiği -->
                            <div class="col-xxl-8 col-xl-7 order-xl-3 order-xxl-1">
                                <div class="px-4 py-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h4 class="card-title">Son 7 Gün Satış Eğilimi</h4>
                                    </div>
                                    <div dir="ltr">
                                        <div class="mt-3" style="height: 330px;">
                                            <canvas id="crm-sales-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Görevler ve Hatırlatmalar -->
            <div class="col-xxl-4">
                <div class="card h-100">
                    <div class="card-header border-dashed">
                        <h4 class="card-title mb-0">Görevler & Hatırlatmalar</h4>
                    </div>
                    <div class="card-body p-0" style="max-height: 400px; overflow-y:auto;">
                        <div class="p-3">
                            @if($overdueTasks->count() > 0)
                                <h6 class="fs-xs text-danger text-uppercase mb-2">Gecikmiş ({{ $overdueTasks->count() }})</h6>
                                @foreach($overdueTasks as $task)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle">
                                                <i class="ti ti-alert-circle"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fs-sm mb-1">{{ $task->title }}</h6>
                                            <p class="fs-xs text-muted mb-0">{{ optional($task->customer)->name ?? 'Müşteri Yok' }} - {{ $task->due_date->format('d.m.Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <h6 class="fs-xs text-primary text-uppercase mb-2 mt-4">Bugün ({{ $todayTasks->count() }})</h6>
                            @forelse($todayTasks as $task)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-sm flex-shrink-0 me-3">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                            <i class="ti ti-calendar-event"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fs-sm mb-1">{{ $task->title }}</h6>
                                        <p class="fs-xs text-muted mb-0">{{ optional($task->customer)->name ?? 'Müşteri Yok' }} - {{ $task->due_date->format('H:i') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted fs-sm text-center py-3">Bugün için görev yok.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Son Mesajlar Omnichannel -->
            <div class="col-xxl-4">
                <div class="card h-100">
                    <div class="card-header border-dashed pb-2">
                        <h4 class="card-title mb-0">Son Sohbetler (Omnichannel)</h4>
                    </div>
                    <div class="card-body p-0" style="max-height: 400px; overflow-y:auto;">
                        <ul class="list-group list-group-flush border-dashed">
                            @forelse($recentConversations as $conv)
                                <li class="list-group-item d-flex align-items-center p-3">
                                    <div class="avatar-sm flex-shrink-0 me-3">
                                        @php
                                            $icon = 'ti-message'; $bg = 'bg-secondary';
                                            $ph = strtolower($conv->platform ?? 'whatsapp');
                                            if ($ph == 'whatsapp') { $icon = 'ti-brand-whatsapp'; $bg = 'bg-success'; }
                                            if ($ph == 'instagram') { $icon = 'ti-brand-instagram'; $bg = 'bg-danger'; }
                                            if ($ph == 'telegram') { $icon = 'ti-brand-telegram'; $bg = 'bg-info'; }
                                        @endphp
                                        <span class="avatar-title {{ $bg }}-subtle text-{{ str_replace('bg-','',$bg) }} rounded-circle">
                                            <i class="ti {{ $icon }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 text-truncate">
                                        <h6 class="fs-sm mb-1">{{ optional($conv->customer)->name ?? 'Bilinmeyen Müşteri' }}</h6>
                                        <p class="fs-xs text-muted mb-0 text-truncate">{{ optional($conv->latestMessage)->body ?? 'Mesaj yok' }}</p>
                                    </div>
                                    <div class="text-end ms-2">
                                        <span class="fs-xs text-muted">{{ $conv->updated_at->diffForHumans() }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item p-4 text-center text-muted">Henüz sohbet yok.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Takım Performans Analizi -->
            <div class="col-xxl-4">
                <div class="card h-100">
                    <div class="card-header border-dashed pb-2">
                        <h4 class="card-title mb-0">Temsilci Performansı</h4>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="border-0">Temsilci</th>
                                    <th class="border-0 text-center">Fırsat (Win)</th>
                                    <th class="border-0 text-end">Ciro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPerformers as $perf)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title bg-primary text-white rounded-circle fs-sm">{{ substr($perf->name, 0, 1) }}</span>
                                                </div>
                                                <h6 class="fs-sm fw-medium mb-0">{{ $perf->name }}</h6>
                                            </div>
                                        </td>
                                        <td class="text-center"><span class="badge bg-success-subtle text-success">{{ $perf->deals_won }}</span></td>
                                        <td class="text-end fw-semibold">₺{{ number_format($perf->total_sales, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Performans verisi yok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Müşteri & Lead Akışı -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-dashed">
                        <h4 class="card-title mb-0">Son Gelen Müşteri & Leadler</h4>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="border-0">İsim</th>
                                    <th class="border-0">Şirket</th>
                                    <th class="border-0">Durum</th>
                                    <th class="border-0">Değer</th>
                                    <th class="border-0">Tarih</th>
                                    <th class="border-0 text-end">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeads as $lead)
                                    @php
                                        $sClass = 'bg-secondary';
                                        if($lead->status == 'Lead') $sClass = 'bg-primary';
                                        elseif($lead->status == 'Kazanıldı') $sClass = 'bg-success';
                                        elseif($lead->status == 'Kaybedildi') $sClass = 'bg-danger';
                                        elseif($lead->status == 'Pazarlık') $sClass = 'bg-warning text-dark';
                                    @endphp
                                    <tr>
                                        <td><h6 class="fs-sm fw-medium mb-0">{{ $lead->name }}</h6></td>
                                        <td><span class="text-muted fs-sm">{{ $lead->company ?? '-' }}</span></td>
                                        <td><span class="badge {{ $sClass }}-subtle text-{{ str_replace('bg-','',str_replace(' text-dark','',$sClass)) }}">{{ $lead->status }}</span></td>
                                        <td class="fw-semibold">₺{{ number_format($lead->deal_value, 2) }}</td>
                                        <td class="text-muted fs-sm">{{ $lead->created_at->format('d.m.Y') }}</td>
                                        <td class="text-end">
                                            <a href="/customers/{{ $lead->id }}" class="btn btn-sm btn-light">Profili Gör</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Kayıt bulunamadı.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js and Init Scripts -->
    <script src="{{asset('assets/plugins/chartjs/chart.umd.js')}}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CRM Funnel Doughnut Chart
            var ctxFunnel = document.getElementById('crm-funnel-chart');
            if(ctxFunnel) {
                new Chart(ctxFunnel.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($funnelLabels) !!},
                        datasets: [{
                            data: {!! json_encode($funnelValues) !!},
                            backgroundColor: [
                                '#3e60d5', '#47ad77', '#fa5c7c', '#ffbc00', '#39afd1', '#e3eaef', '#313a46'
                            ],
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' }
                        }
                    }
                });
            }

            // CRM Sales Trend Line Chart
            var ctxSales = document.getElementById('crm-sales-chart');
            if(ctxSales) {
                new Chart(ctxSales.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($salesChartLabels) !!},
                        datasets: [{
                            label: 'Kazanılan Satışlar (₺)',
                            data: {!! json_encode($salesChartData) !!},
                            borderColor: '#47ad77',
                            backgroundColor: 'rgba(71, 173, 119, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#47ad77'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
@endsection