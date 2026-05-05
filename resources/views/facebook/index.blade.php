@extends('layouts.app')

@section('styles') 
@endsection


@section('button')
 
@endsection

@section('content')
<div class="row g-4">

    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>
     
    <div class="col-xxl-4 col-xl-12 col-lg-12 col-md-4 col-sm-12 mb-3">
        <div class="card custom-card product-card h-100 ">
            <div class="card-body align-items-center row">
                <a href="{{route('facebook.redirect')}}" class="col-12 text-center">
                    <i class="bx bx-plus-circle text-primary" style="font-size: 180px;"></i>
                    <p class="h3 text-primary">{{ __('platform.facebook.add_account') }}</p>
                </a>
            </div>
        </div>
    </div>
     
</div>
@endsection

@section('scripts')
@endsection
