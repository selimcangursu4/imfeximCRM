@extends('partials.master')
@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <h4 class="fs-xl fw-bold m-0">Bilgi Bankası (Yapay Zeka Eğitimi)</h4>
        </div>

        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Eğitim Dokümanları</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kbModal"
                    onclick="prepareAddModal()">Yeni Doküman Ekle</button>
            </div>
            <div class="card-body">
                <table id="kbTable" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Başlık</th>
                            <th>İçerik Özeti</th>
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
    <div class="modal fade" id="kbModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kbModalTitle">Yeni Doküman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="kbForm">
                    @csrf
                    <input type="hidden" name="id" id="kbId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Doküman Başlığı</label>
                            <input type="text" name="title" id="kbTitle" class="form-control"
                                placeholder="Örn: Fiyat Listemiz" required>
                        </div>
                        <div class="mb-3">
                            <label>Doküman İçeriği (AI bu bilgiyi kullanarak cevap üretecek)</label>
                            <textarea name="content" id="kbContent" class="form-control" rows="8"
                                placeholder="Olası sorulara cevap verebilecek tüm detayları buraya yazınız..."
                                required></textarea>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="kbActive" name="is_active" value="1"
                                checked>
                            <label class="form-check-label" for="kbActive">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        var table;
        $(document).ready(function () {
            table = $('#kbTable').DataTable({
                ajax: '{{ route("knowledge-bases.index") }}',
                columns: [
                    { data: 'title' },
                    { data: 'content', render: function (d) { return d ? (d.length > 50 ? d.substring(0, 50) + '...' : d) : '-'; } },
                    {
                        data: 'is_active', render: function (d) {
                            return d ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Pasif</span>';
                        }
                    },
                    {
                        data: 'id', render: function (id) {
                            return `
                        <button class="btn btn-sm btn-primary edit-btn" data-id="${id}">Düzenle</button>
                        <button class="btn btn-sm btn-danger del-btn" data-id="${id}">Sil</button>
                    `;
                        }
                    }
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json' }
            });

            $('#kbForm').submit(function (e) {
                e.preventDefault();
                let id = $('#kbId').val();
                let url = id ? `/knowledge-bases/${id}` : '{{ route("knowledge-bases.store") }}';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function () {
                        try {
                            var modal = bootstrap.Modal.getInstance(document.getElementById('kbModal'));
                            if (modal) {
                                modal.hide();
                            } else {
                                $('#kbModal').modal('hide');
                            }
                        } catch (err) {
                            console.log('Modal close error:', err);
                            document.getElementById('kbModal').classList.remove('show');
                            document.body.classList.remove('modal-open');
                            $('.modal-backdrop').remove();
                        }

                        table.ajax.reload();
                        Swal.fire('Başarılı', 'İşlem tamamlandı.', 'success');
                    },
                    error: function (xhr) {
                        let msg = 'Kayıt sırasında bir problem oluştu.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        Swal.fire('Hata', msg, 'error');
                    }
                });
            });

            $('#kbTable').on('click', '.edit-btn', function () {
                let id = $(this).data('id');
                $.get(`/knowledge-bases/${id}`, function (data) {
                    $('#kbId').val(data.id);
                    $('#kbTitle').val(data.title);
                    $('#kbContent').val(data.content);
                    $('#kbActive').prop('checked', data.is_active);
                    $('#kbModalTitle').text('Doküman Düzenle');
                    var modal = new bootstrap.Modal(document.getElementById('kbModal'));
                    modal.show();
                });
            });

            $('#kbTable').on('click', '.del-btn', function () {
                if (!confirm('Bu dokümanı silmek istediğinize emin misiniz? Yapay zeka artık buradan beslenemeyecek.')) return;
                let id = $(this).data('id');
                $.ajax({
                    url: `/knowledge-bases/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function () { table.ajax.reload(); }
                });
            });
        });

        function prepareAddModal() {
            $('#kbForm')[0].reset();
            $('#kbId').val('');
            $('#kbModalTitle').text('Yeni Doküman');
        }

        // Ensure the form gets submitted correctly
        $('#kbModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
        });
    </script>
@endsection