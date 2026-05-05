@extends('layouts.starter')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/select2/select2.css')}}" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{route('starter.business.store')}}" method="POST" class="card custom-card mb-4">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-briefcase-alt me-2"></i>
                    {{ __('starter.create_business') }}
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <x-validation-component></x-validation-component>
                    </div>

                    <!-- Section: Informasi Bisnis -->
                    <div class="col-12 mb-4">
                        <h6 class="text-muted mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            {{ __('starter.business_info') }}
                        </h6>
                    </div>

                    <!-- Nama Bisnis -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-briefcase text-primary me-1"></i>
                            {{ __('starter.business_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-buildings"></i>
                            </span>
                            <input class="form-control"
                                name="name"
                                value="{{old('name')}}"
                                required
                                type="text"
                                placeholder="{{ __('starter.business_example') }}">
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('starter.business_desc') }}
                        </small>
                    </div>

                    <!-- Timezone -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-time text-success me-1"></i>
                            {{__('master.configuration.timezone')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control timezone" name="timezone" required>
                            <option value="">{{__('master.configuration.choose_timezone')}}</option>
                            @foreach (timezone() as $t => $timezone)
                            <option value="<?= $timezone; ?>" @if($timezone==old('timezone')) selected @endif>{{$timezone}}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('starter.timezone_desc') }}
                        </small>
                    </div>

                    <!-- Phone Country Code -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-phone text-info me-1"></i>
                            {{__('master.configuration.country_phone_code')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control phonecode" name="phone_country_code" required>
                            <option value="">{{__('master.configuration.choose_country_phone_code')}}</option>
                            @foreach(country_codes() as $country => $value)
                            <option value="{{$country}}" @if(old('phone_country_code')==$country) selected @endif>{{$value}}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('starter.phone_code_desc') }}
                        </small>
                    </div>

                    <!-- Default Language -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-world text-warning me-1"></i>
                            {{__('master.configuration.default_lang')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control lang" name="default_lang" required>
                            <option value="">{{__('master.configuration.choose_lang')}}</option>
                            <option value="id" @if('id'==old('default_lang')) selected @endif>{{__('sidebar.indonesia')}}</option>
                            <option value="en" @if('en'==old('default_lang')) selected @endif>{{__('sidebar.english')}}</option>
                            <option value="hi" @if('hi'==old('default_lang')) selected @endif>{{__('sidebar.india')}}</option>
                            <option value="pt" @if('pt'==old('default_lang')) selected @endif>{{__('sidebar.portugal')}}</option>
                            <option value="es" @if('es'==old('default_lang')) selected @endif>{{__('sidebar.spanish')}}</option>
                            <option value="de" @if('de'==old('default_lang')) selected @endif>{{__('sidebar.german')}}</option>
                            <option value="ar" @if('ar'==old('default_lang')) selected @endif>{{__('sidebar.arab')}}</option>
                            <option value="ja" @if('ja'==old('default_lang')) selected @endif>{{__('sidebar.japan')}}</option>
                            <option value="nl" @if('nl'==old('default_lang')) selected @endif>{{__('sidebar.dutch')}}</option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('starter.default_lang_desc') }}
                        </small>
                    </div>

                    <div class="col-12 my-3">
                        <hr>
                    </div>

                    <!-- Section: Konfigurasi API & Pengiriman -->
                    <div class="col-12 mb-4">
                        <h6 class="text-muted mb-3">
                            <i class="bx bx-cog me-1"></i>
                            {{ __('starter.api_config') }}
                        </h6>
                    </div>

                    <!-- API Device Use -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-devices text-primary me-1"></i>
                            {{__('setting.device_id_api_usage')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="api_device_use" required>
                            <option value="">{{__('general.choose')}}</option>
                            <option value="required" @if(old('api_device_use')=='required' ) selected @endif>
                                {{__('setting.must_include')}}
                            </option>
                            <option value="optional" @if(old('api_device_use', 'optional' )=='optional' ) selected @endif>
                                {{__('general.optional')}}
                            </option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('starter.device_id_desc') }}
                        </small>
                    </div>

                    <!-- Method Sent -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-shuffle text-success me-1"></i>
                            {{__('master.configuration.method_sent')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="whatsapp_sender_notif" required>
                            <option value="sequence" @if(old('whatsapp_sender_notif')=='sequence' ) selected @endif>
                                {{__('master.configuration.sequence')}}
                            </option>
                            <option value="spin" @if(old('whatsapp_sender_notif')=='spin' ) selected @endif>
                                Spin
                            </option>
                            <option value="random" @if(old('whatsapp_sender_notif')=='random' || is_null(old('whatsapp_sender_notif'))) selected @endif>
                                Random
                            </option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('starter.sending_method_desc') }}
                        </small>
                    </div>

                    <div class="col-12 my-3">
                        <hr>
                    </div>

                    <!-- Section: Konfigurasi Scraping -->
                    <div class="col-12 mb-4">
                        <h6 class="text-muted mb-3">
                            <i class="bx bx-search-alt me-1"></i>
                            {{ __('starter.scraping_config') }}
                        </h6>
                    </div>

                    <!-- Phone Scrapp -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-shield text-warning me-1"></i>
                            {{__('master.configuration.phone_scrapp')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="scrapp_phone" required>
                            <option value="protect_double" @if(old('scrapp_phone')=='protect_double' ) selected @endif>
                                {{__('general.no')}}
                            </option>
                            <option value="no_protect" @if(old('scrapp_phone')=='no_protect' ) selected @endif>
                                {{__('general.yes')}}
                            </option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('starter.allow_duplicate_scraping') }}
                        </small>
                    </div>

                    <!-- Just Scrapp WhatsApp -->
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bx bxl-whatsapp text-success me-1"></i>
                            {{__('master.configuration.just_scrapp_whatsapp')}}
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="scrapp_phone_whatsapp" required>
                            <option value="must_whatsapp" @if(old('scrapp_phone_whatsapp')=='must_whatsapp' ) selected @endif>
                                {{__('master.configuration.yes_scrapp')}}
                            </option>
                            <option value="all" @if(old('scrapp_phone_whatsapp')=='all' ) selected @endif>
                                {{__('master.configuration.no_scrapp')}}
                            </option>
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('starter.whatsapp_only_scraping') }}
                        </small>
                    </div>

                    <div class="col-12 my-3">
                        <hr>
                    </div>


                    <!-- Reset History Days -->
                    <div class="col-lg-6 mb-4 forgethistory @if(old('history_ai_chat_option') != 'yes') d-none @endif">
                        <label class="form-label fw-semibold">
                            <i class="bx bx-reset text-warning me-1"></i>
                            {{ __('starter.reset_history_days') }}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-calendar"></i>
                            </span>
                            <input class="form-control"
                                name="history_ai_chat"
                                value="{{old('history_ai_chat')}}"
                                type="number"
                                min="1"
                                placeholder="30">
                            <span class="input-group-text">{{ __('starter.days') }}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle"></i>
                            {{ __('starter.auto_reset_history') }}
                        </small>
                    </div>

                </div>

                <!-- Info Alert -->
                <div class="alert alert-info d-flex align-items-start mt-4" role="alert">
                    <i class="bx bx-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>{{ __('starter.important_info') }}</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>{{ __('starter.config_can_change') }}</li>
                            <li>{{ __('starter.timezone_warning') }}</li>
                            <li>{{ __('starter.scraping_quality') }}</li>
                            <li>{{ __('starter.ai_history_benefit') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    <span class="text-danger">*</span> {{ __('starter.required_field') }}
                </small>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i>
                    {{__('general.add_data')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.timezone').select2({
            placeholder: '{{__("master.configuration.choose_timezone")}}'
        });

        $('.phonecode').select2({
            placeholder: '{{__("master.configuration.choose_country_phone_code")}}'
        });

        $(".lang").select2({
            placeholder: '{{__("master.configuration.choose_lang")}}'
        });

        // Trigger history chat visibility on page load
        if ($(".historychat").val() == 'yes') {
            $(".forgethistory").removeClass('d-none');
        }
    });

    // Toggle history reset days visibility
    $(".historychat").on("change", function() {
        if ($(this).val() == 'yes') {
            $(".forgethistory").removeClass('d-none');
        } else {
            $(".forgethistory").addClass('d-none');
        }
    });
</script>
@endsection