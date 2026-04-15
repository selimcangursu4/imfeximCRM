@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center mb-3">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Hazır Mesaj Ayarları</h4>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickReplyModal">
                    <i class="ti ti-plus me-1"></i> Yeni Hazır Mesaj Ekle
                </button>
                <ol class="breadcrumb m-0 py-0 ms-3 d-none d-md-inline-flex">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Ayarlar</a></li>
                    <li class="breadcrumb-item active">Hazır Mesajlar</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="quickRepliesTable" class="table table-hover table-centered mb-0 align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Mesaj Başlığı / Tetikleyici</th>
                                <th>Mesaj İçeriği</th>
                                <th>Oluşturulma</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Hazır Mesaj Modalı -->
    <div class="modal fade" id="quickReplyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Yeni Hazır Mesaj Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickReplyForm">
                    @csrf
                    <input type="hidden" name="id" id="replyId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Başlık / Tetikleyici</label>
                            <input type="text" name="title" id="replyTitle" class="form-control" placeholder="Örn: Selamlama" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Mesaj İçeriği</label>
                            <textarea name="content" id="replyContent" class="form-control" rows="5" placeholder="Müşteriye gönderilecek mesaj..." required></textarea>
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
            var table = $('#quickRepliesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('settings.quick-replies.data') }}",
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { 
                        data: 'title', 
                        name: 'title',
                        render: function(data) {
                            return `<span class="fw-bold text-primary">${data}</span>`;
                        }
                    },
                    { 
                        data: 'content', 
                        name: 'content',
                        render: function(data) {
                            return data.length > 100 ? data.substring(0, 100) + '...' : data;
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
                                <button class="btn btn-sm btn-icon btn-light edit-reply" data-id="${row.id}" title="Düzenle">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-light-danger delete-reply" data-id="${row.id}" title="Sil">
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

            // Form Submit (Ekle/Düzenle)
            $('#quickReplyForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('settings.quick-replies.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#quickReplyModal').modal('hide');
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

            // Düzenleme Butonu
            $(document).on('click', '.edit-reply', function() {
                var id = $(this).data('id');
                $.get(`/settings/quick-replies/${id}`, function(response) {
                    if (response.success) {
                        $('#modalTitle').text('Hazır Mesajı Düzenle');
                        $('#replyId').val(response.data.id);
                        $('#replyTitle').val(response.data.title);
                        $('#replyContent').val(response.data.content);
                        $('#quickReplyModal').modal('show');
                    }
                });
            });

            // Silme Butonu
            $(document).on('click', '.delete-reply', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu hazır mesaj kalıcı olarak silinecektir!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/settings/quick-replies/${id}`,
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

            // Modal Kapanınca Temizle
            $('#quickReplyModal').on('hidden.bs.modal', function() {
                $('#modalTitle').text('Yeni Hazır Mesaj Ekle');
                $('#quickReplyForm')[0].reset();
                $('#replyId').val('');
            });
        });
    </script>
@endsection
