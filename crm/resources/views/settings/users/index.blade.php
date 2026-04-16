@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center mb-3">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Kullanıcı Yönetimi</h4>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                    <i class="ti ti-plus me-1"></i> Yeni Kullanıcı Ekle
                </button>
                <ol class="breadcrumb m-0 py-0 ms-3 d-none d-md-inline-flex">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Ayarlar</a></li>
                    <li class="breadcrumb-item active">Kullanıcılar</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover table-centered mb-0 align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Ad Soyad</th>
                                <th>E-posta</th>
                                <th>Rol</th>
                                <th>Departman</th>
                                <th>Kayıt Tarihi</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kullanıcı Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Yeni Kullanıcı Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="userForm">
                    @csrf
                    <input type="hidden" name="id" id="userId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Ad Soyad</label>
                            <input type="text" name="name" id="userName" class="form-control" placeholder="Örn: Ahmet Yılmaz" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">E-posta Adresi</label>
                            <input type="email" name="email" id="userEmail" class="form-control" placeholder="Örn: ahmet@firma.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Rol</label>
                            <select name="role" id="userRole" class="form-select" required>
                                <option value="satis_danismani">Satış Danışmanı</option>
                                <option value="admin">Admin</option>
                                <option value="yonetici">Yönetici</option>
                                <option value="web_developer">Web Developer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Departman</label>
                            <input type="text" name="department" id="userDepartment" class="form-control" placeholder="Örn: Satış">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-xs fw-bold">Şifre</label>
                            <input type="password" name="password" id="userPassword" class="form-control" placeholder="Şifre belirleyin">
                            <small class="text-muted" id="passwordNote">Yeni kullanıcı için şifre zorunludur.</small>
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
            var table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('settings.users.data') }}",
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { 
                        data: 'name', 
                        name: 'name',
                        render: function(data) {
                            return `<div class="d-flex align-items-center gap-2">
                                <div class="avatar-group-item">
                                    <div class="avatar avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                            ${data.charAt(0).toUpperCase()}
                                        </span>
                                    </div>
                                </div>
                                <span class="fw-bold">${data}</span>
                            </div>`;
                        }
                    },
                    { data: 'email', name: 'email' },
                    { 
                        data: 'role', 
                        name: 'role',
                        render: function(data) {
                            const roles = {
                                'admin': '<span class="badge bg-danger-subtle text-danger">Admin</span>',
                                'yonetici': '<span class="badge bg-warning-subtle text-warning">Yönetici</span>',
                                'web_developer': '<span class="badge bg-info-subtle text-info">Web Developer</span>',
                                'satis_danismani': '<span class="badge bg-success-subtle text-success">Satış Danışmanı</span>'
                            };
                            return roles[data] || '<span class="badge bg-secondary-subtle text-secondary">Bilinmeyen</span>';
                        }
                    },
                    { data: 'department', name: 'department', render: data => data || '-' },
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
                                <button class="btn btn-sm btn-icon btn-light edit-user" data-id="${row.id}" title="Düzenle / Detay">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-light-danger delete-user" data-id="${row.id}" title="Sil">
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
            $('#userForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('settings.users.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#userModal').modal('hide');
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
            $(document).on('click', '.edit-user', function() {
                var id = $(this).data('id');
                $.get(`/settings/users/${id}`, function(response) {
                    if (response.success) {
                        $('#modalTitle').text('Kullanıcı Bilgilerini Güncelle');
                        $('#userId').val(response.data.id);
                        $('#userName').val(response.data.name);
                        $('#userEmail').val(response.data.email);
                        $('#userRole').val(response.data.role || 'satis_danismani');
                        $('#userDepartment').val(response.data.department || '');
                        $('#userPassword').val('');
                        $('#passwordNote').text('Şifreyi değiştirmek istemiyorsanız boş bırakın.');
                        $('#userModal').modal('show');
                    }
                });
            });

            // Silme Butonu
            $(document).on('click', '.delete-user', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu kullanıcı hesabı kalıcı olarak silinecektir!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/settings/users/${id}`,
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
                                } else {
                                     Swal.fire({
                                        icon: 'error',
                                        title: 'Hata!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata!',
                                    text: (xhr.responseJSON ? xhr.responseJSON.message : 'Silme işlemi başarısız.')
                                });
                            }
                        });
                    }
                });
            });

            // Modal Kapanınca Temizle
            $('#userModal').on('hidden.bs.modal', function() {
                $('#modalTitle').text('Yeni Kullanıcı Ekle');
                $('#userForm')[0].reset();
                $('#userId').val('');
                $('#passwordNote').text('Yeni kullanıcı için şifre zorunludur.');
            });
        });
    </script>
@endsection
