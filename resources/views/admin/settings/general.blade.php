@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/dropify/css/dropify.min.css')}}">
@endsection

@section('content')
<!-- Start::app-content -->
<div class="row">
    <div class="col-lg-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('general.settings.store'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    {{$page}}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.platform_name')}}</label>
                        <input class="form-control" name="name" value="<?= $setting->app_name; ?>" type="text">
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.email_contact')}}</label>
                        <input class="form-control" name="email" value="<?= $setting->email_contact; ?>" type="email">
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.phone_contact')}}</label>
                        <input class="form-control" name="phone" value="<?= $setting->phone_contact; ?>" type="number">
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.tax_package')}}</label>
                        <input class="form-control" max="100" name="tax" value="<?= (int)$setting->tax; ?>" type="number">
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.currency')}}</label>
                        <select class="form-control" name="currency">
                            <option value="">{{__('general.choose')}}</option>
                            @foreach(currencies() as $symbol => $name)
                            <option value="{{ $symbol }}" @if($setting->currency == $symbol) selected @endif>
                                {{ $name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.currency_position')}}</label>
                        <select class="form-control" name="currency_position">
                            <option value="">{{__('general.choose')}}</option>
                            <option value="start" @if($setting->currency_position == 'start') selected @endif>{{__('setting.start_position')}}</option>
                            <option value="end" @if($setting->currency_position == 'end') selected @endif>{{__('setting.end_position')}}</option>
                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('setting.contact_address')}}</label>
                        <textarea class="form-control" name="address">{{$setting->contact_address}}</textarea>
                    </div>
                    <div class="col-lg-3 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.logo')}}</label>
                        <input class="dropify" type="file" id="logo" name="logo" data-default-file="{{asset($setting->logo)}}">
                    </div>
                    <div class="col-lg-3 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.loader_icon')}}</label>
                        <input class="dropify" type="file" id="loader-logo" name="loader" data-default-file="{{asset($setting->loader)}}">
                    </div>
                    <div class="col-lg-3 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.white_logo')}}</label>
                        <input class="dropify" type="file" id="white" name="white_logo" data-default-file="{{asset($setting->white_logo)}}">
                    </div>
                    <div class="col-lg-3 col-sm-12 mt-3">
                        <label class="form-label">{{__('setting.icon')}}</label>
                        <input class="dropify" type="file" id="icon" name="icon" data-default-file="{{asset($setting->icon)}}">
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

@section('scripts')
<script src="{{ asset('assets/libs/dropify/js/dropify.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.dropify').dropify();
    });
</script>
@endsection
@endsection