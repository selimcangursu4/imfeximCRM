<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>CRM Giriş</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link href="{{ asset('assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="auth-page-wrapper d-flex align-items-center min-vh-100 py-4 py-lg-5">
        @yield('content')
    </div>

    <script src="{{ asset('assets/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
