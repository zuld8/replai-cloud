@extends('layouts.app')

@section('button')
<div class="btn-list">
    <a href="{{route('device')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
       {{__('master.device.back_to_device_list')}}
    </a>
    <a href="{{route('device')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('master.device.back_to_device_list')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
            <div class="card-title">
                    {{__('master.device.please_scan_qr')}}
                </div> 
                <div class="card-header-action d-none loggout_area">
                    <a href="javascript:void(0)" class="btn btn-outline-danger logout-btn" data-id="{{ $device->id }}">
                        <i class="bx bx-power-off"></i>&nbsp {{__('sidebar.signout')}}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <input type="hidden" id="device_status" value="1">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">
                        <input type="hidden" id="device_id" value="{{$device->id}}">

                        <input type="hidden" id="warningLang" value="{{__('general.warning')}}">
                        <input type="hidden" id="expiredLang" value="{{__('master.device.qr_expired')}}">
                        <input type="hidden" id="expiredAlertLang" value="{{__('master.device.qr_expired_alert')}}">
                        <input type="hidden" id="refreshPageLang" value="{{__('master.device.refresh_page')}}">
                        <input type="hidden" id="connectedLang" value="{{__('master.device.connected')}}">
                        <input type="hidden" id="serverLossLang" value="{{__('master.device.server_not_connect')}}">
                        <input type="hidden" id="areyouSureLang" value="{{__('master.device.are_you_sure')}}">
                        <input type="hidden" id="logoutLang" value="{{__('master.device.logout_session')}}">
                        <input type="hidden" id="waitingLang" value="{{__('master.device.please_waiting')}}">
                        <input type="hidden" id="okLang" value="{{__('general.ok')}}">
                        <input type="hidden" id="closeLang" value="{{__('general.close')}}">

                        <div class="loader-area">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-grow text-info text-center" role="status"></div>
                            </div>
                            <br />
                            <p class="text-center">
                                <strong>{{__('master.device.please_waiting')}} </strong>
                            </p>
                        </div>
                        <div class=" qr-area text-center"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/js/qr.js')}}"></script>
@endsection