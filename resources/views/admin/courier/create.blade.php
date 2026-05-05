@extends('layouts.admin')
 

@section('button')
<div class="btn-list">
    <a href="{{route('couriers')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('courier.back_to_courier_list')}}
    </a>
    <a href="{{route('couriers')}}" class="btn btn-info d-sm-none btn-icon" aria-label="Kembali ke Daftar Ekspedisi">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12">
        <form action="<?= route('courier.store'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">{{$page}}</div>
                <x-validation-component></x-validation-component>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('courier.courier_name')}}</label>
                                <input class="form-control" name="name" value="{{old('name')}}" type="text" required>
                            </div>
                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('courier.courier_code')}}</label>
                                <input class="form-control" name="code" value="{{old('code')}}" type="text" required>
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