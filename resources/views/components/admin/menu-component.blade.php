<!-- Admin Sidebar -->
<aside class="app-sidebar sticky" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="{{route('admin.index')}}" class="header-logo">
            <img src="{{asset($internalSetting->logo)}}" alt="logo" class="desktop-logo">
            <img src="{{asset($internalSetting->icon)}}" alt="logo" class="toggle-logo">
            <img src="{{asset($internalSetting->white_logo)}}" alt="logo" class="desktop-dark">
            <img src="{{asset($internalSetting->icon)}}" alt="logo" class="toggle-dark">
        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll">

        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">

            <!-- Admin Profile Section -->
            <div class="main-sidebar-loggedin">
                <div class="app-sidebar__user">
                    <div class="dropdown user-pro-body text-center">
                        <div class="user-pic mb-2">
                            <img src="{{asset(auth()->user()->image_data)}}" alt="user-img" class="rounded-circle mCS_img_loaded">
                        </div>
                        <div class="user-info">
                            <h6 class="mb-0 text-truncate">{{auth()->user()->name}}</h6>
                            <span class="fs-13 text-muted text-truncate d-block">{{auth()->user()->email}}</span>
                            <span class="badge bg-danger-transparent mt-1">
                                <i class="bx bx-shield me-1"></i>{{ __('sidebar.administrator') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="sidebar-navs mx-auto my-3">
                <a href="{{route('admin.settings')}}" class="btn btn-icon btn-outline-light rounded-pill btn-wave m-1" title="{{ __('sidebar.admin_settings') }}">
                    <i class="fe fe-settings"></i>
                </a>
                <a href="{{ route('admin.profile') }}" class="btn btn-icon btn-outline-light rounded-pill btn-wave m-1" title="{{ __('sidebar.admin_profile') }}">
                    <i class="fe fe-user"></i>
                </a>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-sidebar').submit();"
                    class="btn btn-icon btn-outline-danger rounded-pill btn-wave m-1" title="{{ __('sidebar.logout') }}">
                    <i class="fe fe-power"></i>
                </a>
                <form id="logout-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

            <!-- Sidebar Toggle -->
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>

            <ul class="main-menu">

                <!-- DASHBOARD SECTION -->
                <li class="slide__category"><span class="category-name">{{ __('sidebar.main_panel') }}</span></li>

                <li class="slide {{ request()->is('administrator') ? 'active' : '' }}">
                    <a href="{{route('admin.index')}}" class="side-menu__item {{ request()->is('administrator') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='bx bx-tachometer'></i>
                        </span>
                        <span class="side-menu__label">{{ __('sidebar.admin_dashboard') }}</span>
                    </a>
                </li>

                <!-- MANAJEMEN PENGGUNA SECTION -->
                <li class="slide__category"><span class="category-name">{{ __('sidebar.user_management') }}</span></li>

                <!-- User & Business Management -->
                <li class="slide has-sub {{ request()->is('administrator/merchants*') || request()->is('administrator/business*') ? 'open active' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item {{ request()->is('administrator/merchants*') || request()->is('administrator/business*') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='bx bx-group'></i>
                        </span>
                        <span class="side-menu__label">{{ __('sidebar.manage_users') }}</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">{{ __('sidebar.manage_users') }}</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('merchants') }}" class="side-menu__item {{ request()->is('administrator/merchants') || request()->is('administrator/merchants/detail*') ? 'active' : '' }}">
                                <i class='bx bx-user me-2'></i>{{ __('sidebar.user_list') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('merchant.categories') }}" class="side-menu__item {{ request()->is('administrator/merchants/categories*') ? 'active' : '' }}">
                                <i class='bx bx-category me-2'></i>{{ __('sidebar.user_categories') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('business') }}" class="side-menu__item {{ request()->is('administrator/business*') ? 'active' : '' }}">
                                <i class='bx bx-buildings me-2'></i>{{ __('sidebar.business_list') }}
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- SISTEM SAAS SECTION -->
                <li class="slide__category"><span class="category-name">{{ __('sidebar.saas_system') }}</span></li>

                <!-- Master SAAS Data -->
                <li class="slide has-sub {{ request()->is('administrator/banks*') || request()->is('administrator/packages*') || request()->is('administrator/couriers*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item {{ request()->is('administrator/banks*') || request()->is('administrator/packages*') || request()->is('administrator/couriers*') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='bx bx-data'></i>
                        </span>
                        <span class="side-menu__label">{{ __('sidebar.saas_master_data') }}</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">{{ __('sidebar.saas_master_data') }}</a>
                        </li>
                        <li class="slide">
                            <a href="{{route('packages')}}" class="side-menu__item {{ request()->is('administrator/packages*') ? 'active' : '' }}">
                                <i class='bx bx-package me-2'></i>{{ __('sidebar.subscription_packages') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{route('package.storage')}}" class="side-menu__item {{ request()->is('administrator/package-storage*') ? 'active' : '' }}">
                                <i class='bx bx-package me-2'></i>{{ __('sidebar.storage_packages') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{route('banks')}}" class="side-menu__item {{ request()->is('administrator/banks*') ? 'active' : '' }}">
                                <i class='bx bx-credit-card me-2'></i>{{ __('sidebar.bank_master') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{route('couriers')}}" class="side-menu__item {{ request()->is('administrator/couriers*') ? 'active' : '' }}">
                                <i class='bx bx-car me-2'></i>{{ __('sidebar.courier_master') }}
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Transaction Management -->
                <li class="slide has-sub {{ request()->is('administrator/transactions*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item {{ request()->is('administrator/transactions*') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='bx bx-money'></i>
                        </span>
                        <span class="side-menu__label">{{ __('sidebar.manage_transactions') }}</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">{{ __('sidebar.manage_transactions') }}</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('transactions') }}" class="side-menu__item {{ request()->is('administrator/transactions') || request()->is('administrator/transactions/detail*') ? 'active' : '' }}">
                                <i class='bx bx-credit-card me-2'></i>{{ __('sidebar.subscription_transactions') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('transaction.topup') }}" class="side-menu__item {{ request()->is('administrator/transactions/topup*') ? 'active' : '' }}">
                                <i class='bx bx-wallet me-2'></i>{{ __('sidebar.topup_ai_credit_trans') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('transaction.storage') }}" class="side-menu__item {{ request()->is('administrator/transactions/storage') || request()->is('administrator/transactions/storage/detail*') ? 'active' : '' }}">
                                <i class='bx bx-credit-card me-2'></i>{{ __('sidebar.topup_storage') }}
                            </a>
                        </li>
                         <li class="slide">
                            <a href="{{ route('transaction.mua') }}" class="side-menu__item {{ request()->is('administrator/transactions/mua') || request()->is('administrator/transactions/mua/detail*') ? 'active' : '' }}">
                                <i class='bx bx-user-check me-2'></i>MUA Addons
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Affiliate Management -->
                <li class="slide has-sub {{ request()->is('administrator/affiliate*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item {{ request()->is('administrator/affiliate*') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='bx bx-dollar-circle'></i>
                        </span>
                        <span class="side-menu__label">💰 Affiliate</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Affiliate</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('admin.affiliate') }}" class="side-menu__item {{ request()->is('administrator/affiliate') ? 'active' : '' }}">
                                <i class='bx bx-group me-2'></i>Affiliator List
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('admin.affiliate.withdrawals') }}" class="side-menu__item {{ request()->is('administrator/affiliate/withdrawals*') ? 'active' : '' }}">
                                <i class='bx bx-wallet me-2'></i>Withdrawal Requests
                                @php
                                    $pendingWd = \App\Models\Affiliate\AffiliateWithdrawal::where('status','pending')->count();
                                @endphp
                                @if($pendingWd > 0)
                                <span class="badge bg-danger rounded-pill ms-1" style="font-size:0.65rem;">{{ $pendingWd }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- WEBSITE MANAGEMENT SECTION -->
                <li class="slide__category"><span class="category-name">{{ __('sidebar.manage_website') }}</span></li>

                <!-- Content Management -->
                <li class="slide has-sub {{ request()->is('administrator/web-app/pages*') || request()->is('administrator/web-app/links*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item {{ request()->is('administrator/web-app/pages*') || request()->is('administrator/web-app/links*') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='bx bx-globe'></i>
                        </span>
                        <span class="side-menu__label">{{ __('sidebar.website_content') }}</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">{{ __('sidebar.website_content') }}</a>
                        </li>
                        <li class="slide">
                            <a href="{{route('pages')}}" class="side-menu__item {{ request()->is('administrator/web-app/pages*') ? 'active' : '' }}">
                                <i class='bx bx-file me-2'></i>{{ __('sidebar.manage_pages') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{route('links')}}" class="side-menu__item {{ request()->is('administrator/web-app/links*') ? 'active' : '' }}">
                                <i class='bx bx-link me-2'></i>{{ __('sidebar.manage_links') }}
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Blog Management -->
                <li class="slide has-sub {{ request()->is('administrator/web-app/blog-categories*') || request()->is('administrator/web-app/blogs*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item {{ request()->is('administrator/web-app/blog-categories*') || request()->is('administrator/web-app/blogs*') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='bx bx-edit'></i>
                        </span>
                        <span class="side-menu__label">{{ __('sidebar.manage_blog') }}</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">{{ __('sidebar.manage_blog') }}</a>
                        </li>
                        <li class="slide">
                            <a href="{{route('blog.categories')}}" class="side-menu__item {{ request()->is('administrator/web-app/blog-categories*') ? 'active' : '' }}">
                                <i class='bx bx-category me-2'></i>{{ __('sidebar.blog_categories') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{route('blogs')}}" class="side-menu__item {{ request()->is('administrator/web-app/blogs*') ? 'active' : '' }}">
                                <i class='bx bx-news me-2'></i>{{ __('sidebar.blog_articles') }}
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- KONFIGURASI SECTION -->
                <li class="slide__category"><span class="category-name">{{ __('sidebar.system_configuration') }}</span></li>

                <!-- System Settings -->
                <li class="slide has-sub {{ request()->is('administrator/settings*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item {{ request()->is('administrator/settings*') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='bx bx-cog'></i>
                        </span>
                        <span class="side-menu__label">{{ __('sidebar.system_settings') }}</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">{{ __('sidebar.system_settings') }}</a>
                        </li>
                        <li class="slide">
                            <a href="{{route('admin.settings')}}" class="side-menu__item {{ request()->is('administrator/settings') ? 'active' : '' }}">
                                <i class='bx bx-slider me-2'></i>{{ __('sidebar.general_settings') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{route('general.settings')}}" class="side-menu__item {{ request()->is('administrator/settings/general') ? 'active' : '' }}">
                                <i class='bx bx-palette me-2'></i>{{ __('sidebar.display_settings') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{route('website.settings')}}" class="side-menu__item {{ request()->is('administrator/settings/website') ? 'active' : '' }}">
                                <i class='bx bx-text me-2'></i>{{ __('sidebar.text_settings') }}
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{route('notification.settings')}}" class="side-menu__item {{ request()->is('administrator/settings/notifications') ? 'active' : '' }}">
                                <i class='bx bx-bell me-2'></i>{{ __('sidebar.notification_settings') }}
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>

            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg>
            </div>
        </nav>
        <!-- End::nav -->

    </div>
    <!-- End::main-sidebar -->

</aside>
<!-- End::app-sidebar -->