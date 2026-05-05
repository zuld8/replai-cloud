@extends('layouts.app')





@section('styles')


<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">


<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">


<style>


    /* ── Facebook Blue Button ─────────────────────────────────────────── */


    .btn-facebook {


        display: inline-flex;


        align-items: center;


        gap: 0;


        background: linear-gradient(180deg, #1877F2 0%, #145DBF 100%);


        border: none;


        border-radius: 6px;


        color: #fff;


        font-family: Helvetica, Arial, sans-serif;


        font-size: 14px;


        cursor: pointer;


        padding: 0;


        overflow: hidden;


        text-decoration: none;


        box-shadow: 0 2px 6px rgba(24,119,242,0.4);


        transition: box-shadow 0.2s, transform 0.1s;


    }


    .btn-facebook:hover {


        box-shadow: 0 4px 14px rgba(24,119,242,0.55);


        transform: translateY(-1px);


        color: #fff;


        text-decoration: none;


    }


    .btn-facebook:active {


        transform: translateY(0);


        box-shadow: 0 2px 4px rgba(24,119,242,0.3);


    }


    .btn-facebook .fb-icon-wrap {


        background-color: rgba(0,0,0,0.15);


        display: flex;


        align-items: center;


        justify-content: center;


        width: 38px;


        height: 38px;


        flex-shrink: 0;


    }


    .btn-facebook .fb-icon-wrap svg {


        width: 20px;


        height: 20px;


        fill: #fff;


    }


    .btn-facebook .fb-text {


        padding: 0 14px;


        display: flex;


        flex-direction: column;


        align-items: flex-start;


        line-height: 1.2;


    }


    .btn-facebook .fb-text .fb-main {


        font-size: 14px;


        font-weight: 700;


        white-space: nowrap;


    }


    .btn-facebook .fb-text .fb-sub {


        font-size: 11px;


        font-weight: 400;


        opacity: 0.88;


        white-space: nowrap;


    }


    /* Large version for center empty state */


    .btn-facebook-lg .fb-icon-wrap {


        width: 48px;


        height: 48px;


    }


    .btn-facebook-lg .fb-icon-wrap svg {


        width: 26px;


        height: 26px;


    }


    .btn-facebook-lg .fb-text .fb-main {


        font-size: 16px;


    }


    .btn-facebook-lg .fb-text .fb-sub {


        font-size: 12px;


    }


    .btn-facebook-lg .fb-text {


        padding: 0 20px;


    }


    .btn-facebook-lg {


        border-radius: 8px;


    }


</style>


@endsection





@section('button')
<style>
.btn-limit-disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
    filter: grayscale(0.5);
    display: inline-block;
}
.btn-limit-disabled > * {
    pointer-events: none;
}
</style>
@php
    $__pkg      = \App\Models\Setting::where('id', my_business())->first(['id'])?->package_active;
    $__limitOk  = messenger_limitation(my_business());
    $__count    = \App\Models\Meta\MessengerAccount::where('business_id', my_business())->count();
    $__max      = $__pkg ? $__pkg->messanger : 0;
    $__label    = 'Messenger';
@endphp



<div class="btn-list">


    <input type="hidden" id="fbAppId" value="{{platform_currency()->fb_app_id}}">


    <input type="hidden" id="fbConfigMessenger" value="{{platform_currency()->messanger_config_id}}">





    {{-- Desktop: Facebook-style Connect button --}}


    @if($__limitOk)
<a href="javascript:void(0);" onclick="launchMessengerLogin()" class="btn-facebook d-none d-sm-inline-flex">


        <span class="fb-icon-wrap">


            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">


                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>


            </svg>


        </span>


        <span class="fb-text">


            <span class="fb-main">Tambah Page</span>


            <span class="fb-sub">Login with Facebook</span>


        </span>


    </a>
@else
<span
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    data-bs-title="{{ 'Limit ' . $__label . ' tercapai (' . $__count . '/' . $__max . ' akun). Upgrade paket untuk menambah.' }}"
    style="display:inline-block;opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
    <a tabindex="-1" style="pointer-events:none" onclick="return false;" href="javascript:void(0);" onclick="launchMessengerLogin()" class="btn-facebook d-none d-sm-inline-flex">


        <span class="fb-icon-wrap">


            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">


                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>


            </svg>


        </span>


        <span class="fb-text">


            <span class="fb-main">Tambah Page</span>


            <span class="fb-sub">Login with Facebook</span>


        </span>


    </a>
</span>
@endif





    {{-- Mobile: icon-only --}}


    @if($__limitOk)
<a href="javascript:void(0);" onclick="launchMessengerLogin()" class="btn-facebook d-sm-none">


        <span class="fb-icon-wrap">


            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">


                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>


            </svg>


        </span>


    </a>
