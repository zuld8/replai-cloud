<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{env('APP_NAME')}} - @yield('template_title') </title>
    <link rel="icon" href="<?= asset('assets/img/icon-logo.png'); ?>" type="image/x-icon">
    <link href="{{asset('assets/css/icons.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/installer.min.css') }}" rel="stylesheet" />
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
                        <i class="fa fa-close" aria-hidden="true"></i>
                    </button>
                    <h4>
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
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

</body>

</html>