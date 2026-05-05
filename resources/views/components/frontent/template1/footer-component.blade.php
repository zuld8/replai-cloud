<footer class="footer section bg-primary  pb-0 text-white overflow-hidden"> 
    <div class="container">
        @if($setting->footer_web == 'yes')
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <a class="mr-lg-5 d-flex" href="{{route('web.home')}}">
                    <img src="{{asset($setting->white_logo)}}" height="65" class="mr-3" alt="{{$setting->app_name}}">
                </a>
                <p class="my-4">{{$setting->footer_description}}</p>
            </div>
            <div class="col-6 col-sm-3 col-lg-2 mb-4 mb-lg-0">
                <h6>{{$setting->footer_1}}</h6>
                <ul class="links-vertical">
                    @foreach ($links as $link)
                    <li><a target="_blank" href="{{$link->url}}">{{$link->name}}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-6 col-sm-3 col-lg-2 mb-4 mb-lg-0">
                <h6>{{$setting->footer_2}}</h6>
                <ul class="links-vertical">
                    @foreach ($links2 as $link)
                    <li><a target="_blank" href="{{$link->url}}">{{$link->name}}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <h6>{{$setting->footer_3}}</h6>
                <ul class="links-vertical">
                    @foreach ($links3 as $link)
                    <li><a target="_blank" href="{{$link->url}}">{{$link->name}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        <hr style="border: 1px solid white;">
        <div class="row">
            <div class="col pb-4 mb-md-0">
                <div class="d-flex text-center justify-content-center align-items-center">
                    <p class="font-weight-normal mb-0">{{$setting->copyright}}</p>
                </div>
            </div>
        </div>
    </div>
</footer>