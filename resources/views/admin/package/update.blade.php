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
<form action="<?= route('packages.edit', $package->id); ?>" enctype="multipart/form-data" method="POST" class="row">
    @csrf
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>


    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-info-circle me-2"></i>
                <span>{{__('auth.general_info')}}</span>
            </div>
            <div class="card-body row">
                <!-- Nama Paket -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.package_name')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-package"></i>
                        </span>
                        <input class="form-control" name="name" value="{{old('name',$package->name)}}" type="text" placeholder="Contoh: Paket Premium" required>
                    </div>
                </div>

                <!-- Opsi Masa Berlangganan -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.subscription_period_option')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-calendar"></i>
                        </span>
                        <select class="form-control dayoption" name="days_option">
                            <option value="limited" @if(old('days_option',$package->days_option)=='limited' ) selected @endif>{{__('auth.subscription_limited')}}</option>
                            <option value="unlimited" @if(old('days_option',$package->days_option)=='unlimited' ) selected @endif>{{__('auth.subscription_unlimited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Jumlah Hari -->
                <div class="col-lg-6 col-sm-12 mt-2 @if(old('days_option',$package->days_option) == 'unlimited') d-none @endif formdays">
                    <label class="form-label">{{__('auth.add_days')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-time"></i>
                        </span>
                        <input class="form-control" name="add_days" value="{{old('add_days',(int)$package->add_days)}}" type="number" placeholder="30" required>
                        <span class="input-group-text">{{__('auth.package_days')}}</span>
                    </div>
                </div>

                <!-- Storage -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.storage_mb')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hdd"></i>
                        </span>
                        <input class="form-control" name="storage" value="{{old('storage',$package->storage)}}" type="number" placeholder="1024" required>
                        <span class="input-group-text">MB</span>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.response_ai')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-bot"></i>
                        </span>
                        <input class="form-control" name="ai_response" value="{{old('ai_response',(int)$package->ai_response)}}" type="number" placeholder="1024" required>
                    </div>
                </div>

                <!-- Trial Version -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.trial_version')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-gift"></i>
                        </span>
                        <select class="form-control trialversion" name="trial_version">
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('trial_version',$package->trial_version) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('trial_version',$package->trial_version) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Harga -->
                <div class="col-lg-6 col-sm-12 mt-2 @if(old('trial_version',$package->trial_version) != 'no') d-none @endif formprice">
                    <label class="form-label">{{__('auth.package_price')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-money"></i>
                        </span>
                        <span class="input-group-text">Rp</span>
                        <input class="form-control" name="price" value="{{old('price',(int)$package->price)}}" type="number" placeholder="100000">
                    </div>
                </div>

                <!-- Integrasi Ongkir -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.shipping_integration')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-package"></i>
                        </span>
                        <select class="form-control" name="cek_ongkir">
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('cek_ongkir',$package->cek_ongkir)=='no' ) selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('cek_ongkir',$package->cek_ongkir)=='yes' ) selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Integrasi G-Sheet -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.gsheet_integration')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bxl-google"></i>
                        </span>
                        <select class="form-control" name="google_sheet">
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('google_sheet',$package->google_sheet)=='no' ) selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('google_sheet',$package->google_sheet)=='yes' ) selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">Mua Limit Option</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-user-circle"></i>
                        </span>
                        <select class="form-control" id="muaOption" name="mua_limit_optin">
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('mua_limit_optin',$package->mua_limit_optin)=='no' ) selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('mua_limit_optin',$package->mua_limit_optin)=='yes' ) selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-12 mt-2 muaOption d-none">
                    <label class="form-label">Mua ( monthly user active ) Limit <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-user-plus"></i>
                        </span>
                        <input class="form-control" name="mua_limit" value="{{old('mua_limit',(int)$package->mua_limit)}}" type="number" placeholder="1024" required>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 🔌 PLATFORM CONNECTIONS                                  -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <div class="col-12 mb-2">
        <div style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:10px 18px;border-radius:8px;font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;">
            <i class="bx bx-devices" style="font-size:16px;"></i>
            Platform & Connections
        </div>
    </div>


    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bxl-whatsapp me-2"></i>
                <span>{{__('auth.whatsapp_module')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit Device -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_device')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-devices"></i>
                        </span>
                        <select class="form-control limitdevice" name="limit_device" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_device',$package->limit_device)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_device',$package->limit_device)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Limit WhatsApp -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.send_wa_limit')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-message-dots"></i>
                        </span>
                        <select class="form-control limitwhatsapp" name="limit_whatsapp_option" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_whatsapp_option',$package->limit_whatsapp_option)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_whatsapp_option',$package->limit_whatsapp_option)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Device Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 @if(old('limit_device',$package->limit_device) != 'yes') d-none @endif formdevice">
                    <label class="form-label">{{__('auth.device_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-mobile"></i>
                        </span>
                        <input class="form-control" name="device_limit" value="{{old('device_limit',(int)$package->device_limit)}}" type="number" placeholder="5">
                        <span class="input-group-text">Device</span>
                    </div>
                </div>

                <!-- WhatsApp Period -->
                <div class="col-lg-6 col-sm-12 mt-2 formwhatsapp-priode @if(old('limit_whatsapp_option',$package->limit_whatsapp_option) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.limit_wa_period')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-calendar-alt"></i>
                        </span>
                        <select class="form-control" name="limit_whatsapp_priode">
                            <option value="daily" @if(old('limit_whatsapp_priode',$package->limit_whatsapp_priode)=='daily' ) selected @endif>{{__('auth.period_daily')}}</option>
                            <option value="monthly" @if(old('limit_whatsapp_priode',$package->limit_whatsapp_priode)=='monthly' ) selected @endif>{{__('auth.period_monthly')}}</option>
                            <option value="yearly" @if(old('limit_whatsapp_priode',$package->limit_whatsapp_priode)=='yearly' ) selected @endif>{{__('auth.period_yearly')}}</option>
                        </select>
                    </div>
                </div>

                <!-- WhatsApp Limit Count -->
                <div class="col-lg-6 col-sm-12 mt-2 formwhatsapp @if(old('limit_whatsapp_option',$package->limit_whatsapp_option) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.limit_send_wa')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-send"></i>
                        </span>
                        <input class="form-control" name="whatsapp_limit" value="{{old('whatsapp_limit',(int)$package->whatsapp_limit)}}" type="number" placeholder="1000">
                        <span class="input-group-text">Pesan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-badge-check me-2 text-success"></i>
                <span>WhatsApp Business API (WABA) Module</span>
            </div>
            <div class="card-body row">
                <!-- Limit Option -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">Limit Option <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-slider"></i>
                        </span>
                        <select class="form-control limitwaba" name="limit_waba" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_waba', $package->limit_waba)=='no') selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_waba', $package->limit_waba)=='yes') selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- WABA Account Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 formwaba @if(old('limit_waba', $package->limit_waba) != 'yes') d-none @endif">
                    <label class="form-label">WABA Account Limit</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="waba_limit" value="{{old('waba_limit', $package->waba_limit)}}" type="number" placeholder="5">
                        <span class="input-group-text">Akun</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bxl-telegram me-2"></i>
                <span>{{__('auth.telegram_module')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit Option -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_option')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-slider"></i>
                        </span>
                        <select class="form-control limittelegram" name="limit_telegram" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_telegram',$package->limit_telegram)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_telegram',$package->limit_telegram)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Telegram Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 formtelegram @if(old('limit_telegram',$package->limit_telegram) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.telegram_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="telegram" value="{{old('telegram',(int)$package->telegram)}}" type="number" placeholder="5">
                        <span class="input-group-text">Akun</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bxl-instagram me-2"></i>
                <span>{{__('auth.instagram_module')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit Option -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_option')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-slider"></i>
                        </span>
                        <select class="form-control limitinstagram" name="limit_instagram" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_instagram',$package->limit_instagram)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_instagram',$package->limit_instagram)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Instagram Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 forminstagram @if(old('limit_instagram',$package->limit_instagram) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.instagram_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="instagram" value="{{old('instagram',(int)$package->instagram)}}" type="number" placeholder="5">
                        <span class="input-group-text">Akun</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bxl-messenger me-2"></i>
                <span>{{__('auth.messenger_module')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit Option -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_option')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-slider"></i>
                        </span>
                        <select class="form-control limitmessanger" name="limit_messanger" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_messanger',$package->limit_messanger)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_messanger',$package->limit_messanger)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Messenger Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 formmessanger @if(old('limit_messanger',$package->limit_messanger) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.messenger_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="messanger" value="{{old('messanger',(int)$package->messanger)}}" type="number" placeholder="5">
                        <span class="input-group-text">Akun</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-chat me-2"></i>
                <span>{{__('auth.livechat_module')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit LiveChat -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_livechat_usage')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-conversation"></i>
                        </span>
                        <select class="form-control livechatlimit" name="livechat_limit" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('livechat_limit',$package->livechat_limit)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('livechat_limit',$package->livechat_limit)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- LiveChat Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 formlivechat @if(old('livechat_limit',$package->livechat_limit) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.livechat_usage_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="limit_livechat" value="{{old('limit_livechat',(int)$package->limit_livechat)}}" type="number" placeholder="3">
                        <span class="input-group-text">Platform</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- ⚙️ FEATURES & TOOLS                                       -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <div class="col-12 mb-2">
        <div style="background:linear-gradient(135deg,#11998e,#38ef7d);color:white;padding:10px 18px;border-radius:8px;font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;">
            <i class="bx bx-cog" style="font-size:16px;"></i>
            Features & Tools
        </div>
    </div>


    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-user me-2"></i>
                <span>{{__('auth.human_agent_access')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit User -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_user')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-user-check"></i>
                        </span>
                        <select class="form-control limituser" name="limit_user_option" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_user_option',$package->limit_user_option)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_user_option',$package->limit_user_option)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- User Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 formlimituser @if(old('limit_user_option',$package->limit_user_option) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.user_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-group"></i>
                        </span>
                        <input class="form-control" name="users_limit" value="{{old('users_limit',(int)$package->users_limit)}}" type="number" placeholder="10">
                        <span class="input-group-text">{{__('auth.feature_users')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-message-square-detail me-2"></i>
                <span>{{__('auth.template_module')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit Template -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_template')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-file"></i>
                        </span>
                        <select class="form-control templatelimit" name="limit_template" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_template',$package->limit_template)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_template',$package->limit_template)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Template Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 formtemplate @if(old('limit_template',$package->limit_template) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.template_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="template_limit" value="{{old('template_limit',(int)$package->template_limit)}}" type="number" placeholder="50">
                        <span class="input-group-text">Template</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-message-rounded-dots me-2"></i>
                <span>{{__('auth.chatbot_module')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit ChatBot -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_chatbot')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-bot"></i>
                        </span>
                        <select class="form-control chatbotlimit" name="limit_chatbot" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_chatbot',$package->limit_chatbot)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_chatbot',$package->limit_chatbot)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- ChatBot Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 formchatbot @if(old('limit_chatbot',$package->limit_chatbot) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.chatbot_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="chatbot_limit" value="{{old('chatbot_limit',(int)$package->chatbot_limit)}}" type="number" placeholder="5">
                        <span class="input-group-text">ChatBot</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-brain me-2"></i>
                <span>{{__('auth.ai_training_module')}}</span>
            </div>
            <div class="card-body row">

                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">Maximal Upload Per File</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="max_per_upload" value="{{old('max_per_upload',1)}}" type="number" placeholder="10">
                        <span class="input-group-text">Mb</span>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">Maximal Upload File</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="max_total_rag" value="{{old('max_total_rag',1)}}" type="number" placeholder="10">
                        <span class="input-group-text">Mb</span>
                    </div>
                </div>

                <!-- Limit AI Training -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_ai_training')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-bot"></i>
                        </span>
                        <select class="form-control ailimit" name="limit_ai_training" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_ai_training',$package->limit_ai_training)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_ai_training',$package->limit_ai_training)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- AI Training Limit -->
                <div class="col-lg-6 col-sm-12 mt-2 formai @if(old('limit_ai_training',$package->limit_ai_training) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.ai_training_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-hash"></i>
                        </span>
                        <input class="form-control" name="ai_training_limit" value="{{old('ai_training_limit',(int)$package->ai_training_limit)}}" type="number" placeholder="10">
                        <span class="input-group-text">Training</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 📢 BROADCAST & DATA                                       -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <div class="col-12 mb-2">
        <div style="background:linear-gradient(135deg,#f093fb,#f5576c);color:white;padding:10px 18px;border-radius:8px;font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;">
            <i class="bx bx-broadcast" style="font-size:16px;"></i>
            Broadcast & Data
        </div>
    </div>


    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-envelope me-2"></i>
                <span>{{__('auth.email_module')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit Email -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_email')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-mail-send"></i>
                        </span>
                        <select class="form-control emaillimit" name="limit_email_option" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_email_option',$package->limit_email_option)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_email_option',$package->limit_email_option)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Email Period -->
                <div class="col-lg-6 col-sm-12 mt-2 formemail-priode @if(old('limit_email_option',$package->limit_email_option) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.limit_email_period')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-calendar-alt"></i>
                        </span>
                        <select class="form-control" name="limit_email_priode">
                            <option value="daily" @if(old('limit_email_priode',$package->limit_email_priode)=='daily' ) selected @endif>{{__('auth.period_daily')}}</option>
                            <option value="monthly" @if(old('limit_email_priode',$package->limit_email_priode)=='monthly' ) selected @endif>{{__('auth.period_monthly')}}</option>
                            <option value="yearly" @if(old('limit_email_priode',$package->limit_email_priode)=='yearly' ) selected @endif>{{__('auth.period_yearly')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Email Limit Count -->
                <div class="col-lg-6 col-sm-12 mt-2 formemail @if(old('limit_email_option',$package->limit_email_option) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.email_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-send"></i>
                        </span>
                        <input class="form-control" name="email_limit" value="{{old('email_limit',(int)$package->email_limit)}}" type="number" placeholder="500">
                        <span class="input-group-text">Email</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bx bx-data me-2"></i>
                <span>{{__('auth.scraping_module')}}</span>
            </div>
            <div class="card-body row">
                <!-- Limit Scraping -->
                <div class="col-lg-6 col-sm-12 mt-2">
                    <label class="form-label">{{__('auth.limit_scraping')}} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-code-alt"></i>
                        </span>
                        <select class="form-control scrappinglimit" name="limit_scrapp_option" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="no" @if(old('limit_scrapp_option',$package->limit_scrapp_option)=='no' ) selected @endif>{{__('auth.unlimited')}}</option>
                            <option value="yes" @if(old('limit_scrapp_option',$package->limit_scrapp_option)=='yes' ) selected @endif>{{__('auth.limited')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Scraping Period -->
                <div class="col-lg-6 col-sm-12 mt-2 formscrapping-priode @if(old('limit_scrapp_option',$package->limit_scrapp_option) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.limit_scraping_period')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-calendar-alt"></i>
                        </span>
                        <select class="form-control" name="limit_scrapp_priode">
                            <option value="daily" @if(old('limit_scrapp_priode',$package->limit_scrapp_priode)=='daily' ) selected @endif>{{__('auth.period_daily')}}</option>
                            <option value="monthly" @if(old('limit_scrapp_priode',$package->limit_scrapp_priode)=='monthly' ) selected @endif>{{__('auth.period_monthly')}}</option>
                            <option value="yearly" @if(old('limit_scrapp_priode',$package->limit_scrapp_priode)=='yearly' ) selected @endif>{{__('auth.period_yearly')}}</option>
                        </select>
                    </div>
                </div>

                <!-- Scraping Limit Count -->
                <div class="col-lg-6 col-sm-12 mt-2 formscrapping @if(old('limit_scrapp_option',$package->limit_scrapp_option) != 'yes') d-none @endif">
                    <label class="form-label">{{__('auth.scraping_limit')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bx bx-download"></i>
                        </span>
                        <input class="form-control" name="scrapp_limit" value="{{old('scrapp_limit',(int)$package->scrapp_limit)}}" type="number" placeholder="100">
                        <span class="input-group-text">Data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUBMIT BUTTON -->
    <div class="col-12">
        <button type="submit" class="btn btn-primary w-100 btn-lg">
            <i class="bx bx-save me-2"></i>
            {{__('auth.update_package_data')}}
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Trial Version Handler
    $(".trialversion").on("change", function() {
        if ($(this).val() == 'no') {
            $(".formprice").removeClass('d-none');
        } else {
            $(".formprice").addClass('d-none');
        }
    });

    // User Limit Handler
    $(".limituser").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formlimituser").removeClass('d-none');
        } else {
            $(".formlimituser").addClass('d-none');
        }
    });

    // Device Limit Handler
    $(".limitdevice").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formdevice").removeClass('d-none');
        } else {
            $(".formdevice").addClass('d-none');
        }
    });

    // WhatsApp Limit Handler
    $(".limitwhatsapp").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formwhatsapp-priode").removeClass('d-none');
            $(".formwhatsapp").removeClass('d-none');
        } else {
            $(".formwhatsapp-priode").addClass('d-none');
            $(".formwhatsapp").addClass('d-none');
        }
    });

    // Scraping Limit Handler
    $(".scrappinglimit").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formscrapping-priode").removeClass('d-none');
            $(".formscrapping").removeClass('d-none');
        } else {
            $(".formscrapping-priode").addClass('d-none');
            $(".formscrapping").addClass('d-none');
        }
    });

    // Email Limit Handler
    $(".emaillimit").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formemail-priode").removeClass('d-none');
            $(".formemail").removeClass('d-none');
        } else {
            $(".formemail-priode").addClass('d-none');
            $(".formemail").addClass('d-none');
        }
    });

    // Template Limit Handler
    $(".templatelimit").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formtemplate").removeClass('d-none');
        } else {
            $(".formtemplate").addClass('d-none');
        }
    });

    // AI Training Limit Handler
    $(".ailimit").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formai").removeClass('d-none');
        } else {
            $(".formai").addClass('d-none');
        }
    });

    // ChatBot Limit Handler
    $(".chatbotlimit").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formchatbot").removeClass('d-none');
        } else {
            $(".formchatbot").addClass('d-none');
        }
    });

    // Day Option Handler
    $(".dayoption").on("change", function() {
        if ($(this).val() == 'limited') {
            $(".formdays").removeClass('d-none');
        } else {
            $(".formdays").addClass('d-none');
        }
    });

    // Instagram Limit Handler
    $(".limitinstagram").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".forminstagram").removeClass('d-none');
        } else {
            $(".forminstagram").addClass('d-none');
        }
    });


    // WABA Limit Handler
    $(".limitwaba").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formwaba").removeClass('d-none');
        } else {
            $(".formwaba").addClass('d-none');
        }
    });

    // Messenger Limit Handler
    $(".limitmessanger").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formmessanger").removeClass('d-none');
        } else {
            $(".formmessanger").addClass('d-none');
        }
    });

    // Telegram Limit Handler
    $(".limittelegram").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".formtelegram").removeClass('d-none');
        } else {
            $(".formtelegram").addClass('d-none');
        }
    });

    // LiveChat Limit Handler (ditambahkan karena kurang di script sebelumnya)
    $(".livechatlimit").on("change", function() {
        if ($(this).val() == 'no') {
            $(".formlivechat").addClass('d-none');
        } else {
            $(".formlivechat").removeClass('d-none');
        }
    });

    $("#muaOption").on("change", function() {
        if ($(this).val() == 'limited') {
            $(".muaOption").removeClass('d-none');
        } else {
            $(".muaOption").addClass('d-none');
        }
    });
</script>
@endsection