@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/dropify/css/dropify.min.css')}}">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('banks')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('bank.back_to_list')}}
    </a>
    <a href="{{route('banks')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('bank.back_to_list')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12">
        <form action="<?= route('banks.store'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">{{$page}}</div>
                <x-validation-component></x-validation-component>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-lg-4 col-sm-12 mt-3">
                                <label class="form-label">{{__('bank.name')}}</label>
                                <input class="form-control" name="name" value="{{old('name')}}" type="text" required>
                            </div>
                            <div class="col-lg-4 col-sm-12 mt-3">
                                <label class="form-label">{{__('bank.code')}}</label>
                                <input class="form-control" name="code" value="{{old('code')}}" type="text" required>
                            </div>
                            <div class="col-lg-4 col-sm-12 mt-3">
                                <label class="form-label">{{__('bank.number')}}</label>
                                <input class="form-control" name="number" type="number" value="{{old('number')}}" required>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label">{{__('bank.logo')}}</label>
                                <input class="dropify" type="file" id="image" name="image" data-default-file="">
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
<script src="{{ asset('assets/libs/dropify/js/dropify.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.dropify').dropify();
    });
</script>
@endsection