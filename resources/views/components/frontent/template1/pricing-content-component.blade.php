<div class="row text-gray">
    @foreach ($pricing as $package)
    <div class="col-12 col-lg-4">
        <div class="card shadow-soft px-2">
            <div class="card-header border-light pt-5 pb-2 px-4">
                <div class="d-flex justify-content-center mb-3">
                    <span class="h5 mb-0"></span>
                    <span
                        class="text-info display-4 mb-0 mr-1"
                        data-annual="0"
                        data-monthly="0">{{number_format($package->price)}}</span>
                    <span class="h6 font-weight-normal align-self-end"> / {{number_format($package->add_days)}} Hari </span>
                </div>
                <h4 class="text-black">{{$package->name}}</h4>
            </div>
            <div class="card-body pt-0">
                <ul class="list-group simple-list">
                    <li class="list-group-item font-weight-normal text-left">
                        @if($package->limit_user_option == 'yes' && $package->users_limit == 0)
                        <span class="text-danger">
                            <i class="fa fa-times-circle"></i>
                        </span>
                        @else
                        <span class="text-success">
                            <i class="fa fa-check-circle"></i>
                        </span>
                        @endif

                        Pengguna
                        <span class="font-weight-bolder"> ( {{$package->limit_user_option == 'yes' ? number_format($package->users_limit) : 'Unlimited' }} ) </span>
                    </li>
                    <li class="list-group-item font-weight-normal text-left">
                        @if($package->limit_device == 'yes' && $package->device_limit == 0)
                        <span class="text-danger">
                            <i class="fa fa-times-circle"></i>
                        </span>
                        @else
                        <span class="text-success">
                            <i class="fa fa-check-circle"></i>
                        </span>
                        @endif

                        Whatsapp Device
                        <span class="font-weight-bolder"> ( {{$package->limit_device == 'yes' ? number_format($package->device_limit) : 'Unlimited' }} ) </span>
                    </li>
                    <li class="list-group-item font-weight-normal text-left">
                        @if($package->limit_whatsapp_option == 'yes' && $package->whatsapp_limit == 0)
                        <span class="text-danger">
                            <i class="fa fa-times-circle"></i>
                        </span>
                        @else
                        <span class="text-success">
                            <i class="fa fa-check-circle"></i>
                        </span>
                        @endif
                        Whatsapp Sender
                        <span class="font-weight-bolder"> ( {{$package->limit_whatsapp_option == 'yes' ? number_format($package->whatsapp_limit) : 'Unlimited' }} {{$package->whatsapp_priode != '' ? '/' : ''}} {{$package->whatsapp_priode}} ) </span>
                    </li>
                    <li class="list-group-item font-weight-normal text-left">
                        @if($package->limit_email_option == 'yes' && $package->email_limit == 0)
                        <span class="text-danger">
                            <i class="fa fa-times-circle"></i>
                        </span>
                        @else
                        <span class="text-success">
                            <i class="fa fa-check-circle"></i>
                        </span>
                        @endif
                        Whatsapp Sender
                        <span class="font-weight-bolder"> ( {{$package->limit_email_option == 'yes' ? number_format($package->email_limit) : 'Unlimited' }} {{$package->email_priode != '' ? '/' : ''}} {{$package->email_priode}} ) </span>
                    </li>
                    <li class="list-group-item font-weight-normal text-left">
                        @if($package->limit_scrapp_option == 'yes' && $package->scrapp_limit == 0)
                        <span class="text-danger">
                            <i class="fa fa-times-circle"></i>
                        </span>
                        @else
                        <span class="text-success">
                            <i class="fa fa-check-circle"></i>
                        </span>
                        @endif
                        Scrapping G-Maps
                        <span class="font-weight-bolder"> ( {{$package->limit_scrapp_option == 'yes' ? number_format($package->scrapp_limit) : 'Unlimited' }} {{$package->scrapping_priode != '' ? '/' : ''}} {{$package->scrapping_priode}} ) </span>
                    </li>
                    <li class="list-group-item font-weight-normal text-left">
                        @if($package->limit_template == 'yes' && $package->template_limit == 0)
                        <span class="text-danger">
                            <i class="fa fa-times-circle"></i>
                        </span>
                        @else
                        <span class="text-success">
                            <i class="fa fa-check-circle"></i>
                        </span>
                        @endif

                        Template Pesan
                        <span class="font-weight-bolder"> ( {{$package->limit_template == 'yes' ? number_format($package->template_limit) : 'Unlimited' }} ) </span>
                    </li>
                    <li class="list-group-item font-weight-normal text-left">
                        @if($package->limit_ai_training == 'yes' && $package->ai_training_limit == 0)
                        <span class="text-danger">
                            <i class="fa fa-times-circle"></i>
                        </span>
                        @else
                        <span class="text-success">
                            <i class="fa fa-check-circle"></i>
                        </span>
                        @endif

                        Ai Training
                        <span class="font-weight-bolder"> ( {{$package->limit_ai_training == 'yes' ? number_format($package->ai_training_limit) : 'Unlimited' }} ) </span>
                    </li>
                    <li class="list-group-item font-weight-normal text-left">
                        @if($package->limit_chatbot == 'yes' && $package->chatbot_limit == 0)
                        <span class="text-danger">
                            <i class="fa fa-times-circle"></i>
                        </span>
                        @else
                        <span class="text-success">
                            <i class="fa fa-check-circle"></i>
                        </span>
                        @endif

                        ChatBot Auto Reply
                        <span class="font-weight-bolder"> ( {{$package->limit_chatbot == 'yes' ? number_format($package->chatbot_limit) : 'Unlimited' }} ) </span>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>
    @endforeach
</div>