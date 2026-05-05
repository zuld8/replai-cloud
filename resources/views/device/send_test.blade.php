@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection


@section('button')
<div class="btn-list">
    <a href="{{ route('device') }}" class="btn btn-primary">
        <i class="bx bx-chevron-left"></i>
        Kembali Ke Daftar Koneksi
    </a>
</div>

@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <form action="<?= route('device.message.store'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf 
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">

                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">Dikirim Dari:</label>
                                <select class="form-control device" name="device" required>
                                    <option value="">{{__('general.choose')}} </option>
                                    @foreach ($devices as $device)
                                    <option value="{{$device->id}}" @if($device->id == old('device')) selected @endif >{{$device->name}} - {{$device->phone}} </option>
                                    @endforeach
                                </select>
                            </div>

<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-label">Tipe Pengiriman:</label>
    <select class="form-control" name="type" required>
        <option value="personal" @if(old('type', 'personal') == 'personal') selected @endif>{{ __('general.personal') }}</option>
        <option value="group" @if(old('type', 'personal') == 'group') selected @endif>{{ __('general.group') }}</option>
    </select>
</div>


<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-label">Nomor WhatsApp Penerima:</label>
    <input 
        class="form-control" 
        name="phone" 
        id="waPhone" 
        value="{{ old('phone') }}" 
        type="number" 
        required
    >
</div>

<script>
    const waInput = document.getElementById('waPhone');

    // Saat fokus dan kosong, isi dengan '62'
    waInput.addEventListener('focus', function () {
        if (this.value.trim() === '') {
            this.value = '62';
        }
    });

    // Saat input berubah (manual atau autofill), ganti '0' di awal jadi '62'
    waInput.addEventListener('input', function () {
        if (this.value.startsWith('0')) {
            this.value = '62' + this.value.slice(1);
        }
    });

    // Saat halaman dimuat, cek apakah perlu ganti '0' jadi '62'
    window.addEventListener('DOMContentLoaded', function () {
        if (waInput.value.startsWith('0')) {
            waInput.value = '62' + waInput.value.slice(1);
        }
    });
</script>


                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('sidebar.message_template')}}</label>
                                <select class="form-control template" name="template" required>
                                    <option value="">{{__('general.choose')}} </option>
                                    @foreach ($templates as $template)
                                    <option value="{{$template->id}}" @if($template->id == old('template')) selected @endif >{{$template->name}} </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>Kirim Pesan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.device').select2();
        $('.template').select2();
    });
</script>
@endsection
