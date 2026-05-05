@extends('layouts.admin')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('content')
<!-- Start::app-content -->
<form action="<?= route('admin.setting.change'); ?>" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-validation-component></x-validation-component>
        </div>
        <div class="col-12">
            <div class="card" style="position: relative; z-index: 9;">
                <div class="card-body p-0">
                    <div class="border-block-end-dashed  rounded-2 p-2">
                        <div>
                            <ul class="nav nav-pills nav-justified gx-3 tab-style-6 d-sm-flex d-block " id="myTab" role="tablist">
                                <li class="nav-item rounded" role="presentation">
                                    <a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#general-information" aria-controls="general-information" aria-selected="true">General</a>
                                </li>
                                <li class="nav-item rounded" role="presentation">
                                    <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#follow-ai" aria-controls="follow-ups" aria-selected="true"><i class="bx bx-clock me-1"></i> Ai</a>
                                </li>
                                <li class="nav-item rounded" role="presentation">
                                    <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#follow-gmaps" aria-controls="follow-ups" aria-selected="true"><i class="bx bx-clock me-1"></i> Google Maps</a>
                                </li>
                                <li class="nav-item rounded" role="presentation">
                                    <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#follow-whatsapp" aria-controls="follow-ups" aria-selected="true"><i class="bx bx-clock me-1"></i> Whatsapp</a>
                                </li>
                                <li class="nav-item rounded" role="presentation">
                                    <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#follow-smtp" aria-controls="follow-ups" aria-selected="true"><i class="bx bx-clock me-1"></i> SMTP</a>
                                </li>
                                <li class="nav-item rounded" role="presentation">
                                    <a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#payment" aria-controls="payment" aria-selected="true"><i class="bx bx-clock me-1"></i> Integration</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="py-2">
                        <div class="tab-content " id="myTabContent">
                            <div class="tab-pane fade active show" id="general-information" role="tabpanel" style="border: none;">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        <label class="form-label">{{__('master.configuration.timezone')}}</label>
                                        <select class="form-control timezone" name="timezone">
                                            <option value="">{{__('master.configuration.choose_timezone')}}</option>
                                            @foreach (timezone() as $t => $timezone)
                                            <option value="<?= $timezone; ?>" @if($timezone==$setting->timezone) selected @endif >{{$timezone}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('master.configuration.country_phone_code')}}</label>
                                        <select class="form-control phonecode" name="phone_country_code">
                                            <option value="">{{__('master.configuration.choose_country_phone_code')}}</option>
                                            @foreach(country_codes() as $country => $value)
                                            <option value="{{$country}}" @if($setting->phone_country_code == $country) selected @endif>{{$value}}</option> 
                                            @endforeach 
                                        </select>
                                    </div>

                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('master.configuration.default_lang')}}</label>
                                        <select class="form-control lang" name="default_lang">
                                            <option value="">{{__('master.configuration.choose_lang')}}</option>
                                            <option value="id" @if('id'==$setting->default_lang) selected @endif>{{__('sidebar.indonesia')}} </option>
                                            <option value="en" @if('en'==$setting->default_lang) selected @endif>{{__('sidebar.english')}}</option>
                                            <option value="ja" @if('ja'==$setting->default_lang) selected @endif>{{__('sidebar.japan')}}</option>
                                            <option value="ar" @if('ar'==$setting->default_lang) selected @endif>{{__('sidebar.arab')}}</option>
                                            <option value="nl" @if('nl'==$setting->default_lang) selected @endif>{{__('sidebar.dutch')}}</option>
                                            <option value="pt" @if('pt'==$setting->default_lang) selected @endif>{{__('sidebar.portugal')}}</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.local_api_key')}}</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control apikeylocal" readonly value="<?= $setting->local_api_key; ?>">
                                            <button class="btn btn-primary" type="button" id="generateApiKey">
                                                <i class="bx bx-refresh text-white"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.device_id_api_usage')}}</label>
                                        <select class="form-control lang" name="api_device_use" required>
                                            <option value="">{{__('general.choose')}}</option>
                                            <option value="required" @if('required'==$setting->api_device_use) selected @endif>{{__('setting.must_include')}} </option>
                                            <option value="optional" @if('optional'==$setting->api_device_use) selected @endif>{{__('general.optional')}}</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('master.configuration.option_signature')}}</label>
                                        <select class="form-control" name="signature_option" required>
                                            <option value="none">{{__('master.configuration.dont_use')}}</option>
                                            <option value="by_login" @if('by_login'==$setting->signature_option) selected @endif>{{__('master.configuration.based_on_login_user')}}</option>
                                            <option value="by_device" @if('by_device'==$setting->signature_option) selected @endif>{{__('master.configuration.based_on_device_name')}}</option>
                                            <option value="custom" @if('custom'==$setting->signature_option) selected @endif>{{__('master.configuration.custom_signature')}}</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label">{{__('master.configuration.custom_signature')}}</label>
                                        <textarea class="form-control" name="signature_text">{{$setting->signature_text}}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="follow-ai" role="tabpanel" style="border: none;">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.use_of_ai')}}</label>
                                        <select class="form-control lang" name="ai_option">
                                            <option value="">{{__('general.choose')}}</option>
                                            <option value="chatgpt" @if('chatgpt'==$setting->ai_option) selected @endif>OpenAi </option>
                                            <option value="gemini" @if('gemini'==$setting->ai_option) selected @endif>Gemini</option>
                                            <option value="chatpdf" @if('chatpdf'==$setting->ai_option) selected @endif>ChatPDF</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.api_key_ai')}}</label>
                                        <input class="form-control" name="open_ai_key" value="<?= $setting->open_ai_key; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.google_text_audio')}}</label>
                                        <input class="form-control" name="google_text_to_audio" value="<?= $setting->google_text_to_audio; ?>" type="text">
                                        <small>{{__('setting.text_to_audio_desc')}}</small>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.ai_memory_option')}}</label>
                                        <select class="form-control historychat" name="history_ai_chat_option">
                                            <option value="no" @if(old('history_ai_chat_option',$setting->history_ai_chat_option) == 'no') selected @endif>{{__('general.no')}} </option>
                                            <option value="yes" @if(old('history_ai_chat_option',$setting->history_ai_chat_option) == 'yes') selected @endif>{{__('general.yes')}} </option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3 forgethistory @if(old('history_ai_chat_option',$setting->history_ai_chat_option) == 'no') d-none @endif ">
                                        <label class="form-label">{{__('setting.reset_memory')}}</label>
                                        <input class="form-control" name="history_ai_chat" value="<?= $setting->history_ai_chat; ?>" type="number">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.token_price')}}</label>
                                        <input class="form-control" name="price_token" value="<?= (int)$internal->price_token; ?>" type="number">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.credit_per_token')}}</label>
                                        <input class="form-control" name="token_per_price" value="<?= (int)$internal->token_per_price; ?>" type="number">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.basic_credit')}}</label>
                                        <input class="form-control" name="credit_token_basic" value="<?= (int)$internal->credit_token_basic; ?>" type="number">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('setting.advance_credit')}}</label>
                                        <input class="form-control" name="credit_token_advance" value="<?= (int)$internal->credit_token_advance; ?>" type="number">
                                    </div>

                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">MUA ( Monthly User Active ) Per Addons Price</label>
                                        <input class="form-control" name="mua_per_price" value="<?= (int)$internal->mua_per_price; ?>" type="number">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">MUA ( Monthly User Active ) Per Addons</label>
                                        <input class="form-control" name="price_mua" value="<?= (int)$internal->price_mua; ?>" type="number">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="follow-gmaps" role="tabpanel" style="border: none;">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('master.configuration.google_map_api_key')}}</label>
                                        <input class="form-control" name="gmap_key" value="<?= $setting->gmap_key; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('master.configuration.phone_scrapp')}}</label>
                                        <select class="form-control" name="scrapp_phone">
                                            <option value="protect_double" @if($setting->scrapp_phone == 'protect_double') selected @endif >{{__('general.no')}}</option>
                                            <option value="no_protect" @if($setting->scrapp_phone == 'no_protect') selected @endif>{{__('general.yes')}}</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{__('master.configuration.just_scrapp_whatsapp')}}</label>
                                        <select class="form-control" name="scrapp_phone_whatsapp">
                                            <option value="must_whatsapp" @if($setting->scrapp_phone == 'must_whatsapp') selected @endif >{{__('master.configuration.yes_scrapp')}}</option>
                                            <option value="all" @if($setting->scrapp_phone == 'all') selected @endif>{{__('master.configuration.no_scrapp')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="follow-whatsapp" role="tabpanel" style="border: none;">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        <label class="form-label">{{__('master.configuration.method_sent')}}</label>
                                        <select class="form-control" name="whatsapp_sender_notif">
                                            <option value="sequence" @if($setting->whatsapp_sender_notif == 'sequence') selected @endif >{{__('master.configuration.sequence')}}</option>
                                            <option value="spin" @if($setting->whatsapp_sender_notif == 'spin') selected @endif>Spin</option>
                                            <option value="random" @if($setting->whatsapp_sender_notif == 'random') selected @endif>Random</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="follow-smtp" role="tabpanel" style="border: none;">
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <label class="form-label">Mail Encrypt</label>
                                        <input class="form-control" name="mail_encryption" value="<?= $setting->mail_encryption; ?>" type="text">
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <label class="form-label">Mail Host</label>
                                        <input class="form-control" name="mail_host" value="<?= $setting->mail_host; ?>" type="text">
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <label class="form-label">Mail Port</label>
                                        <input class="form-control" name="mail_port" value="<?= $setting->mail_port; ?>" type="text">
                                    </div>

                                    <div class="col-lg-4 col-sm-12 mt-3">
                                        <label class="form-label">Mail Username</label>
                                        <input class="form-control" name="mail_username" value="<?= $setting->mail_username; ?>" type="text">
                                    </div>
                                    <div class="col-lg-4 col-sm-12 mt-3">
                                        <label class="form-label">Mail Password</label>
                                        <input class="form-control" name="mail_password" value="<?= $setting->mail_password; ?>" type="text">
                                    </div>
                                    <div class="col-lg-4 col-sm-12 mt-3">
                                        <label class="form-label">Mail From Address</label>
                                        <input class="form-control" name="mail_from_address" value="<?= $setting->mail_from_address; ?>" type="text">
                                    </div>
                                    <div class="col-lg-4 col-sm-12 mt-3">
                                        <label class="form-label">Mail From Name</label>
                                        <input class="form-control" name="mail_from_name" value="<?= $setting->mail_from_name; ?>" type="text">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="payment" role="tabpanel" style="border: none;">
                                <div class="row">
                                    <div class="col-12">
                                        <h5>{{ __('setting.payment_methods_a') }}</h5>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{ __('setting.payment_method') }}</label>
                                        <select class="form-control" name="method" required>
                                            <option value="bank" @if(old('method',$internal->method) == 'bank') selected @endif>Transfer Bank ( Manual) </option>
                                            <option value="duitku" @if(old('method',$internal->method) == 'duitku') selected @endif>Duitku ( PG) </option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{ __('setting.merchant_code') }}</label>
                                        <input class="form-control" name="merchant_code" value="<?= $internal->merchant_code; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Api Key</label>
                                        <input class="form-control" name="api_key" value="<?= $internal->api_key; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Production Status</label>
                                        <select class="form-control" name="is_production" required>
                                            <option value="yes" @if(old('is_production',$internal->is_production) == 'yes') selected @endif>{{ __('setting.yes') }}</option>
                                            <option value="no" @if(old('is_production',$internal->is_production) == 'no') selected @endif>{{ __('setting.no') }} ( Sandbox ) </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5>{{ __('setting.shipping_cost_b') }}</h5>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{ __('setting.shipping_api_option') }}</label>
                                        <select class="form-control" name="cek_ongkir_option_api" required>
                                            <option value="sistem" @if(old('cek_ongkir_option_api',$setting->cek_ongkir_option_api) == 'sistem') selected @endif>{{ __('setting.from_admin') }}</option>
                                            <option value="self" @if(old('cek_ongkir_option_api',$setting->cek_ongkir_option_api) == 'self') selected @endif>{{ __('setting.own_api') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">{{ __('setting.shipping_api_used') }}</label>
                                        <select class="form-control" name="ongkir_method" required>
                                            <option value="rajaongkir" @if(old('ongkir_method',$setting->ongkir_method) == 'rajaongkir') selected @endif>Rajaongkir </option>
                                            <option value="biteship" @if(old('ongkir_method',$setting->ongkir_method) == 'biteship') selected @endif>BiteShip </option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Api Ongkir</label>
                                        <input class="form-control" name="cek_ongkir_api" value="<?= $setting->cek_ongkir_api; ?>" type="text">
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5>C. Facebook Oauth</h5>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Facebook App ID</label>
                                        <input class="form-control" name="fb_app_id" value="<?= $internal->fb_app_id; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Facebook App Secret</label>
                                        <input class="form-control" name="fb_app_secret" value="<?= $internal->fb_app_secret; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Facebook Config ID</label>
                                        <input class="form-control" name="fb_config_id" value="<?= $internal->fb_config_id; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Instagram Config ID</label>
                                        <input class="form-control" name="instagram_config_id" value="<?= $internal->instagram_config_id; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Messanger Config ID</label>
                                        <input class="form-control" name="messanger_config_id" value="<?= $internal->messanger_config_id; ?>" type="text">
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5>D. Google Client Oauth</h5>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Google Client ID</label>
                                        <input class="form-control" name="google_client_id" value="<?= $internal->google_client_id; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Google Client Secret</label>
                                        <input class="form-control" name="google_client_secret" value="<?= $internal->google_client_secret; ?>" type="text">
                                    </div>
                                    <div class="col-lg-6 col-sm-12 mt-3">
                                        <label class="form-label">Google Redirect </label>
                                        <input class="form-control" name="google_redirect" value="<?= $internal->google_redirect; ?>" type="text">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end p-3">
                    <button class="btn btn-primary" type="submit">{{ __('setting.save_changes') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- End::app-content -->

@section('scripts')

<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.timezone').select2();
        $('.phonecode').select2();
        $(".lang").select2();
    });

    $(".historychat").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".forgethistory").removeClass('d-none')
        } else {
            $(".forgethistory").addClass('d-none');
        }
    })

    $("#generateApiKey").on("click", function() {

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            type: "POST",
            url: "/administrator/settings/generate-api-local",
            success: function(response) {
                $(".apikeylocal").val(response.message)
            },
            error: function(xhr, status, error) {

            },
        });
    });
</script>
@endsection

@endsection