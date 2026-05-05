@extends('layouts.admin')

@section('button')
<div class="btn-list">
    <a href="{{route('blog.categories')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('blog.back_to_list_category')}}
    </a>
    <a href="{{route('blog.categories')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('blog.back_to_list_category')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <form action="<?= route('blog.categories.store'); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title"> {{$page}} </div>
                <x-validation-component></x-validation-component>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label">{{__('general.insert_name')}}</label>
                        <input class="form-control" name="name" type="text" required>
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