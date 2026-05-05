@extends('layouts.app')

@section('button')
<div class="btn-list">
    <a href="{{route('waba')}}" class="btn btn-primary d-none d-sm-inline-block">
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
        <div class="card">
            <div class="row g-0">
                <x-waba-sidebar-update-component idwaba="{{$device->id}}"></x-waba-sidebar-update-component>
                <form action="<?= route('waba.token.update', $device->id); ?>" enctype="multipart/form-data" method="POST" class="col-12 col-md-10 d-flex flex-column">
                    @csrf
                    <div class="card-body">

                        <div class="row g-3 mt-4">

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('waba.access_token')}}</label>
                                <div class="input-group mb-3">
                                    <input class="form-control" name="token" value="<?= $data['access_token']; ?>" type="text" required>
                                    <button class="btn btn-primary" type="button" onclick="copyId('<?= $data['access_token']; ?>')">
                                        <i class="bx bx-clipboard text-white"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">Verify Token ( Callback Token ) </label>
                                <div class="input-group mb-3">
                                    <input class="form-control" name="callback_token" value="<?= old('callback_token', $tokenUid); ?>" readonly type="text" required>
                                    <button class="btn btn-primary" type="button" onclick="copyId('<?= $tokenUid; ?>')">
                                        <i class="bx bx-clipboard text-white"></i>
                                    </button> 
                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="card-footer bg-transparent mt-auto">
                        <div class="btn-list justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
     
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