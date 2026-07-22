@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
    /* ── WA Hybrid Badge ──────────────────────────────────────────────── */
    .badge-wa-hybrid {
        display: inline-flex; align-items: center; gap: 4px;
        background: linear-gradient(90deg, #25D366, #128C7E);
        color: #fff; font-size: 0.68rem; font-weight: 700;
        padding: 2px 9px; border-radius: 20px; letter-spacing: .03em;
        vertical-align: middle; margin-left: 5px;
    }
    .badge-api-only {
        display: inline-flex; align-items: center; gap: 4px;
        background: #6c757d; color: #fff; font-size: 0.68rem;
        font-weight: 700; padding: 2px 9px; border-radius: 20px;
        letter-spacing: .03em; vertical-align: middle; margin-left: 5px;
    }
    /* ── Facebook Blue Button (WABA) ─────────────────────────────────── */
    .btn-fb-waba {
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
    .btn-fb-waba:hover {
        box-shadow: 0 4px 14px rgba(24,119,242,0.55);
        transform: translateY(-1px);
        color: #fff;
        text-decoration: none;
    }
    .btn-fb-waba:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(24,119,242,0.3);
    }
    .btn-fb-waba .fw-icon-wrap {
        background-color: rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        flex-shrink: 0;
    }
    .btn-fb-waba .fw-icon-wrap svg {
        width: 20px;
        height: 20px;
        fill: #fff;
    }
    .btn-fb-waba .fw-text {
        padding: 0 14px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.2;
    }
    .btn-fb-waba .fw-text .fw-main {
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }
    .btn-fb-waba .fw-text .fw-sub {
        font-size: 11px;
        font-weight: 400;
        opacity: 0.88;
        white-space: nowrap;
    }
    /* ── Manual Add Button ─────────────────────────────────────────────── */
    .btn-manual-add {
        display: inline-flex;
        align-items: center;
        gap: 0;
        background: linear-gradient(180deg, #495057 0%, #343a40 100%);
        border: none;
        border-radius: 6px;
        color: #fff;
        font-family: Helvetica, Arial, sans-serif;
        font-size: 14px;
        cursor: pointer;
        padding: 0;
        overflow: hidden;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(52,58,64,0.35);
        transition: box-shadow 0.2s, transform 0.1s;
    }
    .btn-manual-add:hover {
        box-shadow: 0 4px 14px rgba(52,58,64,0.5);
        transform: translateY(-1px);
        color: #fff;
        text-decoration: none;
    }
    .btn-manual-add:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(52,58,64,0.25);
    }
    .btn-manual-add .bma-icon-wrap {
        background-color: rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        flex-shrink: 0;
    }
    .btn-manual-add .bma-icon-wrap svg {
        width: 20px;
        height: 20px;
        fill: #fff;
    }
    .btn-manual-add .bma-text {
        padding: 0 14px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.2;
    }
    .btn-manual-add .bma-text .bma-main {
        font-size: 14px;
        font-weight: 700;
        white-space: nowrap;
    }
</style>
@endsection


@section('button')
<div class="btn-list">
    <input type="hidden" id="fbId" value="{{platform_currency()->fb_app_id}}">
    <input type="hidden" id="fbConfig" value="{{platform_currency()->fb_config_id}}">

    @php
        $bizId       = my_business();
        $pkg         = $bizId ? \App\Models\Setting::find($bizId)?->package_active : null;
        $wabaLimited = $pkg && $pkg->limit_waba === 'yes';
        $wabaMax     = $wabaLimited ? (int)($pkg->waba_limit ?? 0) : 0;
        $wabaCount   = $accounts->count();
        // Block if: no package OR limit reached
        $wabaFull    = !$pkg || ($wabaLimited && $wabaCount >= $wabaMax);
        $lockTip     = !$pkg
            ? 'Tidak ada paket aktif. Subscribe untuk menggunakan WA Business API.'
            : 'Limit WA Business API tercapai (' . $wabaCount . '/' . $wabaMax . ' akun). Upgrade paket untuk menambah.';
    @endphp

    @if(!$wabaFull)
        {{-- Desktop: Manual add button --}}
        <a href="{{route('waba.create')}}" class="btn-manual-add d-none d-sm-inline-flex me-2">
            <span class="bma-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
            </span>
            <span class="bma-text">
                <span class="bma-main">Tambah Akun Manual</span>
            </span>
        </a>

        {{-- Desktop: Facebook login button --}}
        <a href="javascript:void(0);" onclick="launchWhatsAppSignup()" class="btn-fb-waba d-none d-sm-inline-flex">
            <span class="fw-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </span>
            <span class="fw-text">
                <span class="fw-main">Tambah WhatsApp Business API</span>
                <span class="fw-sub">Login with Facebook</span>
            </span>
        </a>

        {{-- Mobile: icon-only buttons --}}
        <a href="{{route('waba.create')}}" class="btn-manual-add d-sm-none me-2">
            <span class="bma-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
            </span>
        </a>
        <a href="javascript:void(0);" onclick="launchWhatsAppSignup()" class="btn-fb-waba d-sm-none">
            <span class="fw-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </span>
        </a>
    @else
        {{-- Limit / No-package: disabled buttons same style as active, with padlock --}}

        {{-- Desktop: Manual Add (disabled) --}}
        <span
            class="d-none d-sm-inline-flex me-2"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="{{ $lockTip }}"
            style="opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
            <a tabindex="-1" style="pointer-events:none" onclick="return false;" class="btn-manual-add">
                <span class="bma-icon-wrap">
                    <i class="bx bx-lock" style="font-size:18px;line-height:1;"></i>
                </span>
                <span class="bma-text">
                    <span class="bma-main">Tambah Akun Manual</span>
                </span>
            </a>
        </span>

        {{-- Desktop: Facebook WABA (disabled) --}}
        <span
            class="d-none d-sm-inline-flex"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="{{ $lockTip }}"
            style="opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
            <a tabindex="-1" style="pointer-events:none" onclick="return false;" class="btn-fb-waba">
                <span class="fw-icon-wrap">
                    <i class="bx bx-lock" style="font-size:18px;color:#fff;line-height:1;"></i>
                </span>
                <span class="fw-text">
                    <span class="fw-main">Tambah WhatsApp Business API</span>
                    @if($pkg)
                        <span class="fw-sub">Limit tercapai ({{ $wabaCount }}/{{ $wabaMax }})</span>
                    @else
                        <span class="fw-sub">Paket tidak aktif</span>
                    @endif
                </span>
            </a>
        </span>

        {{-- Mobile: icon-only disabled --}}
        <span
            class="d-sm-none me-2"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="{{ $lockTip }}"
            style="opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
            <a tabindex="-1" style="pointer-events:none" onclick="return false;" class="btn-manual-add">
                <span class="bma-icon-wrap">
                    <i class="bx bx-lock" style="font-size:18px;line-height:1;"></i>
                </span>
            </a>
        </span>
        <span
            class="d-sm-none"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="{{ $lockTip }}"
            style="opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
            <a tabindex="-1" style="pointer-events:none" onclick="return false;" class="btn-fb-waba">
                <span class="fw-icon-wrap">
                    <i class="bx bx-lock" style="font-size:18px;color:#fff;line-height:1;"></i>
                </span>
            </a>
        </span>
    @endif
</div>
@endsection

@section('content')
<div class="row g-3">
    @foreach ($accounts as $account)
    @php
        $detailBusiness = json_decode($account->details, true);
        $firstDevice    = $account->devices->first();
        $metaWa         = json_decode($firstDevice?->meta_data ?? '{}', true);
        $platformType   = $metaWa['whatsapp']['platform_type'] ?? null;
        // is_coexistence = true jika smb_message_echoes pernah diterima (paling reliable)
        // ATAU platform_type === 'COEXISTENCE' dari Meta GET endpoint
        // is_coexistence dari smb_message_echoes (paling reliable)
        // ATAU platform_type bukan CLOUD_API → semua akun non-cloud = hybrid
        // Nilai yang diketahui: 'NOT_APPLICABLE', 'COEXISTENCE', 'SMB_APP'
        $isCoexistence  = !empty($metaWa['whatsapp']['is_coexistence'])
                        || ($platformType && $platformType !== 'CLOUD_API');
        $displayPhone   = $metaWa['whatsapp']['display_phone_number'] ?? '';
        $reviewStatus   = $detailBusiness['healt_status']['data']['account_review_status'] ?? '-';
        $qualityRating  = strtoupper($metaWa['whatsapp']['quality_rating'] ?? '');

        // Avatar: inisial dari nama WA
        $__nm    = $detailBusiness['healt_status']['data']['name'] ?? ($account->name ?? 'WA');
        $_wds    = array_values(array_filter(
            preg_split('/\s+/', trim($__nm)),
            fn($w) => $w !== '' && ctype_alpha(mb_substr($w, 0, 1))
        ));
        $__init  = count($_wds) >= 2
            ? strtoupper(mb_substr($_wds[0],0,1) . mb_substr($_wds[1],0,1))
            : strtoupper(mb_substr($_wds[0] ?? 'WA', 0, 2));
        $pal     = ['#EAF3FC|#1B5FA6','#F1ECFE|#5B3FB0','#FBEAF0|#993556','#DCFCE7|#166534','#FEF3C7|#854F0B'];
        [$abg,$afg] = explode('|', $pal[abs(crc32($__nm)) % count($pal)]);
        $photoUrl   = $detailBusiness['business_detail']['profile_picture_url'] ?? null;
    @endphp
    <div class="col-lg-4 col-md-6 col-sm-12">
        <div class="card waba-account-card">
            <div class="card-body text-center py-4 px-3">

                {{-- Avatar: foto atau inisial --}}
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt=""
                         style="width:48px;height:48px;border-radius:50%;object-fit:cover;display:block;margin:0 auto 10px;border:2px solid #E4EAF2;">
                @else
                    <div style="width:48px;height:48px;border-radius:50%;background:{{$abg}};color:{{$afg}};
                         display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:600;
                         margin:0 auto 10px;flex-shrink:0;">
                        {{ $__init }}
                    </div>
                @endif

                {{-- Nama --}}
                <div style="font-weight:700;font-size:13.5px;color:#1E2A4A;margin-bottom:2px;
                     overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;margin-left:auto;margin-right:auto;"
                     title="{{ $detailBusiness['healt_status']['data']['name'] ?? $account->name ?? '' }}">
                    {{ $detailBusiness['healt_status']['data']['name'] ?? $account->name ?? '—' }}
                </div>

                {{-- Nomor --}}
                @if($displayPhone)
                <div style="font-size:12px;color:#64748B;margin-bottom:10px;">
                    {{ $displayPhone }}
                </div>
                @else
                <div style="font-size:11px;color:#94A3B8;margin-bottom:10px;font-style:italic;">
                    — nomor belum tersinkron —
                </div>
                @endif

                {{-- Badges --}}
                <div style="display:flex;flex-wrap:wrap;gap:5px;justify-content:center;">

                    {{-- Platform type badge (FIXED: pakai platform_type, bukan account_mode) --}}
                    @if($isCoexistence)
                        <span class="waba-badge waba-badge-hybrid" title="WA Business App + API sekaligus">🔀 WA Hybrid</span>
                    @else
                        <span class="waba-badge waba-badge-api" title="API Only mode">⚡ API Only</span>
                    @endif

                    {{-- Review status --}}
                    @if($reviewStatus === 'APPROVED')
                        <span class="waba-badge waba-badge-approved">✓ Approved</span>
                    @elseif(in_array($reviewStatus, ['PENDING','PENDING_REVIEW']))
                        <span class="waba-badge waba-badge-pending">⏳ Pending</span>
                    @elseif($reviewStatus !== '-')
                        <span class="waba-badge waba-badge-muted">{{ $reviewStatus }}</span>
                    @endif

                    {{-- Quality rating --}}
                    @if(in_array($qualityRating, ['HIGH','GREEN']))
                        <span class="waba-badge waba-badge-green">● Green</span>
                    @elseif(in_array($qualityRating, ['MEDIUM','YELLOW']))
                        <span class="waba-badge waba-badge-limited">● Medium</span>
                    @elseif(in_array($qualityRating, ['LOW','RED']))
                        <span class="waba-badge waba-badge-red">● Low</span>
                    @endif

                </div>
            </div>
            <div class="card-footer text-center py-2" style="background:transparent;border-top:0.5px solid #F1F5F9;">
                <a href="{{ route('waba.update', $account->id) }}"
                   style="border:1px solid #E4EAF2;color:#2E8DE1;border-radius:8px;font-size:12px;padding:5px 16px;display:inline-flex;align-items:center;gap:5px;text-decoration:none;transition:all 0.15s;"
                   onmouseover="this.style.background='#EAF3FC'" onmouseout="this.style.background='transparent'">
                    <i class="bx bx-settings"></i>Informasi Detail
                </a>
            </div>
        </div>
    </div>
    @endforeach
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
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>
<script>
    function copyId(id) {
        document.execCommand("copy");

        const tempInput = document.createElement("textarea");
        tempInput.value = id;
        document.body.appendChild(tempInput);

        tempInput.select();
        document.execCommand("copy");

        document.body.removeChild(tempInput);

        toastr.success("{{__('master.device.copied_device_id')}}", {
            timeOut: 5e3,
            closeButton: !0,
            debug: !1,
            newestOnTop: !0,
            progressBar: !0,
            positionClass: 'toast-top-right',
            preventDuplicates: !0,
            onclick: null,
            showDuration: '100',
            hideDuration: '1000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
            tapToDismiss: !1,
        })
    }

    $(function(e) {
        'use strict';

        $('#whatsappData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("master.device.search")}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });
</script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
<script>
    (function() {
        'use strict';
        window.fbAsyncInit = function() {
            FB.init({
                appId: '{{platform_currency()->fb_app_id}}',
                autoLogAppEvents: true,
                xfbml: true, 
                version: 'v23.0'
            });
        };
    })();
</script>

<script>
    (function() {
        'use strict';

        let tempAccessCode = '';
        let phoneNumberId  = '';
        let waBaId         = '';
        let waitInterval   = null;   // outer scope so CANCEL can stop it
        let flowCancelled  = false;  // prevent auto-discover after CANCEL

        const showLoader = () => document.getElementById('loaderCallback').style.display = 'flex';
        const hideLoader = () => document.getElementById('loaderCallback').style.display = 'none';

        // PASANG listener SEBELUM panggil FB.login
        const sessionInfoListener = (event) => {
            if (event.origin !== "https://www.facebook.com") return;

            try {
                const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
                if (data.type === 'WA_EMBEDDED_SIGNUP') {
                    console.log('[WABA] FB event:', data.event, JSON.stringify(data.data));
                    if (data.event === 'FINISH') {
                        phoneNumberId = data.data.phone_number_id;
                        waBaId        = data.data.waba_id;
                        console.log('[WABA] FINISH → phone:', phoneNumberId, 'waba:', waBaId);
                    } else if (data.event === 'CANCEL') {
                        hideLoader();
                        flowCancelled  = true;
                        tempAccessCode = '';   // reset so auto-discover won't trigger
                        if (waitInterval) { clearInterval(waitInterval); waitInterval = null; }
                        const step = data.data?.current_step || '';
                        const msg  = step === 'WHATSAPP_BUSINESS_APP_ONBOARDING_PHONE_CREATION'
                            ? 'Pendaftaran dibatalkan saat verifikasi nomor. Pastikan nomor WhatsApp Anda belum terdaftar sebagai WABA lain.'
                            : 'Pendaftaran dibatalkan.';
                        console.log('[WABA] CANCEL step:', step);
                        toastr.warning(msg, 'Dibatalkan', { timeOut: 6000, closeButton: true });
                    } else if (data.event === 'ERROR') {
                        hideLoader();
                        console.warn('[WABA] ERROR dari Meta:', JSON.stringify(data.data));
                        toastr.error('Error dari Meta: ' + (data.data?.message || JSON.stringify(data.data)), 'Error', { timeOut: 6000 });
                    }
                }} catch (e) {
                console.log('Non-JSON message:', event.data);
            }
        };

        window.addEventListener('message', sessionInfoListener);

        // Launch
        window.launchWhatsAppSignup = function() {
            showLoader();
            console.log("hai");
            FB.login(function(response) {
                if (response.authResponse) {
                    tempAccessCode = response.authResponse.code;

                    // Tunggu postMessage dari Meta (maks 8 detik), lalu fallback ke auto-discover
                    let waitCount  = 0;
                    const waitFull = 16; // 8 detik (16x 500ms) - tunggu FINISH postMessage
                    const maxWait  = 90; // 45 detik total timeout
                    waitInterval = setInterval(() => {
                        waitCount++;
                        console.log('[WABA] Waiting... code:', !!tempAccessCode, 'phoneId:', !!phoneNumberId, 'wabaId:', !!waBaId, '(' + (waitCount * 0.5).toFixed(1) + 's)');

                        if (tempAccessCode && phoneNumberId && waBaId) {
                            // Ideal: semua data lengkap dari postMessage
                            clearInterval(waitInterval);
                            sendSignupData(tempAccessCode, phoneNumberId, waBaId);
                        } else if (tempAccessCode && waitCount >= waitFull) {
                            // Code ada tapi phoneId/wabaId tidak datang lewat postMessage
                            // → kirim ke backend, biarkan backend auto-discover dari Meta API
                            clearInterval(waitInterval);
                            console.log('[WABA] postMessage tidak datang, auto-discover via backend...');
                            toastr.info('Sedang menghubungkan akun...', '', { timeOut: 3000 });
                            sendSignupData(tempAccessCode, phoneNumberId || '', waBaId || '');
                        } else if (waitCount >= maxWait) {
                            clearInterval(waitInterval);
                            hideLoader();
                            toastr.error(
                                'Koneksi timeout. Pastikan Anda menyelesaikan seluruh tahap pendaftaran, lalu coba lagi.',
                                'Koneksi Gagal',
                                { timeOut: 10000, closeButton: true }
                            );
                        }
                    }, 500);
                } else {
                    hideLoader();
                    alert('Login dibatalkan atau tidak diotorisasi sepenuhnya.');
                }
            }, {
                config_id: $("#fbConfig").val(),
                response_type: 'code',
                override_default_response_type: true,
                extras: {
                    featureType: 'whatsapp_business_app_onboarding',
                    sessionInfoVersion: 3,
                    version: 'v3',
                    setup: {}
                }
            });
        };

        function sendSignupData(code, phoneId, wabaId) {
             
            $.ajax({
                url: '/app/waba/embed/syncron',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                data: JSON.stringify({
                    request_code: code,
                    phone_number_id: phoneId,
                    waba_id: wabaId
                }),
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        toastr.success(data.message || "Berhasil sinkronisasi.");
                        location.reload();
                    } else {
                        toastr.success(data.message || "Berhasil sinkronisasi.");

                    }

                },
                error: function(xhr) {
                    hideLoader();
                    const errMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat sinkronisasi.';
                    toastr.error(errMsg);
                }
            });
        }

    })();
</script>

@endsection