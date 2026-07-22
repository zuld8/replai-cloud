@extends('layouts.admin')

@section('button')
<div class="btn-list">
    <a href="{{route('packages.create')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-plus-circle"></i>
        {{__('general.add_data')}}
    </a>
    <a href="{{route('packages.create')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('general.add_data')}}">
        <i class="bx bx-plus-circle"></i>
    </a>
</div>
@endsection

@section('content')
<style>
/* ── Card wrapper ────────────────────────────────────── */
.pkg-card {
    border: none !important;
    border-radius: 14px !important;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
    margin-bottom: 20px;
}
.pkg-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.14);
}
/* ── Header ──────────────────────────────────────────── */
.pkg-header {
    padding: 20px 20px 16px;
    color: white;
}
.pkg-price {
    font-size: 26px;
    font-weight: 800;
    line-height: 1.1;
}
.pkg-period {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 2px;
}
/* ── Body ────────────────────────────────────────────── */
.pkg-body {
    padding: 14px 18px 10px;
}
.pkg-cat {
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 0.8px;
    color: #94a3b8;
    text-transform: uppercase;
    margin: 10px 0 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.pkg-cat:first-child { margin-top: 0; }
/* ── Row: label kiri · value kanan ──────────────────── */
.pkg-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding: 3px 0;
    font-size: 13px;
    border-bottom: 1px solid #f8fafc;
}
.pkg-lbl {
    color: #64748B;
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.pkg-val {
    font-weight: 600;
    color: #1E2A4A;
    white-space: nowrap;
    flex-shrink: 0;
}
.pkg-val .bx-check { color: #16A34A; font-size: 15px; }
.pkg-val .bx-x     { color: #DC2626; font-size: 15px; }
/* ── Footer ──────────────────────────────────────────── */
.pkg-footer {
    padding: 12px 16px;
    display: flex;
    gap: 8px;
    border-top: 1px solid #f1f5f9;
}
.pkg-footer .btn { flex: 1; font-size: 12px; font-weight: 600; }
</style>

<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">{{__('general.packages')}}</h2>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @foreach ($packages as $i => $package)
        @php
            $isFree = $package->price == 0;
            $colors = [
                ['from'=>'#667eea','to'=>'#764ba2'],
                ['from'=>'#2E8DE1','to'=>'#1a5fa8'],
                ['from'=>'#11998e','to'=>'#38ef7d'],
                ['from'=>'#f7971e','to'=>'#ffd200'],
                ['from'=>'#c94b4b','to'=>'#4b134f'],
            ];
            $clr = $colors[$i % count($colors)];
        @endphp
        <div class="col-xxl-4 col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="pkg-card card">

                {{-- HEADER --}}
                <div class="pkg-header" style="background:linear-gradient(135deg,{{$clr['from']}},{{$clr['to']}});">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <i class="ti ti-businessplan" style="font-size:18px;opacity:0.9;"></i>
                        <span style="font-size:15px;font-weight:700;">{{$package->name}}</span>
                    </div>
                    <div class="pkg-price">
                        {{$isFree ? __('auth.package_free') : currency_format($package->price)}}
                    </div>
                    <div class="pkg-period">
                        / {{$package->days_option == 'limited' ? number_format($package->add_days).' '.__('auth.package_days') : __('auth.package_forever')}}
                    </div>
                </div>

                {{-- BODY --}}
                <div class="pkg-body">

                    {{-- ── Storage & AI ─────────────────────── --}}
                    <div class="pkg-cat"><i class="bx bx-cloud"></i> Storage & AI</div>

                    <div class="pkg-row">
                        <span class="pkg-lbl">Storage</span>
                        <span class="pkg-val">{{$package->storage_name}}</span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">Kredit AI</span>
                        <span class="pkg-val">{{number_format($package->ai_response)}}</span>
                    </div>
                    {{-- Kredit Pesan (BARU) --}}
                    <div class="pkg-row">
                        <span class="pkg-lbl">Kredit Pesan</span>
                        <span class="pkg-val">
                            @if($package->message_limit_option == 'yes')
                                {{number_format($package->message_limit)}}/{{$package->message_limit_priode ?? 'bln'}}
                            @else
                                {{__('auth.unlimited')}}
                            @endif
                        </span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">RAG File</span>
                        <span class="pkg-val">{{$package->max_per_upload}}MB/file · {{$package->max_total_rag}}MB total</span>
                    </div>

                    {{-- ── Platform ──────────────────────────── --}}
                    <div class="pkg-cat"><i class="bx bx-devices"></i> Platform</div>

                    <div class="pkg-row">
                        <span class="pkg-lbl">WA Personal</span>
                        <span class="pkg-val">{{$package->limit_device == 'yes' ? number_format($package->device_limit) : __('auth.unlimited')}}</span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">WA Business API</span>
                        <span class="pkg-val">{{$package->limit_waba == 'yes' ? number_format($package->waba_limit) : __('auth.unlimited')}}</span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">Telegram</span>
                        <span class="pkg-val">{{$package->limit_telegram == 'yes' ? number_format($package->telegram) : __('auth.unlimited')}}</span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">Instagram</span>
                        <span class="pkg-val">{{$package->limit_instagram == 'yes' ? number_format($package->instagram) : __('auth.unlimited')}}</span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">Facebook Messenger</span>
                        <span class="pkg-val">{{$package->limit_messanger == 'yes' ? number_format($package->messanger) : __('auth.unlimited')}}</span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">Live Chat Widget</span>
                        <span class="pkg-val">{{$package->livechat_limit == 'yes' ? number_format($package->limit_livechat) : __('auth.unlimited')}}</span>
                    </div>

                    {{-- ── Fitur ─────────────────────────────── --}}
                    <div class="pkg-cat"><i class="bx bx-cog"></i> Fitur</div>

                    <div class="pkg-row">
                        <span class="pkg-lbl">Human Agent</span>
                        <span class="pkg-val">{{$package->limit_user_option == 'yes' ? number_format($package->users_limit) : __('auth.unlimited')}}</span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">Message Template</span>
                        <span class="pkg-val">{{$package->limit_template == 'yes' ? number_format($package->template_limit) : __('auth.unlimited')}}</span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">ChatBot</span>
                        <span class="pkg-val">{{$package->limit_chatbot == 'yes' ? number_format($package->chatbot_limit) : __('auth.unlimited')}}</span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">AI Training</span>
                        <span class="pkg-val">{{$package->limit_ai_training == 'yes' ? number_format($package->ai_training_limit) : __('auth.unlimited')}}</span>
                    </div>

                    {{-- ── Broadcast & Data ──────────────────── --}}
                    <div class="pkg-cat"><i class="bx bx-broadcast"></i> Broadcast & Data</div>

                    <div class="pkg-row">
                        <span class="pkg-lbl">WA Blast</span>
                        <span class="pkg-val">
                            {{$package->limit_whatsapp_option == 'yes' ? number_format($package->whatsapp_limit) : __('auth.unlimited')}}{{$package->limit_whatsapp_option == 'yes' && $package->limit_whatsapp_priode ? '/'.$package->limit_whatsapp_priode : ''}}
                        </span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">Email Blast</span>
                        <span class="pkg-val">
                            {{$package->limit_email_option == 'yes' ? number_format($package->email_limit) : __('auth.unlimited')}}{{$package->limit_email_option == 'yes' && $package->limit_email_priode ? '/'.$package->limit_email_priode : ''}}
                        </span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">Data Scraping</span>
                        <span class="pkg-val">
                            {{$package->limit_scrapp_option == 'yes' ? number_format($package->scrapp_limit) : __('auth.unlimited')}}{{$package->limit_scrapp_option == 'yes' && $package->limit_scrapp_priode ? '/'.$package->limit_scrapp_priode : ''}}
                        </span>
                    </div>

                    {{-- ── Integrasi ─────────────────────────── --}}
                    <div class="pkg-cat"><i class="bx bx-link"></i> Integrasi</div>

                    <div class="pkg-row">
                        <span class="pkg-lbl">Cek Ongkir</span>
                        <span class="pkg-val">
                            <i class="bx {{ $package->cek_ongkir == 'yes' ? 'bx-check' : 'bx-x' }}"></i>
                        </span>
                    </div>
                    <div class="pkg-row">
                        <span class="pkg-lbl">Google Sheet</span>
                        <span class="pkg-val">
                            <i class="bx {{ $package->google_sheet == 'yes' ? 'bx-check' : 'bx-x' }}"></i>
                        </span>
                    </div>

                </div>{{-- pkg-body --}}

                {{-- FOOTER BUTTONS --}}
                <div class="pkg-footer">
                    <a href="{{route('packages.update',$package->id)}}" class="btn btn-warning btn-sm">
                        <i class="bx bx-pencil me-1"></i>{{__('auth.edit_package')}}
                    </a>
                    <a href="{{route('packages.delete',$package->id)}}" class="btn btn-danger btn-sm">
                        <i class="bx bx-trash me-1"></i>{{__('auth.delete_package')}}
                    </a>
                </div>

            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
