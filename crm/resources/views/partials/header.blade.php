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
                        <span class="badge text-bg-danger badge-circle topbar-badge">5</span>
                    </button>

                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                        <div class="px-3 py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-md fw-semibold">Bildirimler</h6>
                                </div>
                                <div class="col text-end">
                                    <a href="#!" class="badge badge-soft-success badge-label py-1">07 Yeni Bildirim</a>
                                </div>
                            </div>
                        </div>

                        <div style="max-height: 300px;" data-simplebar>
                            <!-- Notification 1 -->
                            <div class="dropdown-item notification-item py-2 text-wrap" id="message-1">
                                <span class="d-flex align-items-center gap-3">
                                    <span class="flex-shrink-0 position-relative">
                                        <img src="assets/images/users/user-4.jpg" class="avatar-md rounded-circle"
                                            alt="User Avatar">
                                        <span class="position-absolute rounded-pill bg-success notification-badge">
                                            <i class="ti ti-bell align-middle"></i>
                                            <span class="visually-hidden">unread notification</span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1 text-muted">
                                        <span class="fw-medium text-body">Emily Johnson</span> commented on a task in
                                        <span class="fw-medium text-body">Design Sprint</span><br>
                                        <span class="fs-xs">12 minutes ago</span>
                                    </span>
                                    <button type="button"
                                        class="flex-shrink-0 text-muted btn btn-link p-0 position-absolute end-0 me-2 d-none noti-close-btn"
                                        data-dismissible="#message-1">
                                        <i class="ti ti-xbox-x-filled fs-xxl"></i>
                                    </button>
                                </span>
                            </div>




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
                            <h5 class="my-0">Geneva</h5>
                            <i class="ti ti-chevron-down align-middle"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- Header -->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Hoşgeldin!</h6>
                        </div>

                        <!-- My Profile -->
                        <a href="users-profile.html" class="dropdown-item">
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