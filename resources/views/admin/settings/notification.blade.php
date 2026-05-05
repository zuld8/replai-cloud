@extends('layouts.admin')

@section('content')
<!-- Start::app-content -->
<div class="row">
    <div class="col-lg-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('notification.settings.store'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    {{$page}}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('setting.choose_device')}}</label>
                        <select class="form-control" name="device">
                            <option value="">{{__('chatbot.choose_device')}}</option>
                            @foreach ($devices as $device)
                            <option value="{{ $device->id }}" @if(old('device', $setting->device_notification) == $device->id) selected @endif>{{ $device->name }} - {{$device->phone}} </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- WABA Device for Notifications --}}
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label fw-semibold">
                            <i class="bx bxl-whatsapp me-1" style="color:#25d366;"></i>
                            WhatsApp Business API (WABA)
                            <span class="badge bg-primary ms-1" style="font-size:0.7rem;">Recommended</span>
                        </label>
                        <select class="form-control" name="waba_device">
                            <option value="">— Tidak menggunakan WABA —</option>
                            @foreach ($wabaDevices as $wdev)
                            @php
                                $wMeta = json_decode($wdev->meta_data, true);
                                $wName = $wMeta['whatsapp']['verified_name'] ?? $wdev->phone;
                                $wStatus = $wMeta['whatsapp']['status'] ?? 'UNKNOWN';
                            @endphp
                            <option value="{{ $wdev->id }}" @if(old('waba_device', $setting->waba_device_notification) == $wdev->id) selected @endif>
                                {{ $wName }} — {{ $wdev->phone }}
                                @if($wStatus === 'CONNECTED') ✅ @else ({{ $wStatus }}) @endif
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Jika diset, notifikasi WA dikirim via API resmi Meta (tidak masuk CRM).</small>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.receive_phone')}}</label>
                        <input class="form-control" name="received_notification" value="{{old('received_notification',$setting->received_notification)}}" type="number">
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.receive_email')}}</label>
                        <input class="form-control" name="received_email_notification" value="{{old('received_email_notification',$setting->received_email_notification)}}" type="email">
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.register_notif')}}</label>
                        <select class="form-control" name="whatsapp_register" required>
                            <option value="no" @if(old('whatsapp_register', $setting->whatsapp_register) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('whatsapp_register', $setting->whatsapp_register) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.register_template')}}</label>
                        <select class="form-control" name="whatsapp_register_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($watemplates as $template)
                            <option value="{{ $template->id }}" @if(old('whatsapp_register_template', $setting->whatsapp_register_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.purchase_notif')}}</label>
                        <select class="form-control" name="whatsapp_buy_package" required>
                            <option value="no" @if(old('whatsapp_buy_package', $setting->whatsapp_buy_package) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('whatsapp_buy_package', $setting->whatsapp_buy_package) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.purchase_template')}}</label>
                        <select class="form-control" name="whatsapp_buy_package_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($watemplates as $template)
                            <option value="{{ $template->id }}" @if(old('whatsapp_buy_package_template', $setting->whatsapp_buy_package_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.package_payment')}}</label>
                        <select class="form-control" name="whatsapp_package_payment" required>
                            <option value="no" @if(old('whatsapp_package_payment', $setting->whatsapp_package_payment) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('whatsapp_package_payment', $setting->whatsapp_package_payment) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.payment_template')}}</label>
                        <select class="form-control" name="whatsapp_package_payment_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($watemplates as $template)
                            <option value="{{ $template->id }}" @if(old('whatsapp_package_payment_template', $setting->whatsapp_package_payment_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.user_notification')}}</label>
                        <select class="form-control" name="whatsapp_package_user" required>
                            <option value="no" @if(old('whatsapp_package_user', $setting->whatsapp_package_user) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('whatsapp_package_user', $setting->whatsapp_package_user) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.user_template')}}</label>
                        <select class="form-control" name="whatsapp_package_user_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($watemplates as $template)
                            <option value="{{ $template->id }}" @if(old('whatsapp_package_user_template', $setting->whatsapp_package_user_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.payment_approval')}}</label>
                        <select class="form-control" name="whatsapp_approval_payment" required>
                            <option value="no" @if(old('whatsapp_approval_payment', $setting->whatsapp_approval_payment) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('whatsapp_approval_payment', $setting->whatsapp_approval_payment) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.approval_template')}}</label>
                        <select class="form-control" name="whatsapp_approval_payment_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($watemplates as $template)
                            <option value="{{ $template->id }}" @if(old('whatsapp_approval_payment_template', $setting->whatsapp_approval_payment_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.register_notif_mail')}}</label>
                        <select class="form-control" name="email_register" required>
                            <option value="no" @if(old('email_register', $setting->email_register) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('email_register', $setting->email_register) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.register_template_mail')}}</label>
                        <select class="form-control" name="email_register_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($mailtemplates as $template)
                            <option value="{{ $template->id }}" @if(old('email_register_template', $setting->email_register_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.purchase_notif_mail')}}</label>
                        <select class="form-control" name="email_buy_package" required>
                            <option value="no" @if(old('email_buy_package', $setting->email_buy_package) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('email_buy_package', $setting->email_buy_package) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.purchase_template_mail')}}</label>
                        <select class="form-control" name="email_buy_package_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($mailtemplates as $template)
                            <option value="{{ $template->id }}" @if(old('email_buy_package_template', $setting->email_buy_package_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.package_payment_mail')}}</label>
                        <select class="form-control" name="email_package_payment" required>
                            <option value="no" @if(old('email_package_payment', $setting->email_package_payment) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('email_package_payment', $setting->email_package_payment) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.payment_template_mail')}}</label>
                        <select class="form-control" name="email_package_payment_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($mailtemplates as $template)
                            <option value="{{ $template->id }}" @if(old('email_package_payment_template', $setting->email_package_payment_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.user_notification_mail')}}</label>
                        <select class="form-control" name="email_package_user" required>
                            <option value="no" @if(old('email_package_user', $setting->email_package_user) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('email_package_user', $setting->email_package_user) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.user_template_mail')}}</label>
                        <select class="form-control" name="email_package_user_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($mailtemplates as $template)
                            <option value="{{ $template->id }}" @if(old('email_package_user_template', $setting->email_package_user_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.payment_approval_mail')}}</label>
                        <select class="form-control" name="email_approval_payment" required>
                            <option value="no" @if(old('email_approval_payment', $setting->email_approval_payment) == 'no') selected @endif>{{__('general.no')}}</option>
                            <option value="yes" @if(old('email_approval_payment', $setting->email_approval_payment) == 'yes') selected @endif>{{__('general.yes')}}</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.approval_template_mail')}}</label>
                        <select class="form-control" name="email_approval_payment_template">
                            <option value="">{{__('blash.choose_template')}}</option>
                            @foreach ($mailtemplates as $template)
                            <option value="{{ $template->id }}" @if(old('email_approval_payment_template', $setting->email_approval_payment_template) == $template->id) selected @endif>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}</button>
            </div>
        </form>
    </div>
</div>
<!-- End::app-content -->
@endsection