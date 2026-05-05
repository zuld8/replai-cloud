<header class="header-global">
    <nav
        id="navbar-main"
        class="navbar navbar-main navbar-expand-lg headroom py-lg-3 px-lg-6 navbar-dark navbar-theme-primary headroom--not-bottom headroom--pinned headroom--top">
        <div class="container">
            <a class="navbar-brand @@logo_classes" href="{{route('web.home')}}">
                <img class="navbar-brand-dark common" src="{{asset($settings->white_logo)}}" height="55" alt="Logo light" />
                <img class="navbar-brand-light common" src="{{asset($settings->logo)}}" height="55" alt="Logo dark" />
            </a>
            <div class="navbar-collapse collapse" id="navbar_global">
                <div class="navbar-collapse-header">
                    <div class="row">
                        <div class="col-6 collapse-brand">
                            <a href="{{route('web.home')}}">
                                <img src="{{asset($settings->whiite_logo)}}" height="35" alt="Logo Impact" />
                            </a>
                        </div>
                        <div class="col-6 collapse-close">
                            <a
                                href="#navbar_global"
                                role="button"
                                class="fas fa-times"
                                data-toggle="collapse"
                                data-target="#navbar_global"
                                aria-controls="navbar_global"
                                aria-expanded="false"
                                aria-label="Toggle navigation"></a>
                        </div>
                    </div>
                </div>
                <ul class="navbar-nav navbar-nav-hover justify-content-center">
                    <li class="nav-item">
                        <a href="{{route('web.home')}}" class="nav-link">{{__('website.home')}}</a>
                    </li>
                    @if($settings->pricing == 'yes')
                    <li class="nav-item">
                        <a href="{{route('web.pricing')}}" class="nav-link">{{__('website.pricing')}}</a>
                    </li>
                    @endif

                    @if($settings->blog == 'yes')
                    <li class="nav-item">
                        <a href="{{route('web.blogs')}}" class="nav-link">{{__('website.blog')}}</a>
                    </li>
                    @endif

                    @if($settings->contact == 'yes')
                    <li class="nav-item">
                        <a href="{{route('web.contact')}}" class="nav-link">{{__('website.contact')}}</a>
                    </li>
                    @endif

                    @foreach ($links as $link)
                    <li class="nav-item">
                        <a href="{{$link->url}}" class="nav-link">{{$link->name}}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="d-none d-lg-block @@cta_button_classes">
                @if(auth()->check())
                <a href="{{route('home')}}" class="btn btn-md btn-secondary animate-up-2"><i class="fa fa-user-circle mr-2"></i> {{__('website.dashboard')}} </a>
                @else
                <a href="{{route('login')}}" class="btn btn-md btn-docs btn-outline-white animate-up-2 mr-3"><i class="fa fa-user-circle mr-2"></i> {{__('website.signin')}}</a>
                @if($settings->register == 'yes')
                <a href="{{route('register')}}" class="btn btn-md btn-secondary animate-up-2"><i class="fa fa-id-card mr-2"></i> {{__('website.register')}} </a>
                @endif
                @endif
            </div>
            <div class="d-flex d-lg-none align-items-center">
                <button
                    class="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#navbar_global"
                    aria-controls="navbar_global"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
    </nav>
</header>