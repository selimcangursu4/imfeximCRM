<header class="app-topbar">
    <div class="container-fluid topbar-menu">
        <div class="d-flex align-items-center gap-2">
            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="index.html" class="logo-light">
                    İMFEXİM CRM
                </a>

                <!-- Logo Dark -->
                <a href="index.html" class="logo-dark">
                    İMFEXİM CRM
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="sidenav-toggle-button btn btn-default btn-icon">
                <i class="ti ti-menu-4 fs-22"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="ti ti-menu-4 fs-22"></i>
            </button>



        </div> <!-- .d-flex-->

        <div class="d-flex align-items-center gap-2">

            <!-- Notification Dropdown -->
            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown"
                        data-bs-offset="0,24" type="button" data-bs-auto-close="outside" aria-haspopup="false"
                        aria-expanded="false">
                        <i data-lucide="bell" class="fs-xxl"></i>
                        @if(auth()->user() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="badge text-bg-danger badge-circle topbar-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </button>

                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                        <div class="px-3 py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-md fw-semibold">Bildirimler</h6>
                                </div>
                                <div class="col text-end">
                                    <a href="#!" class="badge badge-soft-success badge-label py-1">{{ auth()->user() ? auth()->user()->unreadNotifications->count() : 0 }} Yeni Mesaj</a>
                                </div>
                            </div>
                        </div>

                        <div style="max-height: 300px;" data-simplebar>
                            @if(auth()->user())
                                @forelse(auth()->user()->notifications->take(10) as $notification)
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item notification-item py-2 text-wrap {{ $notification->read_at ? 'bg-light' : '' }}">
                                        <span class="d-flex align-items-center gap-3">
                                            <span class="flex-shrink-0 position-relative">
                                                @php
                                                    $p = $notification->data['provider'] ?? 'chat';
                                                    $i = 'ti-message'; $c = 'primary';
                                                    if($p == 'whatsapp') { $i = 'ti-brand-whatsapp'; $c = 'success'; }
                                                    if($p == 'instagram') { $i = 'ti-brand-instagram'; $c = 'danger'; }
                                                    if($p == 'telegram') { $i = 'ti-brand-telegram'; $c = 'info'; }
                                                @endphp
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-{{ $c }}-subtle text-{{ $c }} rounded-circle">
                                                        <i class="ti {{ $i }}"></i>
                                                    </span>
                                                </div>
                                                @if(!$notification->read_at)
                                                <span class="position-absolute end-0 top-0 rounded-circle bg-danger p-1">
                                                    <span class="visually-hidden">unread notification</span>
                                                </span>
                                                @endif
                                            </span>
                                            <span class="flex-grow-1 text-muted">
                                                <span class="fw-medium text-body">{{ $notification->data['customer_name'] ?? 'Müşteri' }}</span><br>
                                                <span class="fs-xs">{{ Str::limit($notification->data['body'] ?? '', 40) }}</span><br>
                                                <span class="fs-xs">{{ $notification->created_at->diffForHumans() }}</span>
                                            </span>
                                        </span>
                                    </a>
                                    @php $notification->markAsRead(); @endphp
                                @empty
                                    <div class="p-3 text-center text-muted">Açıklanacak bildirim yok.</div>
                                @endforelse
                            @endif
                        </div>


                        <!-- All-->
                        <a href="javascript:void(0);"
                            class="dropdown-item text-center text-reset text-decoration-underline link-offset-2 fw-bold notify-item border-top border-light py-2">
                            Tüm Bildirimleri İncele
                        </a>

                    </div> <!-- End dropdown-menu -->
                </div> <!-- end dropdown-->
            </div> <!-- end topbar item-->



            <!-- FullScreen -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" type="button" data-toggle="fullscreen">
                    <i data-lucide="maximize" class="fs-xxl fullscreen-off"></i>
                    <i data-lucide="minimize" class="fs-xxl fullscreen-on"></i>
                </button>
            </div>

            <!-- Light/Dark Mode Button -->
            <div class="topbar-item d-none">
                <button class="topbar-link" id="light-dark-mode" type="button">
                    <i data-lucide="moon" class="fs-xxl mode-light-moon"></i>
                </button>
            </div>


            <!-- User Dropdown -->
            <div class="topbar-item nav-user">
                <div class="dropdown">
                    <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown"
                        data-bs-offset="0,19" href="#!" aria-haspopup="false" aria-expanded="false">
                        <img src="assets/images/users/user-3.jpg" width="32" class="rounded-circle me-lg-2 d-flex"
                            alt="user-image">
                        <div class="d-lg-flex align-items-center gap-1 d-none">
                            <h5 class="my-0">{{ auth()->user()->name ?? 'Kullanıcı' }}</h5>
                            <i class="ti ti-chevron-down align-middle"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- Header -->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Hoşgeldin!</h6>
                        </div>

                        <!-- My Profile -->
                        <a href="{{ route('profile.index') }}" class="dropdown-item">
                            <i class="ti ti-user-circle me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Profilim</span>
                        </a>


                        <!-- Support -->
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="ti ti-headset me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Destek Talep Et</span>
                        </a>

                        <!-- Divider -->
                        <div class="dropdown-divider"></div>


                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="dropdown-item fw-semibold border-0 bg-transparent text-start w-100">
                                <i class="ti ti-logout-2 me-1 fs-17 align-middle"></i>
                                <span class="align-middle">Çıkış Yap</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>


        </div>
    </div>
</header>