@else
<span
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    data-bs-title="{{ 'Limit ' . $__label . ' tercapai (' . $__count . '/' . $__max . ' akun). Upgrade paket untuk menambah.' }}"
    style="display:inline-block;opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
    <a tabindex="-1" style="pointer-events:none" onclick="return false;" href="javascript:void(0);" onclick="launchMessengerLogin()" class="btn-facebook d-sm-none">


        <span class="fb-icon-wrap">


            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">


                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>


            </svg>


        </span>


    </a>
</span>
@endif


</div>



@endsection





@section('content')


<div class="row">


    <div class="col-12">


        <x-validation-component></x-validation-component>


    </div>





    @forelse ($accounts as $account)


    <div class="col-lg-4 col-md-6 col-sm-12 h-100">


        <div class="card custom-card">


            <div class="card-body">


                <div class="text-center">


                    <span class="avatar avatar-xxxl rounded-circle">


                        <img src="{{$account->page_picture_url ?? asset('uploads/image.jpg')}}"


                            alt="{{$account->page_name}}"


                            class="rounded-circle">


                    </span>


                </div>





                <div class="text-center mt-3 mb-3">


                    <h5 class="mb-1">


                        <a href="#" class="text-dark">{{$account->page_name}}</a>


                    </h5>





                    @if($account->page_username)


                    <p class="mb-0 text-muted">@<?= $account->page_username; ?></p>


                    @endif





                    @if($account->category)


                    <span class="badge bg-light text-dark mt-2">


                        <i class="ri-bookmark-line me-1"></i>{{$account->category}}


                    </span>


                    @endif





                    @if($account->about)


                    <p class="mb-2 fs-12 text-muted mt-2">


                        {{Str::limit($account->about, 100)}}


                    </p>


                    @endif





                    <div class="d-flex justify-content-center gap-3 mt-3">


                        <div class="text-center">


                            <p class="mb-0 fw-semibold">{{number_format($account->followers_count)}}</p>


                            <p class="mb-0 fs-11 text-muted">Followers</p>


                        </div>


                        @if($account->phone)


                        <div class="text-center">


                            <p class="mb-0 fw-semibold">


                                <i class="ri-phone-line"></i>


                            </p>


                            <p class="mb-0 fs-11 text-muted">{{$account->phone}}</p>


                        </div>


                        @endif


                    </div>


                </div>





                <div class="btn-list text-center mb-3">


                    @if($account->status == 'active')


                    <span class="badge bg-success-transparent">


                        <i class="ri-checkbox-circle-line me-1"></i>Active


                    </span>


                    @elseif($account->status == 'expired')


                    <span class="badge bg-warning-transparent">


                        <i class="ri-error-warning-line me-1"></i>Token Expired


                    </span>


                    @else


                    <span class="badge bg-danger-transparent">


                        <i class="ri-close-circle-line me-1"></i>Error


                    </span>


                    @endif





                    @if($account->auto_reply_method != 'none')


                    <span class="badge bg-info-transparent">


                        <i class="ri-robot-line me-1"></i>{{ucfirst($account->auto_reply_method)}}


                    </span>


                    @endif


                </div>





                <div class="btn-list d-flex justify-content-center gap-2">


                    <button onclick="syncAccount('{{$account->page_id}}')" class="btn btn-sm btn-info">


                        <i class="bx bx-refresh me-1"></i>Sync


                    </button>


                    <a href="{{route('messenger.update', $account->id)}}" class="btn btn-sm btn-warning">


                        <i class="bx bx-pencil me-1"></i>Edit


                    </a>


                    <button onclick="disconnectAccount('{{$account->id}}')" class="btn btn-sm btn-danger">


                        <i class="bx bx-unlink me-1"></i>Disconnect


                    </button>


                </div>


            </div>





            @if($account->website || $account->email)


            <div class="card-footer border-block-start-dashed">


                <div class="d-flex justify-content-center gap-3 fs-12">


                    @if($account->website)


                    <a href="{{$account->website}}" target="_blank" class="text-muted">


                        <i class="ri-global-line me-1"></i>Website


                    </a>


                    @endif


                    @if($account->email)


                    <a href="mailto:{{$account->email}}" class="text-muted">


                        <i class="ri-mail-line me-1"></i>Email


                    </a>


                    @endif


                </div>


            </div>


            @endif


        </div>


    </div>


    @empty


    <div class="col-12">


        <div class="card custom-card">


            <div class="card-body text-center py-5">


                <i class="bx bxl-messenger" style="font-size: 72px; opacity: 0.3;"></i>


                <h4 class="mt-3">No Messenger Pages Connected</h4>


                <p class="text-muted">Click the "Connect Messenger" button above to connect your Facebook Page</p>


                <div class="mt-4 d-flex justify-content-center">


                    <a href="javascript:void(0);" onclick="launchMessengerLogin()" class="btn-facebook btn-facebook-lg">


                        <span class="fb-icon-wrap">


                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">


                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>


                            </svg>


                        </span>


                        <span class="fb-text">


                            <span class="fb-main">Tambah Page</span>


                            <span class="fb-sub">Login with Facebook</span>


                        </span>


                    </a>


                </div>


            </div>


        </div>


    </div>


    @endforelse


