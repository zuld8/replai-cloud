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
.pkg-body {
    padding: 16px 20px 12px;
}
.pkg-cat {
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 0.8px;
    color: #94a3b8;
    text-transform: uppercase;
    margin: 10px 0 5px;
    padding-bottom: 4px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 5px;
}
.pkg-cat:first-child { margin-top: 0; }
.pkg-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    color: #475569;
    padding: 3px 0;
}
.pkg-item .dot-ok {
    width: 18px; height: 18px; border-radius: 50%;
    background: rgba(16,185,129,0.12);
    border: 1px solid rgba(16,185,129,0.3);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pkg-item .dot-ok i { color: #10b981; font-size: 11px; }
.pkg-item .dot-no {
    width: 18px; height: 18px; border-radius: 50%;
    background: rgba(148,163,184,0.12);
    border: 1px solid rgba(148,163,184,0.3);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pkg-item .dot-no i { color: #94a3b8; font-size: 11px; }
.pkg-item .val { font-weight: 600; color: #334155; }
.pkg-footer { padding: 12px 16px 16px; display: flex; gap: 8px; }
</style>

<div class="row">
    <div class="col-12">
        <x-validation-component></x-validation-component>

        <div class="row">
            @foreach ($packages as $i => $package)
            @php
                $colors = [
                    0 => ['from'=>'#6366f1','to'=>'#8b5cf6'],
                    1 => ['from'=>'#3b82f6','to'=>'#06b6d4'],
                    2 => ['from'=>'#10b981','to'=>'#14b8a6'],
                    3 => ['from'=>'#f59e0b','to'=>'#f97316'],
                    4 => ['from'=>'#ef4444','to'=>'#ec4899'],
                ];
                $clr = $colors[$i % 5];
                $isFree = $package->trial_version == 'yes';
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

                        {{-- Storage & AI --}}
                        <div class="pkg-cat"><i class="bx bx-cloud"></i> Storage & AI</div>
                        <div class="pkg-item">
                            <div class="{{ $package->storage < 1 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->storage < 1 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Storage <span class="val">({{$package->storage_name}})</span></span>
                        </div>
                        <div class="pkg-item">
                            <div class="{{ $package->ai_response < 1 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->ai_response < 1 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>AI Credit <span class="val">({{number_format($package->ai_response)}})</span></span>
                        </div>

                        {{-- Platform --}}
                        <div class="pkg-cat"><i class="bx bx-devices"></i> Platform</div>
                        {{-- WA Personal --}}
                        <div class="pkg-item">
                            <div class="{{ $package->limit_device == 'yes' && $package->device_limit == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_device == 'yes' && $package->device_limit == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>WA Personal <span class="val">({{$package->limit_device == 'yes' ? number_format($package->device_limit) : __('auth.unlimited')}})</span></span>
                        </div>
                        {{-- WABA --}}
                        <div class="pkg-item">
                            <div class="{{ $package->limit_waba == 'yes' && $package->waba_limit == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_waba == 'yes' && $package->waba_limit == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>WA Business API <span class="val">({{$package->limit_waba == 'yes' ? number_format($package->waba_limit) : __('auth.unlimited')}})</span></span>
                        </div>
                        {{-- Telegram --}}
                        <div class="pkg-item">
                            <div class="{{ $package->limit_telegram == 'yes' && $package->telegram == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_telegram == 'yes' && $package->telegram == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Telegram <span class="val">({{$package->limit_telegram == 'yes' ? number_format($package->telegram) : __('auth.unlimited')}})</span></span>
                        </div>
                        {{-- Instagram --}}
                        <div class="pkg-item">
                            <div class="{{ $package->limit_instagram == 'yes' && $package->instagram == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_instagram == 'yes' && $package->instagram == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Instagram <span class="val">({{$package->limit_instagram == 'yes' ? number_format($package->instagram) : __('auth.unlimited')}})</span></span>
                        </div>
                        {{-- Messenger --}}
                        <div class="pkg-item">
                            <div class="{{ $package->limit_messanger == 'yes' && $package->messanger == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_messanger == 'yes' && $package->messanger == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Facebook Messenger <span class="val">({{$package->limit_messanger == 'yes' ? number_format($package->messanger) : __('auth.unlimited')}})</span></span>
                        </div>
                        {{-- Live Chat --}}
                        <div class="pkg-item">
                            <div class="{{ $package->livechat_limit == 'yes' && $package->limit_livechat == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->livechat_limit == 'yes' && $package->limit_livechat == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Live Chat Widget <span class="val">({{$package->livechat_limit == 'yes' ? number_format($package->limit_livechat) : __('auth.unlimited')}})</span></span>
                        </div>

                        {{-- Features --}}
                        <div class="pkg-cat"><i class="bx bx-cog"></i> Features</div>
                        <div class="pkg-item">
                            <div class="{{ $package->limit_user_option == 'yes' && $package->users_limit == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_user_option == 'yes' && $package->users_limit == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Human Agent <span class="val">({{$package->limit_user_option == 'yes' ? number_format($package->users_limit) : __('auth.unlimited')}})</span></span>
                        </div>
                        <div class="pkg-item">
                            <div class="{{ $package->limit_template == 'yes' && $package->template_limit == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_template == 'yes' && $package->template_limit == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Message Template <span class="val">({{$package->limit_template == 'yes' ? number_format($package->template_limit) : __('auth.unlimited')}})</span></span>
                        </div>
                        <div class="pkg-item">
                            <div class="{{ $package->limit_chatbot == 'yes' && $package->chatbot_limit == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_chatbot == 'yes' && $package->chatbot_limit == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>ChatBot <span class="val">({{$package->limit_chatbot == 'yes' ? number_format($package->chatbot_limit) : __('auth.unlimited')}})</span></span>
                        </div>
                        <div class="pkg-item">
                            <div class="{{ $package->limit_ai_training == 'yes' && $package->ai_training_limit == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_ai_training == 'yes' && $package->ai_training_limit == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>AI Training <span class="val">({{$package->limit_ai_training == 'yes' ? number_format($package->ai_training_limit) : __('auth.unlimited')}})</span></span>
                        </div>
                        <div class="pkg-item">
                            <div class="dot-ok"><i class="bx bx-check"></i></div>
                            <span>RAG File <span class="val">{{$package->max_per_upload}}MB/file · {{$package->max_total_rag}}MB total</span></span>
                        </div>

                        {{-- Broadcast --}}
                        <div class="pkg-cat"><i class="bx bx-broadcast"></i> Broadcast & Data</div>
                        <div class="pkg-item">
                            <div class="{{ $package->limit_whatsapp_option == 'yes' && $package->whatsapp_limit == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_whatsapp_option == 'yes' && $package->whatsapp_limit == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>WA Blast <span class="val">({{$package->limit_whatsapp_option == 'yes' ? number_format($package->whatsapp_limit) : __('auth.unlimited')}}{{$package->whatsapp_priode ? '/'.$package->whatsapp_priode : ''}})</span></span>
                        </div>
                        <div class="pkg-item">
                            <div class="{{ $package->limit_email_option == 'yes' && $package->email_limit == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_email_option == 'yes' && $package->email_limit == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Email Blast <span class="val">({{$package->limit_email_option == 'yes' ? number_format($package->email_limit) : __('auth.unlimited')}}{{$package->email_priode ? '/'.$package->email_priode : ''}})</span></span>
                        </div>
                        <div class="pkg-item">
                            <div class="{{ $package->limit_scrapp_option == 'yes' && $package->scrapp_limit == 0 ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->limit_scrapp_option == 'yes' && $package->scrapp_limit == 0 ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Data Scraping <span class="val">({{$package->limit_scrapp_option == 'yes' ? number_format($package->scrapp_limit) : __('auth.unlimited')}}{{$package->scrapping_priode ? '/'.$package->scrapping_priode : ''}})</span></span>
                        </div>

                        {{-- Integration --}}
                        <div class="pkg-cat"><i class="bx bx-link"></i> Integration</div>
                        <div class="pkg-item">
                            <div class="{{ $package->cek_ongkir == 'no' ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->cek_ongkir == 'no' ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Shipping Cost Check</span>
                        </div>
                        <div class="pkg-item">
                            <div class="{{ $package->google_sheet == 'no' ? 'dot-no' : 'dot-ok' }}">
                                <i class="bx {{ $package->google_sheet == 'no' ? 'bx-x' : 'bx-check' }}"></i>
                            </div>
                            <span>Google Sheet</span>
                        </div>

                    </div>{{-- pkg-body --}}

                    {{-- FOOTER BUTTONS --}}
                    <div class="pkg-footer" style="border-top:1px solid #f1f5f9;">
                        <a href="{{route('packages.update',$package->id)}}" class="btn btn-warning btn-sm w-100" style="font-size:12px;font-weight:600;">
                            <i class="bx bx-pencil me-1"></i>{{__('auth.edit_package')}}
                        </a>
                        <a href="{{route('packages.delete',$package->id)}}" class="btn btn-danger btn-sm w-100" style="font-size:12px;font-weight:600;">
                            <i class="bx bx-trash me-1"></i>{{__('auth.delete_package')}}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
