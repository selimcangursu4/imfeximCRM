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

    <script src="{{asset('assets/js/vendors.min.js')}}"></script>
    <script src="{{asset('assets/js/app.js')}}"></script>
    <script src="{{asset('assets/plugins/chartjs/chart.umd.js')}}"></script>
    <script src="{{asset('assets/js/pages/custom-table.js')}}"></script>
    <script src="{{asset('assets/js/pages/dashboard.js')}}"></script>
</body>

</html>