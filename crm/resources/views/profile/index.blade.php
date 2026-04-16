@extends('partials.master')
@section('content')
<div class="container-fluid">
    <div class="page-title-head d-flex align-items-center mb-4">
        <h4 class="fs-xl fw-bold m-0">Profilim</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-3"><i data-lucide="check-circle" class="icon-sm me-2"></i> {{ session('success') }}</div>
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

    <div class="row g-4">
        <!-- Sol: Profil Kartı -->
        <div class="col-lg-4">
            <div class="card text-center overflow-hidden">
                <div class="bg-primary-subtle" style="height: 100px;"></div>
                <div class="card-body" style="margin-top: -50px;">
                    <div class="mb-3">
                        @if($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profil Fotoğrafı" class="avatar-xl rounded-circle border border-4 border-white shadow-sm" style="object-fit: cover;">
                        @else
                            <div class="avatar-xl rounded-circle border border-4 border-white shadow-sm bg-light d-flex align-items-center justify-content-center mx-auto text-primary fw-bold fs-2">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="fs-lg fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ ucfirst(str_replace('_', ' ', $user->role ?? 'Kullanıcı')) }}</p>
                    
                    @if($user->department)
                        <span class="badge bg-info-subtle text-info mb-3 px-3 py-1">{{ $user->department }} Departmanı</span>
                    @endif

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <button type="button" class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i data-lucide="edit-2" class="icon-sm me-1"></i> Bilgileri Güncelle
                        </button>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top text-start">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex align-items-center"><i data-lucide="mail" class="icon-sm text-muted me-2"></i> {{ $user->email }}</li>
                        <li class="mb-2 d-flex align-items-center"><i data-lucide="phone" class="icon-sm text-muted me-2"></i> {{ $user->phone ?? 'Belirtilmedi' }}</li>
                        <li class="mb-2 d-flex align-items-center"><i data-lucide="calendar-heart" class="icon-sm text-muted me-2"></i> {{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d.m.Y') : 'Belirtilmedi' }} (Doğum Tarihi)</li>
                        <li class="mb-2 d-flex align-items-center"><i data-lucide="user" class="icon-sm text-muted me-2"></i> {{ $user->gender ?? 'Belirtilmedi' }}</li>
                        <li class="mb-0 d-flex align-items-center"><i data-lucide="clock" class="icon-sm text-muted me-2"></i> Katılım: {{ $user->created_at->format('d.m.Y') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sağ: Şifre ve Diğer Kartlar -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-dashed">
                    <h4 class="card-title mb-0"><i data-lucide="lock" class="icon-sm me-2"></i> Şifre Değiştir</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium">Mevcut Şifre</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Yeni Şifre</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Yeni Şifre (Tekrar)</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button class="btn btn-dark">Şifreyi Güncelle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bilgileri Güncelle Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-dashed">
                <h5 class="modal-title">Kişisel Bilgilerimi Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    
                    <div class="mb-3 text-center">
                        <label class="form-label fw-medium w-100 text-start">Profil Fotoğrafı</label>
                        <input type="file" name="profile_photo" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted d-block text-start mt-1">Maksimum 2MB ve .jpg, .png formatında olmalıdır. Boş bırakırsanız mevcut fotoğrafınız korunur.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium">İsim Soyisim <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">E-Posta Adresi <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Telefon Numarası</label>
                            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" placeholder="Örn: 05xx xxx xx xx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Doğum Tarihi</label>
                            <input type="date" name="birth_date" class="form-control" value="{{ $user->birth_date }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Cinsiyet</label>
                            <select name="gender" class="form-select">
                                <option value="Belirtilmemiş" {{ $user->gender == 'Belirtilmemiş' ? 'selected' : '' }}>Belirtmek İstemiyorum</option>
                                <option value="Erkek" {{ $user->gender == 'Erkek' ? 'selected' : '' }}>Erkek</option>
                                <option value="Kadın" {{ $user->gender == 'Kadın' ? 'selected' : '' }}>Kadın</option>
                                <option value="Diğer" {{ $user->gender == 'Diğer' ? 'selected' : '' }}>Diğer</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light-subtle">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
