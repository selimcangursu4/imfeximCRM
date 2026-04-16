@extends('partials.master')

@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-3">
        <div class="flex-grow-1">
            <h4 class="fs-xl fw-bold m-0">Ürün ve Hizmet Yönetimi</h4>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                <i class="ti ti-plus me-1"></i> Yeni Ürün/Hizmet Ekle
            </button>
            <ol class="breadcrumb m-0 py-0 ms-3 d-none d-md-inline-flex">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                <li class="breadcrumb-item active">Ürünler</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="productsTable" class="table table-hover table-centered mb-0 align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ürün Adı</th>
                            <th>Fiyat</th>
                            <th>Durum</th>
                            <th>Kayıt Tarihi</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Yeni Ürün Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productForm">
                @csrf
                <input type="hidden" name="id" id="productId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fs-xs fw-bold">Ürün/Hizmet Adı <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="productName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-xs fw-bold">Açıklama</label>
                        <textarea name="description" id="productDesc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-xs fw-bold">Fiyat (Opsiyonel)</label>
                        <input type="number" step="0.01" name="price" id="productPrice" class="form-control">
                    </div>
                    <div class="form-check form-switch border p-2 rounded bg-light">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" id="productStatus" checked>
                        <label class="form-check-label fw-bold" for="productStatus">Aktif Olarak Yayında</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSS/JS for DataTables and SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var table = $('#productsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('products.data') }}",
        },
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'name', 
                name: 'name',
                render: function(data) {
                    return `<span class="fw-bold text-dark">${data}</span>`;
                }
            },
            { 
                data: 'price', 
                name: 'price',
                render: function(data) {
                    return parseFloat(data) > 0 ? `₺${parseFloat(data).toLocaleString('tr-TR')}` : '-';
                }
            },
            { 
                data: 'is_active', 
                name: 'is_active',
                render: function(data) {
                    return data ? '<span class="badge bg-success-subtle text-success">Aktif</span>' : '<span class="badge bg-danger-subtle text-danger">Pasif</span>';
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString('tr-TR');
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-icon btn-light edit-product" data-id="${row.id}" title="Düzenle">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-light-danger delete-product" data-id="${row.id}" title="Sil">
                            <i class="ti ti-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
        }
    });

    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('products.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#productModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Hata: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Bilinmeyen hata')
                });
            }
        });
    });

    $(document).on('click', '.edit-product', function() {
        var id = $(this).data('id');
        $.get(`/products/${id}`, function(response) {
            if (response.success) {
                $('#modalTitle').text('Ürünü Düzenle');
                $('#productId').val(response.data.id);
                $('#productName').val(response.data.name);
                $('#productDesc').val(response.data.description);
                $('#productPrice').val(response.data.price);
                $('#productStatus').prop('checked', response.data.is_active);
                $('#productModal').modal('show');
            }
        });
    });

    $(document).on('click', '.delete-product', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu kayıt kalıcı olarak silinecektir!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/products/${id}`,
                    method: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Silindi!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            }
        });
    });

    $('#productModal').on('hidden.bs.modal', function() {
        $('#modalTitle').text('Yeni Ürün Ekle');
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#productStatus').prop('checked', true);
    });
});
</script>
@endsection
