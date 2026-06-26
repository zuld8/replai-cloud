@extends('layouts.admin')

@section('button')
<div class="btn-list">
    <a href="{{route('packages')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('auth.back_to_package')}}
    </a>
    <a href="{{route('packages')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('auth.back_to_package')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<style>
/* ══ Package Form Brand Styles ══════════════════════════ */
.pkg-section {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #E4EAF2;
    margin-bottom: 20px;
    overflow: hidden;
}
.pkg-section-hdr {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    font-weight: 600;
    font-size: 13px;
    border-left: 4px solid #2E8DE1;
    background: #F7FBFF;
    color: #1E2A4A;
}
.pkg-section-hdr.hdr-fitur  { border-left-color: #5B3FB0; background: #FAF8FF; }
.pkg-section-hdr.hdr-kuota  { border-left-color: #D4537E; background: #FFF7FA; }
.pkg-section-hdr.hdr-umum   { border-left-color: #16A34A; background: #F0FBF4; }
.pkg-section-hdr i { font-size: 16px; }
.pkg-section-hdr.hdr-platform i { color: #2E8DE1; }
.pkg-section-hdr.hdr-fitur  i   { color: #5B3FB0; }
.pkg-section-hdr.hdr-kuota  i   { color: #D4537E; }
.pkg-section-hdr.hdr-umum   i   { color: #16A34A; }

.pkg-body { padding: 18px 20px; }

/* ── Package Table ── */
.pkg-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.pkg-table thead th {
    background: #F5F8FC;
    color: #64748B;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 14px;
    border-bottom: 1px solid #E4EAF2;
}
.pkg-table tbody tr { border-bottom: 1px solid #F0F4F8; }
.pkg-table tbody tr:last-child { border-bottom: none; }
.pkg-table tbody tr:hover { background: #FBFDFF; }
.pkg-table td { padding: 12px 14px; vertical-align: middle; }
.pkg-table td:first-child { min-width: 170px; }
.pkg-table td:nth-child(2) { min-width: 160px; }
.pkg-table td:nth-child(3) { min-width: 140px; }
.pkg-table td:nth-child(4) { min-width: 150px; }

/* ── Row platform label ── */
.pkg-platform-label {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; font-weight: 500; color: #1E2A4A;
}
.pkg-platform-label i { font-size: 17px; }
.pkg-platform-label small { color: #94A3B8; font-size: 11px; }

/* ── Limit badges ── */
.badge-unlimited {
    display: inline-flex; align-items: center; gap: 4px;
    background: #DCFCE7; color: #15803D;
    border-radius: 20px; padding: 3px 10px; font-size: 12px; font-weight: 600;
}
.badge-limited {
    display: inline-flex; align-items: center; gap: 4px;
    background: #EAF3FC; color: #1B5FA6;
    border-radius: 20px; padding: 3px 10px; font-size: 12px; font-weight: 600;
}
/* ── Chip toggle (Aktif/Nonaktif) ── */
.pkg-chip-row { display: flex; flex-wrap: wrap; gap: 12px; padding: 16px 20px; }
.pkg-chip { display: flex; align-items: center; gap: 8px; }
.pkg-chip label { font-size: 12px; color: #64748B; font-weight: 500; }
.pkg-chip select.form-control {
    padding: 4px 8px; font-size: 12px; border-radius: 6px;
    border: 1px solid #E4EAF2; height: auto; width: auto;
}

/* ── Sticky submit ── */
.pkg-sticky { position: sticky; bottom: 0; z-index: 99; padding: 12px 0 4px; background: #fff; }

/* ── Form control brand ── */
.form-control:focus { border-color: #2E8DE1 !important; box-shadow: 0 0 0 3px rgba(46,141,225,.15) !important; }
.form-control { border-color: #E4EAF2; border-radius: 7px; }
.input-group-text { background: #F5F8FC; border-color: #E4EAF2; color: #64748B; font-size: 12px; }
.pkg-helper { font-size: 11px; color: #94A3B8; margin-top: 4px; }
.pkg-ai-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px; }
@media(max-width:576px) { .pkg-ai-row { grid-template-columns: 1fr; } }
</style>

<form action="<?= route('packages.store'); ?>" enctype="multipart/form-data" method="POST" class="row">
    @csrf

    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>

    {{-- ═══ 1. INFORMASI UMUM ════════════════════════════════════════ --}}
    <div class="col-12">
        <div class="pkg-section">
            <div class="pkg-section-hdr hdr-umum">
                <i class="bx bx-info-circle"></i>
                Informasi Umum
            </div>
            <div class="pkg-body">
                <div class="row">
                    {{-- Nama Paket --}}
                    <div class="col-lg-6 col-sm-12 mt-2">
                        <label class="form-label">{{__('auth.package_name')}} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-package"></i></span>
                            <input class="form-control" name="name" value="{{old('name')}}" type="text" placeholder="Contoh: Paket Premium" required>
                        </div>
                    </div>

                    {{-- Masa Aktif Paket --}}
                    <div class="col-lg-6 col-sm-12 mt-2">
                        <label class="form-label">Masa Aktif Paket <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <select class="form-control dayoption" name="days_option">
                                <option value="limited" @if(old('days_option')=='limited' ) selected @endif>Berhari-hari</option>
                                <option value="unlimited" @if(old('days_option')=='unlimited' ) selected @endif>Selamanya</option>
                            </select>
                        </div>
                    </div>

                    {{-- Lama Aktif --}}
                    <div class="col-lg-6 col-sm-12 mt-2 @if(old('days_option') == 'unlimited') d-none @endif formdays">
                        <label class="form-label">Lama Aktif (hari) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-time"></i></span>
                            <input class="form-control" name="add_days" value="{{old('add_days')}}" type="number" placeholder="30" required>
                            <span class="input-group-text">hari</span>
                        </div>
                    </div>

                    {{-- Storage --}}
                    <div class="col-lg-6 col-sm-12 mt-2">
                        <label class="form-label">{{__('auth.storage_mb')}} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-hdd"></i></span>
                            <input class="form-control" name="storage" value="{{old('storage')}}" type="number" placeholder="1024" required>
                            <span class="input-group-text">MB</span>
                        </div>
                    </div>

                    {{-- Kuota AI Credit --}}
                    <div class="col-lg-6 col-sm-12 mt-2">
                        <label class="form-label">Kuota AI Credit <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-bot"></i></span>
                            <input class="form-control" name="ai_response" value="{{old('ai_response')}}" type="number" placeholder="1000" required>
                        </div>
                        <div class="pkg-helper">Kuota pemakaian AI per paket</div>
                    </div>

                    {{-- Paket Trial --}}
                    <div class="col-lg-6 col-sm-12 mt-2">
                        <label class="form-label">Paket Trial? <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-gift"></i></span>
                            <select class="form-control trialversion" name="trial_version">
                                <option value="">{{__('general.choose')}}</option>
                                <option value="no" @if(old('trial_version') == 'no') selected @endif>Tidak (berbayar)</option>
                                <option value="yes" @if(old('trial_version') == 'yes') selected @endif>Ya (gratis trial)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Harga --}}
                    <div class="col-lg-6 col-sm-12 mt-2 @if(old('trial_version') == 'yes') d-none @endif formprice">
                        <label class="form-label">{{__('auth.price')}} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input class="form-control" name="price" value="{{old('price')}}" type="number" placeholder="99000">
                        </div>
                    </div>
                </div>

                {{-- Chips: Ongkir · Google Sheet · MUA --}}
                <div class="pkg-chip-row mt-3 pt-3" style="border-top:1px solid #EEF2F7;">
                    <div class="pkg-chip">
                        <i class="bx bx-car" style="color:#2E8DE1;font-size:16px;"></i>
                        <label>Integrasi Ongkir</label>
                        <select class="form-control" name="ongkir" style="width:auto;">
                            <option value="no" @if(old('ongkir')=='no') selected @endif>Nonaktif</option>
                            <option value="yes" @if(old('ongkir')=='yes') selected @endif>Aktif</option>
                        </select>
                    </div>
                    <div class="pkg-chip">
                        <i class="bx bxl-google" style="color:#16A34A;font-size:16px;"></i>
                        <label>Integrasi Google Sheet</label>
                        <select class="form-control" name="google_sheet" style="width:auto;">
                            <option value="no" @if(old('google_sheet')=='no') selected @endif>Nonaktif</option>
                            <option value="yes" @if(old('google_sheet')=='yes') selected @endif>Aktif</option>
                        </select>
                    </div>
                    <div class="pkg-chip">
                        <i class="bx bx-user-circle" style="color:#5B3FB0;font-size:16px;"></i>
                        <label>Modul MUA</label>
                        <select class="form-control" id="muaOption" name="mua_limit_optin" style="width:auto;">
                            <option value="no" @if(old('mua_limit_optin')=='no') selected @endif>Nonaktif</option>
                            <option value="limited" @if(old('mua_limit_optin')=='limited') selected @endif>Aktif</option>
                        </select>
                        <div class="muaOption @if(old('mua_limit_optin') != 'limited') d-none @endif ms-2">
                            <div class="input-group input-group-sm">
                                <input class="form-control" name="mua_limit" value="{{old('mua_limit')}}" type="number" placeholder="100" style="width:80px;" required>
                                <span class="input-group-text">user</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ 2. PLATFORM & KONEKSI ════════════════════════════════════ --}}
    <div class="col-12">
        <div class="pkg-section">
            <div class="pkg-section-hdr hdr-platform">
                <i class="bx bx-devices"></i>
                Platform &amp; Koneksi
                <small style="font-weight:400;font-size:11px;color:#64748B;margin-left:6px;">(batas jumlah akun/device per platform)</small>
            </div>
            <div class="table-responsive">
                <table class="pkg-table">
                    <thead>
                        <tr>
                            <th>Platform</th>
                            <th>Batas</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- WhatsApp Personal --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bxl-whatsapp" style="color:#25D366;"></i>
                                    WhatsApp Personal
                                    <small>(device)</small>
                                </div>
                            </td>
                            <td>
                                <select class="form-control limitdevice" name="limit_device" required>
                                    <option value="no" @if(old('limit_device')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_device')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formdevice-inf @if(old('limit_device') == 'yes') d-none @endif">∞</span>
                                <div class="formdevice @if(old('limit_device') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="device_limit" value="{{old('device_limit')}}" type="number" placeholder="5">
                                        <span class="input-group-text">Device</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{-- WhatsApp Business (WABA) --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bx-badge-check" style="color:#25D366;"></i>
                                    WhatsApp Business
                                    <small>(WABA)</small>
                                </div>
                            </td>
                            <td>
                                <select class="form-control limitwaba" name="limit_waba" required>
                                    <option value="no" @if(old('limit_waba')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_waba')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formwaba-inf @if(old('limit_waba') == 'yes') d-none @endif">∞</span>
                                <div class="formwaba @if(old('limit_waba') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="waba_limit" value="{{old('waba_limit')}}" type="number" placeholder="1">
                                        <span class="input-group-text">Akun</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{-- Telegram --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bxl-telegram" style="color:#229ED9;"></i>
                                    Telegram
                                </div>
                            </td>
                            <td>
                                <select class="form-control limittelegram" name="limit_telegram" required>
                                    <option value="no" @if(old('limit_telegram')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_telegram')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formtelegram-inf @if(old('limit_telegram') == 'yes') d-none @endif">∞</span>
                                <div class="formtelegram @if(old('limit_telegram') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="telegram" value="{{old('telegram')}}" type="number" placeholder="3">
                                        <span class="input-group-text">Akun</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{-- Instagram --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bxl-instagram" style="color:#E1306C;"></i>
                                    Instagram
                                </div>
                            </td>
                            <td>
                                <select class="form-control limitinstagram" name="limit_instagram" required>
                                    <option value="no" @if(old('limit_instagram')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_instagram')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited forminstagram-inf @if(old('limit_instagram') == 'yes') d-none @endif">∞</span>
                                <div class="forminstagram @if(old('limit_instagram') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="instagram" value="{{old('instagram')}}" type="number" placeholder="1">
                                        <span class="input-group-text">Akun</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{-- Messenger --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bxl-messenger" style="color:#0084FF;"></i>
                                    Messenger
                                </div>
                            </td>
                            <td>
                                <select class="form-control limitmessanger" name="limit_messanger" required>
                                    <option value="no" @if(old('limit_messanger')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_messanger')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formmessanger-inf @if(old('limit_messanger') == 'yes') d-none @endif">∞</span>
                                <div class="formmessanger @if(old('limit_messanger') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="messanger" value="{{old('messanger')}}" type="number" placeholder="1">
                                        <span class="input-group-text">Akun</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{-- Live Chat --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bx-chat" style="color:#2E8DE1;"></i>
                                    Live Chat
                                </div>
                            </td>
                            <td>
                                <select class="form-control livechatlimit" name="livechat_limit" required>
                                    <option value="no" @if(old('livechat_limit')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('livechat_limit')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formlivechat-inf @if(old('livechat_limit') == 'yes') d-none @endif">∞</span>
                                <div class="formlivechat @if(old('livechat_limit') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="limit_livechat" value="{{old('limit_livechat')}}" type="number" placeholder="3">
                                        <span class="input-group-text">Platform</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══ 3. FITUR & TOOLS ═════════════════════════════════════════ --}}
    <div class="col-12">
        <div class="pkg-section">
            <div class="pkg-section-hdr hdr-fitur">
                <i class="bx bx-cog"></i>
                Fitur &amp; Tools
            </div>
            <div class="table-responsive">
                <table class="pkg-table">
                    <thead>
                        <tr>
                            <th>Fitur</th>
                            <th>Batas</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Human Agent --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bx-user" style="color:#5B3FB0;"></i>
                                    Akses Agen (Human Agent)
                                </div>
                            </td>
                            <td>
                                <select class="form-control limituser" name="limit_user_option" required>
                                    <option value="no" @if(old('limit_user_option')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_user_option')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formlimituser-inf @if(old('limit_user_option') == 'yes') d-none @endif">∞</span>
                                <div class="formlimituser @if(old('limit_user_option') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="users_limit" value="{{old('users_limit')}}" type="number" placeholder="5">
                                        <span class="input-group-text">User</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{-- Template Pesan --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bx-message-square-detail" style="color:#5B3FB0;"></i>
                                    Template Pesan
                                </div>
                            </td>
                            <td>
                                <select class="form-control templatelimit" name="limit_template" required>
                                    <option value="no" @if(old('limit_template')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_template')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formtemplate-inf @if(old('limit_template') == 'yes') d-none @endif">∞</span>
                                <div class="formtemplate @if(old('limit_template') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="template_limit" value="{{old('template_limit')}}" type="number" placeholder="20">
                                        <span class="input-group-text">Template</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{-- ChatBot --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bx-message-rounded-dots" style="color:#5B3FB0;"></i>
                                    ChatBot
                                </div>
                            </td>
                            <td>
                                <select class="form-control chatbotlimit" name="limit_chatbot" required>
                                    <option value="no" @if(old('limit_chatbot')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_chatbot')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formchatbot-inf @if(old('limit_chatbot') == 'yes') d-none @endif">∞</span>
                                <div class="formchatbot @if(old('limit_chatbot') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="chatbot_limit" value="{{old('chatbot_limit')}}" type="number" placeholder="5">
                                        <span class="input-group-text">ChatBot</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        {{-- AI Training --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bx-brain" style="color:#5B3FB0;"></i>
                                    AI Training
                                </div>
                            </td>
                            <td>
                                <select class="form-control ailimit" name="limit_ai_training" required>
                                    <option value="no" @if(old('limit_ai_training')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_ai_training')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formai-inf @if(old('limit_ai_training') == 'yes') d-none @endif">∞</span>
                                <div class="formai @if(old('limit_ai_training') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="ai_training_limit" value="{{old('ai_training_limit')}}" type="number" placeholder="10">
                                        <span class="input-group-text">Training</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- RAG knowledge size sub-row --}}
            <div class="pkg-body pt-0" style="border-top:1px solid #EEF2F7;">
                <div class="row mt-3">
                    <div class="col-12 mb-2">
                        <span style="font-size:11px;font-weight:600;color:#5B3FB0;text-transform:uppercase;letter-spacing:.5px;">
                            <i class="bx bx-database me-1"></i>Ukuran Knowledge / RAG
                        </span>
                        <div class="pkg-helper">Ukuran materi knowledge yang bisa di-upload untuk melatih AI agent</div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <label class="form-label" style="font-size:12px;">Ukuran maks per file (MB)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bx bx-file"></i></span>
                            <input class="form-control" name="max_per_upload" value="{{old('max_per_upload')}}" type="number" placeholder="10">
                            <span class="input-group-text">MB</span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <label class="form-label" style="font-size:12px;">Total ukuran knowledge (MB)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bx bx-folder"></i></span>
                            <input class="form-control" name="max_total_rag" value="{{old('max_total_rag')}}" type="number" placeholder="100">
                            <span class="input-group-text">MB</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ 4. KUOTA & PERIODE ═══════════════════════════════════════ --}}
    <div class="col-12">
        <div class="pkg-section">
            <div class="pkg-section-hdr hdr-kuota">
                <i class="bx bx-broadcast"></i>
                Kuota &amp; Periode
                <small style="font-weight:400;font-size:11px;color:#64748B;margin-left:6px;">(limit di-reset tiap periode yang dipilih)</small>
            </div>
            <div class="table-responsive">
                <table class="pkg-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Batas</th>
                            <th>Jumlah</th>
                            <th>Periode Reset</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Kirim WhatsApp --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bxl-whatsapp" style="color:#25D366;"></i>
                                    Kirim WhatsApp
                                </div>
                            </td>
                            <td>
                                <select class="form-control limitwhatsapp" name="limit_whatsapp_option" required>
                                    <option value="no" @if(old('limit_whatsapp_option')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_whatsapp_option')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formwhatsapp-inf @if(old('limit_whatsapp_option') == 'yes') d-none @endif">∞</span>
                                <div class="formwhatsapp @if(old('limit_whatsapp_option') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="whatsapp_limit" value="{{old('whatsapp_limit')}}" type="number" placeholder="1000">
                                        <span class="input-group-text">Pesan</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="formwhatsapp-priode @if(old('limit_whatsapp_option') != 'yes') d-none @endif">
                                    <select class="form-control" name="limit_whatsapp_priode">
                                        <option value="daily" @if(old('limit_whatsapp_priode')=='daily') selected @endif>Harian</option>
                                        <option value="monthly" @if(old('limit_whatsapp_priode')=='monthly') selected @endif>Bulanan</option>
                                        <option value="yearly" @if(old('limit_whatsapp_priode')=='yearly') selected @endif>Tahunan</option>
                                    </select>
                                </div>
                                <span class="formwhatsapp-priode-na @if(old('limit_whatsapp_option') == 'yes') d-none @endif" style="color:#94A3B8;font-size:12px;">—</span>
                            </td>
                        </tr>
                        {{-- Email Blast --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bx-envelope" style="color:#D4537E;"></i>
                                    Email Blast
                                </div>
                            </td>
                            <td>
                                <select class="form-control emaillimit" name="limit_email" required>
                                    <option value="no" @if(old('limit_email')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_email')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formemail-inf @if(old('limit_email') == 'yes') d-none @endif">∞</span>
                                <div class="formemail @if(old('limit_email') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="email_blast" value="{{old('email_blast')}}" type="number" placeholder="3000">
                                        <span class="input-group-text">Email</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="formemail-priode @if(old('limit_email') != 'yes') d-none @endif">
                                    <select class="form-control" name="limit_email_priode">
                                        <option value="daily" @if(old('limit_email_priode')=='daily') selected @endif>Harian</option>
                                        <option value="monthly" @if(old('limit_email_priode')=='monthly') selected @endif>Bulanan</option>
                                        <option value="yearly" @if(old('limit_email_priode')=='yearly') selected @endif>Tahunan</option>
                                    </select>
                                </div>
                                <span class="formemail-priode-na @if(old('limit_email') == 'yes') d-none @endif" style="color:#94A3B8;font-size:12px;">—</span>
                            </td>
                        </tr>
                        {{-- Scraping Data --}}
                        <tr>
                            <td>
                                <div class="pkg-platform-label">
                                    <i class="bx bx-data" style="color:#D4537E;"></i>
                                    Scraping Data
                                </div>
                            </td>
                            <td>
                                <select class="form-control scrappinglimit" name="limit_scrapp_option" required>
                                    <option value="no" @if(old('limit_scrapp_option')=='no') selected @endif>Tidak Terbatas</option>
                                    <option value="yes" @if(old('limit_scrapp_option')=='yes') selected @endif>Terbatas</option>
                                </select>
                            </td>
                            <td>
                                <span class="badge-unlimited formscrapping-inf @if(old('limit_scrapp_option') == 'yes') d-none @endif">∞</span>
                                <div class="formscrapping @if(old('limit_scrapp_option') != 'yes') d-none @endif">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" name="scrapp_limit" value="{{old('scrapp_limit')}}" type="number" placeholder="500">
                                        <span class="input-group-text">Data</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="formscrapping-priode @if(old('limit_scrapp_option') != 'yes') d-none @endif">
                                    <select class="form-control" name="limit_scrapp_priode">
                                        <option value="daily" @if(old('limit_scrapp_priode')=='daily') selected @endif>Harian</option>
                                        <option value="monthly" @if(old('limit_scrapp_priode')=='monthly') selected @endif>Bulanan</option>
                                        <option value="yearly" @if(old('limit_scrapp_priode')=='yearly') selected @endif>Tahunan</option>
                                    </select>
                                </div>
                                <span class="formscrapping-priode-na @if(old('limit_scrapp_option') == 'yes') d-none @endif" style="color:#94A3B8;font-size:12px;">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══ SUBMIT ════════════════════════════════════════════════════ --}}
    <div class="col-12">
        <div class="pkg-sticky">
            <button type="submit" class="btn w-100" style="background:#2E8DE1;color:#fff;font-weight:600;padding:12px;border-radius:9px;font-size:14px;">
                <i class="bx bx-save me-2"></i>
                Simpan Perubahan
            </button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // ── Generic helper to toggle ∞ badge and input ──
    function limitToggle(selectClass, formClass, infClass) {
        $(selectClass).on("change", function() {
            var isLimited = $(this).val() == 'yes';
            $(formClass).toggleClass('d-none', !isLimited);
            $(infClass).toggleClass('d-none', isLimited);
        });
    }

    limitToggle(".limitdevice",   ".formdevice",   ".formdevice-inf");
    limitToggle(".limitwaba",     ".formwaba",     ".formwaba-inf");
    limitToggle(".limittelegram", ".formtelegram", ".formtelegram-inf");
    limitToggle(".limitinstagram",".forminstagram",".forminstagram-inf");
    limitToggle(".limitmessanger",".formmessanger",".formmessanger-inf");
    limitToggle(".limituser",     ".formlimituser",".formlimituser-inf");
    limitToggle(".templatelimit", ".formtemplate", ".formtemplate-inf");
    limitToggle(".chatbotlimit",  ".formchatbot",  ".formchatbot-inf");
    limitToggle(".ailimit",       ".formai",       ".formai-inf");

    // Live Chat: same but val='no' means Tidak Terbatas → hide input
    $(".livechatlimit").on("change", function() {
        var isLimited = $(this).val() == 'yes';
        $(".formlivechat").toggleClass('d-none', !isLimited);
        $(".formlivechat-inf").toggleClass('d-none', isLimited);
    });

    // Quota rows: also toggle periode col
    $(".limitwhatsapp").on("change", function() {
        var isLimited = $(this).val() == 'yes';
        $(".formwhatsapp").toggleClass('d-none', !isLimited);
        $(".formwhatsapp-inf").toggleClass('d-none', isLimited);
        $(".formwhatsapp-priode").toggleClass('d-none', !isLimited);
        $(".formwhatsapp-priode-na").toggleClass('d-none', isLimited);
    });
    $(".emaillimit").on("change", function() {
        var isLimited = $(this).val() == 'yes';
        $(".formemail").toggleClass('d-none', !isLimited);
        $(".formemail-inf").toggleClass('d-none', isLimited);
        $(".formemail-priode").toggleClass('d-none', !isLimited);
        $(".formemail-priode-na").toggleClass('d-none', isLimited);
    });
    $(".scrappinglimit").on("change", function() {
        var isLimited = $(this).val() == 'yes';
        $(".formscrapping").toggleClass('d-none', !isLimited);
        $(".formscrapping-inf").toggleClass('d-none', isLimited);
        $(".formscrapping-priode").toggleClass('d-none', !isLimited);
        $(".formscrapping-priode-na").toggleClass('d-none', isLimited);
    });

    // Trial toggle
    $(".trialversion").on("change", function() {
        $(".formprice").toggleClass('d-none', $(this).val() == 'yes');
    });

    // Day option toggle
    $(".dayoption").on("change", function() {
        $(".formdays").toggleClass('d-none', $(this).val() == 'unlimited');
    });

    // MUA option
    $("#muaOption").on("change", function() {
        $(".muaOption").toggleClass('d-none', $(this).val() != 'limited');
    });
</script>
@endsection
