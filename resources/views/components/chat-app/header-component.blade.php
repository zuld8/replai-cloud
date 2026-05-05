<div class="header row desktop-header">
    <div class="col-sm-12 col-lg-4 p-3">
        <div class="input-group">
            <span class="btn btn-outline-secondary mb-0" type="button"><i class="bx bx-search align-middle"></i></span>
           
            <input type="text" class="form-control"  style="max-width: 63%;" placeholder="{{__('master.device.search_chat')}}...." id="search-chat">
        </div>
    </div>
    <div class="col-sm-12 col-lg-8 d-flex justify-content-between chat-menu">
        <div class="text-center">
            <img src="{{asset($setting->white_logo)}}" class="navbar-brand-img whitelogo h-100 w-25" alt="main_logo">
            <img src="{{asset($setting->logo)}}" class="navbar-brand-img darklogo h-100 w-25" alt="main_logo">
        </div>
        <div class="d-flex justify-content-center align-item-center">
            <div class="dark-light me-3">
                <i class="bx bx-sun  icondarkmode cursor-pointer"></i>
            </div>
            <a href="{{route('device')}}">
                <i class="bx bx-arrow-to-right  cursor-pointer"></i>
            </a>
        </div>
    </div>
</div>

<div class="header row m-0 p-0 mobile-header">
    <div class="col-12 d-flex justify-content-between">
        <div>
            <img src="{{asset($setting->white_logo)}}" class="navbar-brand-img whitelogo h-100 w-50" alt="main_logo">
            <img src="{{asset($setting->logo)}}" class="navbar-brand-img darklogo h-100 w-50" alt="main_logo">
        </div>
        <div class="d-flex justify-content-center chat-menu">
            <div class="contectmenu me-3">
                <i class="bx bx-list-ul cursor-pointer"></i>
            </div>
            <div class="dark-light-mobile me-3">
                <i class="bx bx-sun icondarkmode cursor-pointer"></i>
            </div>
            <a href="{{route('device')}}">
                <i class="bx bx-arrow-to-right  cursor-pointer"></i>
            </a>
        </div>
    </div>
</div>

<div class="header row search-mobile m-0 p-0 d-none">
    <div class="col-12 d-flex justify-content-between">
        <div class="p-2">
            <input type="text" class="form-control" placeholder="{{__('master.device.search_chat')}}...." id="search-chat">
        </div>
        <div class="d-flex justify-content-end chat-menu">
            <div class="close-contact">
                <i class="bx bx-x-circle cursor-pointer"></i>
            </div> 
        </div>
    </div>
</div>