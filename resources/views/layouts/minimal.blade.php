<!doctype html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title> {{config('app.name')}} </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-meta-component></x-meta-component>

    <!-- CSS files -->
    <link href="{{asset('assets/css/tabler.min159a.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/css/tabler-flags.min159a.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/css/tabler-payments.min159a.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/css/tabler-vendors.min159a.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/css/demo.min159a.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/css/icons.css')}}" rel="stylesheet" />
    @yield('styles')
</head>

<body>
    @yield('content')
</body>

<script src="{{asset('assets/libs/jquery/jquery-3.6.1.min.js')}}"></script>
<script src="{{asset('assets/js/tabler.min159a.js')}}" defer></script>
<script src="{{asset('assets/js/demo.min159a.js')}}" defer></script>
<script src="{{ asset('assets/libs/sweetalert/sweetalert2.all.min.js')}}" defer></script>
<script src="{{ asset('assets/libs/toastr/toastr.min.js') }}" defer></script> 
@yield('scripts')

</html>