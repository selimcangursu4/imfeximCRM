@extends('partials.master')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="fs-xl fw-bold m-0">Ayarlar</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Kontrol Paneli</a></li>
                    <li class="breadcrumb-item active">Ayarlar</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Meta Developer API Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <p>Meta Developer API (Instagram, WhatsApp) ayarlarınızı buradan yapabilirsiniz.</p>
                        <a href="{{ route('settings.api.index') }}" class="btn btn-primary w-100">Görüntüle</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Telegram API Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <p>Telegram Developer API (Telegram) ayarlarınızı buradan yapabilirsiniz.</p>
                        <a href="{{ route('settings.telegram.index') }}" class="btn btn-primary w-100">Görüntüle</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Gmail API Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <p>Gmail API ayarlarınızı buradan yapabilirsiniz tüm mail işlemlerinizi panelden takip
                            edebilirsiniz.</p>
                        <a href="" class="btn btn-primary w-100">Görüntüle</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Yapay Zeka API Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <p>ChatGPT veya Gemini API'nizi yapılandırın. Bilgi bankasına göre otomatik mesaj yanıtlarını buradan yönetin.</p>
                        <a href="{{ route('settings.ai.index') }}" class="btn btn-primary w-100">Görüntüle</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Kullanıcılar</h5>
                    </div>
                    <div class="card-body">
                        <p>Firmanıza ait tüm kullanıcı hesaplarını yönetebilir,yeni kullanıcı ekleyebilir çıkartabilirsiniz.
                        </p>
                        <a href="{{ route('settings.users.index') }}" class="btn btn-primary w-100">Görüntüle</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Hazır Mesaj Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <p>Hızlı sohbet için önceden tanımlanmış mesajlarınızı buradan yönetebilirsiniz.</p>
                        <a href="{{ route('settings.quick-replies.index') }}" class="btn btn-primary w-100">Görüntüle</a>
                    </div>
                </div>
            </div>
        </div>



    </div>
@endsection