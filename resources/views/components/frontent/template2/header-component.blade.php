<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="{{route('web.home')}}" class="logo">
            <img src="{{asset($settings->white_logo)}}" alt="{{$settings->app_name}}" class="logo-img">
        </a>
        <ul class="nav-menu" id="navMenu">
            <li><a href="{{route('web.home')}}">Home</a></li>

            @if($settings->pricing == 'yes')
            <li><a href="{{route('web.pricing')}}">{{__('website.pricing')}}</a></li>
            @endif

            @if($settings->blog == 'yes')
            <li><a href="{{route('web.blogs')}}">{{__('website.blog')}}</a></li>
            @endif

            @if($settings->contact == 'yes')
            <li><a href="{{route('web.contact')}}">{{__('website.contact')}}</a></li>
            @endif

            @foreach ($links as $link)
            <a href="{{$link->url}}">{{$link->name}}</a>
            @endforeach

            @if(!auth()->check())
            <li><a href="{{route('login')}}" class="btn-primary">{{__('auth.login')}}</a></li>
            @endif

            @if(auth()->check())
            <li><a href="{{route('index')}}" class="btn-primary">{{__('sidebar.dashboard')}}</a></li>
            @endif
            
        </ul>
        <div class="mobile-toggle" id="mobileToggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav> 