<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>İmfexim CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Selimcan Gürsu | Full Stack Web Developer">
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}">
    <script src="{{asset('assets/js/config.js')}}"></script>
    <link href="{{asset('assets/css/vendors.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="wrapper">
        @include('partials.sidebar')
        @include('partials.header')
        <div class="content-page">
            @yield('content')
            @yield('main')
            @include('partials.footer')
        </div>
    </div>

    <!-- Explicitly load jQuery to prevent reference errors -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{asset('assets/js/vendors.min.js')}}"></script>
    <script src="{{asset('assets/js/app.js')}}"></script>
    <script src="{{asset('assets/plugins/chartjs/chart.umd.js')}}"></script>
    <script src="{{asset('assets/js/pages/custom-table.js')}}"></script>
    <script src="{{asset('assets/js/pages/dashboard.js')}}"></script>

    @yield('scripts')
</body>

</html>