</div>





<div id="loaderCallback" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:9999;justify-content:center;align-items:center;">


    <div class="spinner-border text-primary" role="status">


        <span class="visually-hidden">Loading...</span>


    </div>


</div>


@endsection





@section('scripts')


<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>


<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>





{{-- Facebook SDK --}}


<script>


    window.fbAsyncInit = function() {


        FB.init({


            appId: '{{ platform_currency()->fb_app_id }}',


            cookie: true,


            xfbml: true,


            version: 'v21.0'


        });





        FB.AppEvents.logPageView();


        console.log('Facebook SDK initialized for Messenger');


    };





    (function(d, s, id) {


        var js, fjs = d.getElementsByTagName(s)[0];


        if (d.getElementById(id)) return;


        js = d.createElement(s);


        js.id = id;


        js.src = "https://connect.facebook.com/en_US/sdk.js";


        fjs.parentNode.insertBefore(js, fjs);


    }(document, 'script', 'facebook-jssdk'));


</script>





{{-- Messenger OAuth --}}


<script>


    (function() {


        'use strict';





        const showLoader = () => document.getElementById('loaderCallback').style.display = 'flex';


        const hideLoader = () => document.getElementById('loaderCallback').style.display = 'none';





window.launchMessengerLogin = function() {
            if (typeof FB === 'undefined') {
                alert('Facebook SDK belum load. Refresh halaman dan coba lagi.');
                return;
            }

            showLoader();

            FB.login(function(response) {
                console.log("Messenger login response:", JSON.stringify(response));

                if (response.authResponse && response.authResponse.accessToken) {
                    const accessToken = response.authResponse.accessToken;
                    console.log("Got access token, sending to server...");

                    $.ajax({
                        url: "{{route('messenger.redirect')}}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'application/json',
                        },
                        data: JSON.stringify({
                            access_token: accessToken,
                            user_id: response.authResponse.userID
                        }),
                        success: function(data) {
                            console.log("Server response:", data);
                            if (data.success) {
                                toastr.success(data.message || 'Facebook Pages berhasil terhubung!');
                                setTimeout(function() { location.reload(); }, 1500);
                            } else {
                                hideLoader();
                                toastr.error(data.message || 'Gagal menghubungkan halaman.');
                            }
                        },
                        error: function(xhr) {
                            console.log("Server error:", xhr.responseJSON);
                            hideLoader();
                            toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menghubungkan halaman.');
                        }
                    });
                } else {
                    hideLoader();
                    console.log('Login cancelled or not authorized');
                    toastr.warning('Login Facebook dibatalkan atau tidak diizinkan.');
                }
            }, {
                scope: 'pages_show_list,pages_messaging,pages_read_engagement,pages_manage_metadata',
                return_scopes: true,
            });
        };



        window.syncAccount = function(pageId) {


            showLoader();





            $.ajax({


                url: "{{route('messenger.sync')}}",


                type: 'POST',


                headers: {


                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),


                    'Content-Type': 'application/json',


                },


                data: JSON.stringify({


                    page_id: pageId


                }),


                success: function(data) {


                    hideLoader();


                    if (data.status) {


                        toastr.success(data.message || 'Page synced successfully');


                        location.reload();


                    } else {


                        toastr.error(data.message || 'Failed to sync page');


                    }


                },


                error: function(xhr) {


                    hideLoader();


                    toastr.error(xhr.responseJSON?.message || 'Failed to sync page');


                }


            });


        };





        window.disconnectAccount = function(accountId) {


            if (!confirm('Are you sure you want to disconnect this Messenger page?')) {


                return;


            }





            showLoader();





            $.ajax({


                url: "{{route('messenger')}}" + "/" + accountId,


                type: 'DELETE',


                headers: {


                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),


                },


                success: function() {


                    hideLoader();


                    toastr.success('Messenger page disconnected successfully');


                    location.reload();


                },


                error: function(xhr) {


                    hideLoader();


                    toastr.error(xhr.responseJSON?.message || 'Failed to disconnect page');


                }


            });


        };





    })();


</script>


@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipEls.forEach(function(el) { new bootstrap.Tooltip(el, {trigger: 'hover focus'}); });
});
</script>
@endpush
