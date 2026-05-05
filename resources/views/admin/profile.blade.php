@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/dropify/css/dropify.min.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>
    
    <div class="col-lg-8 col-sm-12 mt-4">
        <form action="<?= route('admin.profile.change'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    {{__('general.update_profile')}}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label">{{__('general.full_name')}}</label>
                        <input class="form-control" name="name" value="<?= auth()->user()->name; ?>" type="text" required>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('general.email')}}</label>
                        <input class="form-control" name="email" value="<?= auth()->user()->email; ?>" type="email" required>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('general.wa_phone')}}</label>
                        <input class="form-control" name="phone" value="<?= auth()->user()->phone; ?>" type="number" required>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('auth.gender')}}</label>
                        <select class="form-control" name="gender">
                            <option value="male" @if(auth()->user()->gender == 'male') selected @endif>{{__('auth.male')}}</option>
                            <option value="female" @if(auth()->user()->gender == 'female') selected @endif>{{__('auth.female')}}</option>
                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('general.photo')}}</label>
                        <input class="dropify" type="file" id="image" name="image" data-default-file="{{asset(auth()->user()->image_data)}}">
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}
                </button>
            </div>
        </form>
    </div>

    <div class="col-lg-4 col-sm-12 mt-4">
        <form action="<?= route('admin.profile.password'); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    {{__('general.change_password')}}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label">{{__('auth.current_password')}}</label>
                        <input class="form-control" name="old_password" type="password" required>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('general.new_password')}}</label>
                        <input class="form-control" name="password" type="password" required>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="form-label">{{__('general.password_confirmation')}}</label>
                        <input class="form-control" name="confirm" type="password" required>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/dropify/js/dropify.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.dropify').dropify();
    });
</script>
@endsection