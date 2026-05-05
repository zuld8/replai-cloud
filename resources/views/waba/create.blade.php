@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('waba')}}" class="btn btn-primary  d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('master.device.back_to_device_list')}}
    </a>
    <a href="{{route('waba')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('master.device.back_to_device_list')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('waba.store'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    {{__('page.add_device')}}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">Facebook App ID</label>
                                <input class="form-control" name="appid" value="<?= old('appid'); ?>" type="text" required>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">Facebook App Secret</label>
                                <input class="form-control" name="app_secret" value="<?= old('appid'); ?>" type="text" required>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">WhatsApp Business Account ID</label>
                                <input class="form-control" name="businessid" value="<?= old('businessid'); ?>" type="text" required>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">Pilih Pengguna</label>
                                <select class="form-control users" name="agent[]" multiple="multiple" required>
                                    @foreach ($users as $user)
                                    <option value="<?= $user->id; ?>"><?= $user->name; ?></option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <label class="form-label">Access Token</label>
                                <input class="form-control" name="access_token" value="<?= old('access_token'); ?>" type="text" required>
                            </div> 

                            <div class="col-lg-6 col-sm-12 mt-3 ">
                                <label class="form-label">Verify Token ( Callback Token ) </label>
                                <div class="input-group mb-3">
                                    <input class="form-control" name="callback_token" value="<?= old('callback_token', $tokenUid); ?>" readonly type="text" required>
                                    <button class="btn btn-primary" type="button" onclick="copyId('<?= $tokenUid; ?>')">
                                        <i class="bx bx-clipboard text-white"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3 ">
                                <label class="form-label">Webhook Callback Url </label>
                                <div class="input-group mb-3">
                                    <input class="form-control" value="{{config('app.url')}}/api-app/waba/callback-url/{{$tokenUid}}" readonly type="text">
                                    <button class="btn btn-primary" type="button" onclick="copyId('<?= config('app.url') . '/api-app/waba/callback-url/' . $tokenUid; ?>')">
                                        <i class="bx bx-clipboard text-white"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.add_data')}}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.users').select2();
    });

    function copyId(id) {
        document.execCommand("copy");

        const tempInput = document.createElement("textarea");
        tempInput.value = id;
        document.body.appendChild(tempInput);

        tempInput.select();
        document.execCommand("copy");

        document.body.removeChild(tempInput);

        toastr.success("Berhasil menyalin text", {
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
</script>
@endsection