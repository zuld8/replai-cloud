 <!-- app-header -->
 <header class="app-header">

     <!-- Start::main-header-container -->
     <div class="main-header-container container-fluid">

         <!-- Start::header-content-left -->
         <div class="header-content-left">

             <!-- Start::header-element -->
             <div class="header-element">
                 <div class="horizontal-logo">
                     <a href="{{route('starter.packages')}}" class="header-logo"
                        style="display:flex;align-items:center;gap:10px;text-decoration:none">
                         <img src="{{asset($internalSetting->icon)}}" alt="Replai"
                              style="height:42px;width:42px;border-radius:50%;object-fit:cover;flex-shrink:0;display:block !important">
                         <span style="font-size:1.1rem;font-weight:700;color:#fff;line-height:1.2;display:block !important">
                           Replai<span style="color:#93C5FD">.id</span>
                           <span style="display:block;font-size:0.62rem;font-weight:500;color:rgba(255,255,255,.65);letter-spacing:.04em">AI CRM Platform</span>
                         </span>
                     </a>
                 </div>
             </div>
             <!-- End::header-element -->

             <!-- Start::header-element -->
             <div class="header-element">
                 <!-- Start::header-link -->
                 <a aria-label="anchor" href="javascript:void(0);" class="sidemenu-toggle header-link" data-bs-toggle="sidebar">
                     <span class="open-toggle me-2">
                         <i class="fe fe-align-left header-link-icon border-0"></i>
                     </span>
                 </a>
                 <!-- End::header-link -->
             </div>
             <!-- End::header-element -->



         </div>
         <!-- End::header-content-left -->

         <!-- Start::header-content-right -->
         <div class="header-content-right">


             <div class="header-element country-selector">
                 <a aria-label="anchor" href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                     <i class="bx bx-world header-link-icon" style="font-size:1.25rem"></i>
                 </a>
                 <ul class="main-header-dropdown dropdown-menu border-0">
                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','id') : route('setlang','id') }}">
                             <span class="avatar avatar-xs lh-1 me-2">
                                 <img src="{{asset('assets/img/flags/indonesia.png')}}" alt="img">
                             </span> {{__('sidebar.indonesia')}} @if(current_lang() == 'id') <i class="bx bx-check-circle ms-2 text-success"></i> @endif
                         </a>
                     </li>
                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','en') : route('setlang','en') }}">
                             <span class="avatar avatar-xs lh-1 me-2">
                                 <img src="{{asset('assets/img/flags/english.png')}}" alt="img">
                             </span> {{__('sidebar.english')}} @if(current_lang() == 'en') <i class="bx bx-check-circle ms-2 text-success"></i> @endif
                         </a>
                     </li>
                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','hi') : route('setlang','hi') }}">
                             <span class="avatar avatar-xs lh-1 me-2">
                                 <img src="{{asset('assets/img/flags/india.png')}}" alt="img">
                             </span> {{__('sidebar.india')}} @if(current_lang() == 'hi') <i class="bx bx-check-circle ms-2 text-success"></i> @endif
                         </a>
                     </li>
                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','pt') : route('setlang','pt') }}">
                             <span class="avatar avatar-xs lh-1 me-2">
                                 <img src="{{asset('assets/img/flags/portugal.png')}}" alt="img">
                             </span> {{__('sidebar.portugal')}} @if(current_lang() == 'pt') <i class="bx bx-check-circle ms-2 text-success"></i> @endif
                         </a>
                     </li>
                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','es') : route('setlang','es') }}">
                             <span class="avatar avatar-xs lh-1 me-2">
                                 <img src="{{asset('assets/img/flags/spanyol.png')}}" alt="img">
                             </span> {{__('sidebar.spanish')}} @if(current_lang() == 'es') <i class="bx bx-check-circle ms-2 text-success"></i> @endif
                         </a>
                     </li>
                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','de') : route('setlang','de') }}">
                             <span class="avatar avatar-xs lh-1 me-2">
                                 <img src="{{asset('assets/img/flags/de.svg')}}" alt="img">
                             </span> {{__('sidebar.german')}} @if(current_lang() == 'de') <i class="bx bx-check-circle ms-2 text-success"></i> @endif
                         </a>
                     </li>
                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','ar') : route('setlang','ar') }}">
                             <span class="avatar avatar-xs lh-1 me-2">
                                 <img src="{{asset('assets/img/flags/arab.png')}}" alt="img">
                             </span> {{__('sidebar.arab')}} @if(current_lang() == 'ar') <i class="bx bx-check-circle ms-2 text-success"></i> @endif
                         </a>
                     </li>
                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','ja') : route('setlang','ja') }}">
                             <span class="avatar avatar-xs lh-1 me-2">
                                 <img src="{{asset('assets/img/flags/jp.svg')}}" alt="img">
                             </span> {{__('sidebar.japan')}} @if(current_lang() == 'ja') <i class="bx bx-check-circle ms-2 text-success"></i> @endif
                         </a>
                     </li>
                     <li>
                         <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','nl') : route('setlang','nl') }}">
                             <span class="avatar avatar-xs lh-1 me-2">
                                 <img src="{{asset('assets/img/flags/nl.svg')}}" alt="img">
                             </span> {{__('sidebar.dutch')}} @if(current_lang() == 'nl') <i class="bx bx-check-circle ms-2 text-success"></i> @endif
                         </a>
                     </li>
                 </ul>
             </div>

             <!-- Start::header-element -->
             <div class="header-element header-theme-mode">
                 <a aria-label="anchor" href="javascript:void(0);" class="header-link layout-setting">
                     <i class="bx bxs-sun header-link-icon dark-layout" style="font-size:1.25rem"></i>
                     <i class="bx bxs-moon header-link-icon light-layout" style="font-size:1.25rem"></i>
                 </a>
             </div>
             <!-- End::header-element -->



             <!-- Start::header-element -->
             <div class="header-element header-fullscreen">
                 <!-- Start::header-link -->
                 <a aria-label="anchor" onclick="openFullscreen();" href="javascript:void(0);" class="header-link">
                     <i class="bx bx-expand-alt header-link-icon full-screen-open" style="font-size:1.25rem"></i>
                     <i class="bx bx-collapse-alt header-link-icon full-screen-close d-none" style="font-size:1.25rem"></i>
                 </a>
                 <!-- End::header-link -->
             </div>
             <!-- End::header-element -->

             <!-- Start::header-element -->
             <div class="header-element mainuserProfile">
                 <!-- Start::header-link|dropdown-toggle -->
                 <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                     <div class="d-flex align-items-center">
                         <div class="d-sm-flex wd-100p lh-0">
                             <div class="avatar avatar-md"><img alt="avatar" class="rounded-circle" src="{{asset(auth()->user()->image_data)}}"></div>
                             <div class="ms-2 my-auto d-none d-xl-flex">
                                 <h6 class=" font-weight-semibold mb-0 fs-13 user-name d-sm-block d-none">{{auth()->user()->name}}</h6>
                             </div>
                         </div>
                     </div>
                 </a>
                 <!-- End::header-link|dropdown-toggle -->
                 <div class="main-header-dropdown dropdown-menu pt-0 border-0 header-profile-dropdown dropdown-menu-end dropdown-menu-arrow" aria-labelledby="mainHeaderProfile">
                     <div class="p-3 menu-header-content text-fixed-white rounded-top text-center">
                         <div class="">
                             <div class="avatar avatar-xl rounded-circle"><img alt="" class="rounded-circle" src="{{asset(auth()->user()->image_data)}}"></div>
                             <p class="text-fixed-white fs-18 fw-semibold mb-0">{{auth()->user()->name}}</p>
                             <span class="fs-13 text-fixed-white">{{auth()->user()->phone}}</span>
                         </div>
                     </div>
                     <div>
                         <hr class="dropdown-divider">
                     </div>
                     <div>
                         <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                             <i class="bx bx-arrow-to-right me-1"></i> Logout
                         </a>
                         <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                             @csrf
                         </form>
                     </div>
                 </div>
             </div>
             <!-- End::header-element -->



         </div>
         <!-- End::header-content-right -->

     </div>
     <!-- End::main-header-container -->

 </header>
 <!-- /app-header -->