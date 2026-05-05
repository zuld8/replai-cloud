@extends('layouts.admin')

@section('button')
<div class="btn-list">
    <a href="{{route('links')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('cms.back_to_list_link')}}
    </a>
    <a href="{{route('links')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('cms.back_to_list_link')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div> 
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <form action="<?= route('links.edit',$link->id); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">{{$page}} </div>
                <x-validation-component></x-validation-component>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('general.insert_name')}}</label>
                        <input class="form-control" name="name" value="{{old('name',$link->name)}}" type="text" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('cms.url')}}</label>
                        <input class="form-control" name="url" value="{{old('url',$link->url)}}" type="url" required>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('cms.position')}}</label>
                        <select class="form-control" name="position" required>
                            <option value="1" @if(old('position',$link->position) == 1) selected @endif>Header</option>
                            <option value="2" @if(old('position',$link->position) == 2) selected @endif>Footer 1</option>
                            <option value="3" @if(old('position',$link->position) == 3) selected @endif>Footer 2</option>
                            <option value="4" @if(old('position',$link->position) == 4) selected @endif>Footer 3</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label class="form-label">{{__('cms.order')}}</label>
                        <input class="form-control" name="order_position" value="{{old('order_position',$link->order_position)}}" type="number" required>
                    </div> 
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}</button>
            </div>
        </form>
    </div>
</div>
@endsection