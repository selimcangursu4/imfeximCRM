@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-3">
        <div class="flex-grow-1">
            <h4 class="fs-xl fw-bold m-0">Görev & Hatırlatmalar</h4>
        </div>
    </div>

    @if($isAdmin)
    <div class="row mb-3">
        <div class="col-12">
            <div class="nav nav-pills p-1 bg-light rounded-pill d-inline-flex" id="taskFilters">
                <a href="#" class="nav-link rounded-pill px-4 py-2 active filter-btn" data-filter="all">
                    <i class="ti ti-list me-1"></i> Tüm Görevler
                </a>
                <a href="#" class="nav-link rounded-pill px-4 py-2 filter-btn" data-filter="my">
                    <i class="ti ti-user-check me-1"></i> Bana Atananlar
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="card mt-2">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ $isAdmin ? 'Görev Listesi' : 'Bana Atanan Görevler' }}</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="ti ti-plus me-1"></i> Yeni Görev Ekle
            </button>
        </div>
        <div class="card-body">
            <table id="tasksTable" class="table table-striped table-bordered dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>Görev Başlığı</th>
                        <th>İlgili Müşteri</th>
                        <th>Tür</th>
                        <th>Zaman</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Görev/Hatırlatma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTaskForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Başlık</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Açıklama (Opsiyonel)</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Tarih & Saat</label>
                        <input type="datetime-local" name="due_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Tip</label>
                        <select name="type" class="form-select">
                            <option value="call">Arama</option>
                            <option value="meeting">Toplantı</option>
                            <option value="email">E-Posta</option>
                            <option value="generic">Diğer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Müşteri (Opsiyonel)</label>
                        <select name="customer_id" class="form-select">
                            <option value="">Seçiniz</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var currentFilter = 'all';
    
    var table = $('#tasksTable').DataTable({
        ajax: {
            url: '{{ route("tasks.index") }}',
            data: function (d) {
                d.filter = currentFilter;
            }
        },
        columns: [
            { data: 'title' },
            { 
                data: 'customer', 
                render: function(data) { return data ? data.name : '-'; }
            },
            { data: 'type', render: function(d) {
                var map = {call:'Arama', meeting:'Toplantı', email:'E-Posta', generic:'Diğer'};
                return map[d] || d;
            }},
            { data: 'due_date', render: function(d) {
                return new Date(d).toLocaleString('tr-TR');
            }},
            { data: 'status', render: function(d, type, row) {
                let color = d === 'completed' ? 'success' : (d === 'pending' ? 'warning' : 'danger');
                let text = d === 'completed' ? 'Tamamlandı' : (d === 'pending' ? 'Bekliyor' : 'Gecikmiş');
                if (d !== 'completed') {
                    return `<span class="badge bg-${color}">${text}</span><br><button class="btn btn-xs btn-outline-success mt-1 mark-completed" data-id="${row.id}">Tamamla</button>`;
                }
                return `<span class="badge bg-${color}">${text}</span>`;
            }},
            { data: 'id', render: function(id) {
                return `<button class="btn btn-sm btn-danger delete-task" data-id="${id}"><i class="ti ti-trash"></i></button>`;
            }}
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json' }
    });

    // Filter Buttons
    $('.filter-btn').click(function(e) {
        e.preventDefault();
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        table.ajax.reload();
    });

    $('#addTaskForm').submit(function(e) {
        e.preventDefault();
        $.post('{{ route("tasks.store") }}', $(this).serialize(), function() {
            $('#addTaskModal').modal('hide');
            table.ajax.reload();
            Swal.fire('Başarılı', 'Görev eklendi', 'success');
        });
    });

    $('#tasksTable').on('click', '.mark-completed', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/tasks/${id}`,
            method: 'PUT',
            data: { status: 'completed', _token: '{{ csrf_token() }}' },
            success: function() { table.ajax.reload(); }
        });
    });

    $('#tasksTable').on('click', '.delete-task', function() {
        if(!confirm('Emin misiniz?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: `/tasks/${id}`,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function() { table.ajax.reload(); }
        });
    });
});
</script>
@endsection
