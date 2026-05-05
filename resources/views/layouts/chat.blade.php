<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> {{config('app.name')}} - {{$page}} </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-meta-component></x-meta-component>
    <link href="{{asset('assets/css/icons.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/argon.css')}}?v=2.0.4" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/libs/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/chatui.css')}}">
</head>

<body>
    <!-- partial:index.partial.html -->
    <div class="app">
        <x-chat-app.header-component></x-chat-app.header-component>
        <div class="wrapper">
            <x-chat-app.contact-component></x-chat-app.contact-component>
            <div class="chat-area">

                @yield('content')

            </div>
        </div>
    </div>

    <script src="{{asset('assets/libs/jquery/jquery-3.6.1.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}" ></script>
    <script src="{{ asset('assets/libs/sweetalert/sweetalert2.all.min.js')}}" defer></script>
    <script src="{{ asset('assets/libs/toastr/toastr.min.js') }}" defer></script>
    <script src="{{asset('assets/js/chat.js')}}" defer></script>

    <script src="{{asset('assets/js/theme-presets.js')}}"></script>
</body>

</html>