@extends('layouts.admin')

@section('button')
<div class="btn-list">
    <a href="{{route('package.storage')}}" class="btn btn-secondary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('package.back_to')}}
    </a>
    <a href="{{route('package.storage')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('package.back_to')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<form action="<?= route('package.storage.store'); ?>" enctype="multipart/form-data" method="POST" class="row">
    @csrf
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>
    <div class="col-12">
        <div class="card mb-4"> 
            <div class="card-body row">
                <div class="col-lg-6 col-sm-12 mt-3">
                    <label class="form-label">{{__('package.name')}}</label>
                    <input class="form-control" name="name" value="{{old('name')}}" type="text" required>
                </div>
                
                <div class="col-lg-6 col-sm-12 mt-3">
                    <label class="form-label">{{__('package.subscription_period')}}</label>
                    <select class="form-control dayoption" name="days_option">
                        <option value="limited" @if(old('days_option')=='limited' ) selected @endif>{{__('package.limited')}}</option>
                        <option value="unlimited" @if(old('days_option')=='unlimited' ) selected @endif>{{__('package.unlimited')}}</option>
                    </select>
                </div>
                <div class="col-lg-6 col-sm-12 mt-3 @if(old('days_option') == 'unlimited') d-none @endif formdays">
                    <label class="form-label">{{__('package.add_days')}}</label>
                    <input class="form-control" name="add_days" value="{{old('add_days',0)}}" type="number" required>
                </div>
                <div class="col-lg-6 col-sm-12 mt-3 ">
                    <label class="form-label">Storage ( Mb ) </label>
                    <input class="form-control" name="storage" value="{{old('storage',0)}}" type="number" required>
                </div> 

                <div class="col-lg-6 col-sm-12 mt-3 ">
                    <label class="form-label">{{__('package.price')}}</label>
                    <input class="form-control" name="price" value="{{old('price')}}" type="number">
                </div>  
            </div>
        </div>
    </div>

 
    <div class="col-12">
        <button type="submit" class="btn btn-primary w-100">{{__('package.add_data')}}</button>
    </div>
</form>
@endsection

@section('scripts')
<script>
  

    $(".dayoption").on("change", function() {
        if ($(this).val() == 'limited') {
            $(".formdays").removeClass('d-none');
        } else {
            $(".formdays").addClass('d-none');
        }
    });
</script>
@endsection