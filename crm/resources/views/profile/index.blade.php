@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center">
        <h4 class="fs-xl fw-bold m-0">Profilim</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-dashed">
                    <h4 class="card-title mb-0">Genel Bilgiler</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label>İsim Soyisim</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label>E-Posta Adresi</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <button class="btn btn-primary">Bilgileri Güncelle</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-dashed">
                    <h4 class="card-title mb-0">Şifre Değiştir</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label>Mevcut Şifre</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Yeni Şifre</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Yeni Şifre (Sistemin Onayı)</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button class="btn btn-danger">Şifreyi Değiştir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
