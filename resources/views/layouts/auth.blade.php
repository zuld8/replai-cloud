<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{config('app.name')}} - {{$page}} </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-meta-component></x-meta-component>
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{asset('assets/css/icons.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('assets/libs/toastr/toastr.min.css')}}">
    <link href="{{asset('assets/css/pages/auth.css')}}?v={{filemtime(public_path('assets/css/pages/auth.css'))}}" rel="stylesheet" type="text/css" id="app-style" />

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
    <div class="logo-container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="{{route('login')}}" class="logo">
                <img src="{{asset($internalSetting->logo)}}" style="max-height:56px;width:auto;max-width:220px" />
            </a>
            
            <!-- Language Selector -->
            <div class="language-selector">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fe fe-globe"></i>
                        @if(current_lang() == 'id')
                            <img src="{{asset('assets/img/flags/indonesia.png')}}" alt="ID" style="width: 20px; height: 15px; margin-left: 5px;">
                        @elseif(current_lang() == 'en')
                            <img src="{{asset('assets/img/flags/english.png')}}" alt="EN" style="width: 20px; height: 15px; margin-left: 5px;">
                        @elseif(current_lang() == 'hi')
                            <img src="{{asset('assets/img/flags/india.png')}}" alt="HI" style="width: 20px; height: 15px; margin-left: 5px;">
                        @elseif(current_lang() == 'pt')
                            <img src="{{asset('assets/img/flags/portugal.png')}}" alt="PT" style="width: 20px; height: 15px; margin-left: 5px;">
                        @elseif(current_lang() == 'es')
                            <img src="{{asset('assets/img/flags/spanyol.png')}}" alt="ES" style="width: 20px; height: 15px; margin-left: 5px;">
                        @elseif(current_lang() == 'de')
                            <img src="{{asset('assets/img/flags/de.svg')}}" alt="DE" style="width: 20px; height: 15px; margin-left: 5px;">
                        @elseif(current_lang() == 'ar')
                            <img src="{{asset('assets/img/flags/arab.png')}}" alt="AR" style="width: 20px; height: 15px; margin-left: 5px;">
                        @elseif(current_lang() == 'ja')
                            <img src="{{asset('assets/img/flags/jp.svg')}}" alt="JA" style="width: 20px; height: 15px; margin-left: 5px;">
                        @elseif(current_lang() == 'nl')
                            <img src="{{asset('assets/img/flags/nl.svg')}}" alt="NL" style="width: 20px; height: 15px; margin-left: 5px;">
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('setlang','id') }}">
                                <img src="{{asset('assets/img/flags/indonesia.png')}}" alt="ID" style="width: 20px; height: 15px; margin-right: 10px;">
                                {{__('sidebar.indonesia')}}
                                @if(current_lang() == 'id') <i class="bx bx-check-circle ms-auto text-success"></i> @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('setlang','en') }}">
                                <img src="{{asset('assets/img/flags/english.png')}}" alt="EN" style="width: 20px; height: 15px; margin-right: 10px;">
                                {{__('sidebar.english')}}
                                @if(current_lang() == 'en') <i class="bx bx-check-circle ms-auto text-success"></i> @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('setlang','hi') }}">
                                <img src="{{asset('assets/img/flags/india.png')}}" alt="HI" style="width: 20px; height: 15px; margin-right: 10px;">
                                {{__('sidebar.india')}}
                                @if(current_lang() == 'hi') <i class="bx bx-check-circle ms-auto text-success"></i> @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('setlang','pt') }}">
                                <img src="{{asset('assets/img/flags/portugal.png')}}" alt="PT" style="width: 20px; height: 15px; margin-right: 10px;">
                                {{__('sidebar.portugal')}}
                                @if(current_lang() == 'pt') <i class="bx bx-check-circle ms-auto text-success"></i> @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('setlang','es') }}">
                                <img src="{{asset('assets/img/flags/spanyol.png')}}" alt="ES" style="width: 20px; height: 15px; margin-right: 10px;">
                                {{__('sidebar.spanish')}}
                                @if(current_lang() == 'es') <i class="bx bx-check-circle ms-auto text-success"></i> @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('setlang','de') }}">
                                <img src="{{asset('assets/img/flags/de.svg')}}" alt="DE" style="width: 20px; height: 15px; margin-right: 10px;">
                                {{__('sidebar.german')}}
                                @if(current_lang() == 'de') <i class="bx bx-check-circle ms-auto text-success"></i> @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('setlang','ar') }}">
                                <img src="{{asset('assets/img/flags/arab.png')}}" alt="AR" style="width: 20px; height: 15px; margin-right: 10px;">
                                {{__('sidebar.arab')}}
                                @if(current_lang() == 'ar') <i class="bx bx-check-circle ms-auto text-success"></i> @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('setlang','ja') }}">
                                <img src="{{asset('assets/img/flags/jp.svg')}}" alt="JA" style="width: 20px; height: 15px; margin-right: 10px;">
                                {{__('sidebar.japan')}}
                                @if(current_lang() == 'ja') <i class="bx bx-check-circle ms-auto text-success"></i> @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('setlang','nl') }}">
                                <img src="{{asset('assets/img/flags/nl.svg')}}" alt="NL" style="width: 20px; height: 15px; margin-right: 10px;">
                                {{__('sidebar.dutch')}}
                                @if(current_lang() == 'nl') <i class="bx bx-check-circle ms-auto text-success"></i> @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @yield('content')

    <script src="{{asset('assets/libs/jquery/jquery.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('assets/libs/sweetalert/sweetalert2.all.min.js')}}" defer></script>
    <script src="{{ asset('assets/libs/toastr/toastr.min.js') }}" defer></script>
    @yield('scripts')
</body>

</html>