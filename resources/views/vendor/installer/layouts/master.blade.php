<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title> {{env('APP_NAME')}} - @yield('template_title') </title> 
        <link rel="icon" href="<?= asset('assets/img/icon.png'); ?>" type="image/x-icon">
        <link href="{{asset('assets/css/icons.css')}}" rel="stylesheet">
        <link href="{{ asset('assets/css/installer.min.css') }}" rel="stylesheet"/>

        @yield('style')
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>
    </head>
    <body>
        <div class="master">
            <div class="box">
                <div class="header">
                    <h1 class="header__title">@yield('title')</h1>
                </div>
                <ul class="step">
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('MdhLicense::final') }}">
                        <i class="step__icon bx bx-server" aria-hidden="true"></i>
                    </li>
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('MdhLicense::environment')}} {{ isActive('MdhLicense::environmentWizard')}} ">
                        @if(Request::is('install/environment') || Request::is('install/environment/wizard')  )
                            <a href="{{ route('MdhLicense::environment') }}">
                                <i class="step__icon bx bx-cog" aria-hidden="true"></i>
                            </a>
                        @else
                            <i class="step__icon bx bx-cog" aria-hidden="true"></i>
                        @endif
                    </li>
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('MdhLicense::permissions') }}">
                        @if(Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard')  )
                            <a href="{{ route('MdhLicense::permissions') }}">
                                <i class="step__icon bx bx-key" aria-hidden="true"></i>
                            </a>
                        @else
                            <i class="step__icon bx bx-key" aria-hidden="true"></i>
                        @endif
                    </li>
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('MdhLicense::requirements') }}">
                        @if(Request::is('install/requirements') || Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard')  )
                            <a href="{{ route('MdhLicense::requirements') }}">
                                <i class="step__icon bx bx-list-check" aria-hidden="true"></i>
                            </a>
                        @else
                            <i class="step__icon bx bx-list-check" aria-hidden="true"></i>
                        @endif
                    </li>
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('MdhLicense::license') }}">
                        @if(Request::is('install') || Request::is('install/requirements') || Request::is('install/license') || Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard') )
                            <a href="{{ route('MdhLicense::license') }}">
                                <i class="step__icon bx bx-lock" aria-hidden="true"></i>
                            </a>
                        @else
                            <i class="step__icon bx bx-lock" aria-hidden="true"></i>
                        @endif
                    </li>
                    <li class="step__divider"></li>
                    <li class="step__item {{ isActive('MdhLicense::welcome') }}">
                        @if(Request::is('install') || Request::is('install/requirements') || Request::is('install/license') || Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard')  )
                            <a href="{{ route('MdhLicense::welcome') }}">
                                <i class="step__icon bx bx-home" aria-hidden="true"></i>
                            </a>
                        @else
                            <i class="step__icon bx bx-home" aria-hidden="true"></i>
                        @endif
                    </li>
                    <li class="step__divider"></li>
                    
                </ul>
                <div class="main">
                    @if (session('message'))
                        <p class="alert text-center">
                            <strong>
                                @if(is_array(session('message')))
                                    {{ session('message')['message'] }}
                                @else
                                    {{ session('message') }}
                                @endif
                            </strong>
                        </p>
                    @endif
                    @if(session()->has('errors'))
                        <div class="alert alert-danger" id="error_alert">
                            <button type="button" class="close" id="close_alert" data-dismiss="alert" aria-hidden="true">
                                 <i class="bx bx-close" aria-hidden="true"></i>
                            </button>
                            <h4>
                                <i class="bx bx-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                {{ trans('installer_messages.forms.errorTitle') }}
                            </h4>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('container')
                </div>
            </div>
        </div>
        @yield('scripts')
        <script type="text/javascript">
            var x = document.getElementById('error_alert');
            var y = document.getElementById('close_alert');
            y.onclick = function() {
                x.style.display = "none";
            };
        </script>
    </body>
</html>
