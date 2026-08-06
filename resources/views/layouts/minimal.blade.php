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

    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '4513300828959177');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=4513300828959177&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
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