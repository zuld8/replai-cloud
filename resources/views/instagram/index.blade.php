@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
    /* ── Instagram Gradient Button ────────────────────────────── */
    .btn-ig-connect {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        background: linear-gradient(135deg, #833AB4 0%, #E1306C 50%, #F77737 100%);
        box-shadow: 0 3px 12px rgba(225, 48, 108, 0.35);
    }
    .btn-ig-connect:hover {
        opacity: 0.92;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(225, 48, 108, 0.4);
        color: #fff;
        text-decoration: none;
    }
    .btn-ig-connect:active { transform: translateY(0); }
    .btn-ig-connect svg { fill: #fff; }
    .btn-ig-connect-lg {
        padding: 14px 32px;
        font-size: 16px;
        border-radius: 12px;
    }

    /* ── Empty State ─────────────────────────────────────────── */
    .empty-state-ig { padding: 50px 20px; }
    .ig-gradient-icon {
        width: 88px;
        height: 88px;
        border-radius: 26px;
        background: linear-gradient(135deg, #833AB4 0%, #E1306C 50%, #F77737 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        box-shadow: 0 8px 24px rgba(225, 48, 108, 0.25);
    }
    .ig-gradient-icon svg { width: 44px; height: 44px; fill: #fff; }

    /* ── Info Banner ──────────────────────────────────────────── */
    .ig-info-banner {
        background: linear-gradient(135deg, rgba(131,58,180,0.04), rgba(225,48,108,0.04), rgba(247,119,55,0.04));
        border: 1px solid rgba(225,48,108,0.12);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    .ig-info-banner .info-title {
        font-weight: 700;
        color: #1a1a2e;
        font-size: 14px;
        margin-bottom: 6px;
    }
    .ig-info-banner .info-text {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.6;
        margin: 0;
    }
    .ig-info-banner .info-text strong { color: #374151; }
    .ig-info-banner .info-icon {
        color: #E1306C;
        font-size: 22px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* ── Steps ────────────────────────────────────────────────── */
    .ig-steps {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 24px;
        margin-bottom: 28px;
    }
    .ig-step {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px 18px;
        width: 200px;
        text-align: center;
    }
    .ig-step .step-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #833AB4, #E1306C);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
    }
    .ig-step .step-text {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
    }
    .ig-step .step-text strong { color: #374151; }

    /* ── Account Card ────────────────────────────────────────── */
    .ig-account-card {
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .ig-account-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
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
    $__limitOk  = instagram_limitation(my_business());
    $__count    = \App\Models\Meta\InstagramAccount::where('business_id', my_business())->count();
    $__max      = $__pkg ? $__pkg->instagram : 0;
    $__label    = 'Instagram';
@endphp
<div class="btn-list">
    <input type="hidden" id="fbAppId" value="{{platform_currency()->fb_app_id}}">
    <input type="hidden" id="fbConfigInstagram" value="{{platform_currency()->instagram_config_id}}">
    <input type="hidden" id="igAppId" value="{{platform_currency()->ig_app_id ?? platform_currency()->fb_app_id}}">

    {{-- Desktop --}}
    @if($__limitOk)
<a href="javascript:void(0);" onclick="launchInstagramLogin()" class="btn-ig-connect d-none d-sm-inline-flex">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
        </svg>
        Tambah Akun Instagram
    </a>
@else
<span
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    data-bs-title="{{ 'Limit ' . $__label . ' tercapai (' . $__count . '/' . $__max . ' akun). Upgrade paket untuk menambah.' }}"
    style="display:inline-block;opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
    <a tabindex="-1" style="pointer-events:none" onclick="return false;" href="javascript:void(0);" onclick="launchInstagramLogin()" class="btn-ig-connect d-none d-sm-inline-flex">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
        </svg>
        Tambah Akun Instagram
    </a>
</span>
@endif

    {{-- Mobile --}}
    @if($__limitOk)
<a href="javascript:void(0);" onclick="launchInstagramLogin()" class="btn-ig-connect d-sm-none" style="padding: 8px 12px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#fff">
            <path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round" fill="none"/>
        </svg>
    </a>
@else
<span
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    data-bs-title="{{ 'Limit ' . $__label . ' tercapai (' . $__count . '/' . $__max . ' akun). Upgrade paket untuk menambah.' }}"
    style="display:inline-block;opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
    <a tabindex="-1" style="pointer-events:none" onclick="return false;" href="javascript:void(0);" onclick="launchInstagramLogin()" class="btn-ig-connect d-sm-none" style="padding: 8px 12px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#fff">
            <path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round" fill="none"/>
        </svg>
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

    {{-- Info Banner --}}
    <div class="col-12">
        <div class="ig-info-banner d-flex gap-3">
            <i class="ri-instagram-line info-icon"></i>
            <div>
                <div class="info-title">Instagram DM Integration</div>
                <p class="info-text">
                    Terima dan balas Direct Message Instagram langsung dari CRM.<br>
                    Pastikan akun Instagram Anda sudah berupa <strong>Professional Account</strong> (Business atau Creator).<br>
                    <span style="color: #9ca3af;">Caranya: Instagram → Settings → Account → Switch to Professional Account</span>
                </p>
            </div>
        </div>
    </div>

    @forelse ($accounts as $account)
    <div class="col-lg-4 col-md-6 col-sm-12 h-100">
        <div class="card custom-card ig-account-card">
            <div class="card-body">
                <div class="text-center">
                    <span class="avatar avatar-xxxl rounded-circle">
                        <img src="{{$account->profile_picture_url ?? asset('uploads/image.jpg')}}" alt="{{$account->username}}" class="rounded-circle">
                    </span>
                </div>
                <div class="text-center mt-3 mb-3">
                    <h5 class="mb-1">
                        <a href="https://instagram.com/{{$account->username}}" target="_blank" class="text-dark">@<span>{{$account->username}}</span></a>
                    </h5>
                    <p class="mb-0 text-muted">{{$account->name}}</p>

                    @if($account->biography)
                    <p class="mb-2 fs-12 text-muted mt-2">
                        {{Str::limit($account->biography, 100)}}
                    </p>
                    @endif

                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <div class="text-center">
                            <p class="mb-0 fw-semibold">{{number_format($account->followers_count)}}</p>
                            <p class="mb-0 fs-11 text-muted">Followers</p>
                        </div>
                        <div class="text-center">
                            <p class="mb-0 fw-semibold">{{number_format($account->follows_count)}}</p>
                            <p class="mb-0 fs-11 text-muted">Following</p>
                        </div>
                        <div class="text-center">
                            <p class="mb-0 fw-semibold">{{number_format($account->media_count)}}</p>
                            <p class="mb-0 fs-11 text-muted">Posts</p>
                        </div>
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

                    @if($account->unread_messages_count > 0)
                    <span class="badge bg-primary">
                        {{$account->unread_messages_count}} New Messages
                    </span>
                    @endif
                </div>

                <div class="btn-list d-flex justify-content-center gap-2">
                    <button onclick="syncAccount('{{$account->instagram_id}}')" class="btn btn-sm btn-info">
                        <i class="bx bx-refresh me-1"></i>Sync
                    </button>
                    <a href="{{route('instagram.update',$account->id)}}" class="btn btn-sm btn-warning">
                        <i class="bx bx-pencil me-1"></i>Edit
                    </a>
                    <button onclick="disconnectAccount('<?= $account->id;?>')" class="btn btn-sm btn-danger">
                        <i class="bx bx-unlink me-1"></i>Disconnect
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body empty-state-ig text-center">
                <div class="ig-gradient-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </div>
                <h4 class="mb-2">Belum Ada Akun Instagram Terhubung</h4>
                <p class="text-muted mb-0">Hubungkan akun Instagram untuk mulai menerima &amp; membalas DM dari CRM.</p>

                {{-- Steps --}}
                <div class="ig-steps">
                    <div class="ig-step">
                        <div class="step-num">1</div>
                        <div class="step-text">Switch ke <strong>Professional Account</strong> di Instagram</div>
                    </div>
                    <div class="ig-step">
                        <div class="step-num">2</div>
                        <div class="step-text">Klik <strong>Tambah Akun</strong> di bawah</div>
                    </div>
                    <div class="ig-step">
                        <div class="step-num">3</div>
                        <div class="step-text">Login &amp; <strong>izinkan akses</strong></div>
                    </div>
                </div>

                <a href="javascript:void(0);" onclick="launchInstagramLogin()" class="btn-ig-connect btn-ig-connect-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/>
                    </svg>
                    Hubungkan Akun Instagram
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<div id="loaderCallback" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);z-index:9999;justify-content:center;align-items:center;">
    <div class="text-center">
        <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;"></div>
        <p class="text-muted">Menghubungkan Instagram...</p>
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
            version: 'v22.0'
        });
        FB.AppEvents.logPageView();
        console.log('Facebook SDK initialized');
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

<script>
    (function() {
        'use strict';

        const showLoader = () => document.getElementById('loaderCallback').style.display = 'flex';
        const hideLoader = () => document.getElementById('loaderCallback').style.display = 'none';

        // Instagram Login via FB SDK - with retry and auth check
        window.launchInstagramLogin = function() {
            if (typeof FB === 'undefined') {
                alert('Facebook SDK belum load. Refresh halaman dan coba lagi.');
                return;
            }
            showLoader();

            // Facebook OAuth — scope-based (TANPA config_id)
            // Lebih singkat: langsung pilih Page, tidak perlu pilih Business Portfolio
            // Server akan ambil Instagram Business Account yang linked ke Page yang dipilih
            const appId       = document.getElementById('fbAppId').value;
            const redirectUri = '{{ route("instagram.redirect") }}';

            const igAppId = document.getElementById('igAppId')?.value || appId;
            const redirectUri = '{{ route("instagram.redirect") }}';

            if (igAppId && igAppId !== appId) {
                // Instagram Business Login (dedicated Instagram app)
                const params = new URLSearchParams({
                    client_id:     igAppId,
                    redirect_uri:  redirectUri,
                    scope:         'instagram_business_basic,instagram_business_manage_messages',
                    response_type: 'code',
                    state:         'ig_business'
                });
                window.location.href = 'https://www.instagram.com/oauth/authorize?' + params.toString();
            } else {
                // Fallback: Facebook OAuth dialog
                const params = new URLSearchParams({
                    client_id:     appId,
                    redirect_uri:  redirectUri,
                    scope:         'pages_show_list,pages_messaging,instagram_basic,instagram_manage_messages,business_management',
                    response_type: 'code',
                    state:         'fb_oauth'
                });
                window.location.href = 'https://www.facebook.com/v22.0/dialog/oauth?' + params.toString();
            }
        };

        function sendIgToken(authCode, accessToken, userId) {
            console.log("Sending IG token to server...");
            $.ajax({
                url: "{{route('instagram.redirect')}}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                },
                data: JSON.stringify({
                    access_token: accessToken,
                    user_id: userId
                }),
                success: function(data) {
                    console.log("Server response:", data);
                    if (data.success) {
                        toastr.success(data.message || 'Instagram berhasil dihubungkan!');
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        hideLoader();
                        toastr.error(data.message || 'Gagal menghubungkan Instagram.');
                    }
                },
                error: function(xhr) {
                    console.log("Server error:", xhr.responseJSON);
                    hideLoader();
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan.');
                }
            });
        }

        window.syncAccount = function(instagramId) {
            showLoader();
            $.ajax({
                url: "{{route('instagram.sync')}}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                },
                data: JSON.stringify({ instagram_id: instagramId }),
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        toastr.success(data.message || 'Akun berhasil di-sync');
                        location.reload();
                    } else {
                        toastr.error(data.message || 'Gagal sync akun');
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    toastr.error(xhr.responseJSON?.message || 'Gagal sync akun');
                }
            });
        };

        window.disconnectAccount = function(accountId) {
            if (!confirm('Yakin ingin disconnect akun Instagram ini?')) return;
            showLoader();
            $.ajax({
                url: "{{route('instagram')}}" + "/" + accountId,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function() {
                    hideLoader();
                    toastr.success('Akun Instagram berhasil di-disconnect');
                    location.reload();
                },
                error: function(xhr) {
                    hideLoader();
                    toastr.error(xhr.responseJSON?.message || 'Gagal disconnect akun');
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
