@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection


@section('button')
<div class="btn-list">
    <a href="{{route('directory.cities')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('master.directory.back_to_city')}}
    </a>
    <a href="{{route('directory.cities')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('master.directory.back_to_city')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <form action="<?= route('directory.city.store'); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    {{__('page.city.add')}}
                </div>
                <x-validation-component></x-validation-component>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('sidebar.state')}}</label>
                                <select class="form-control provinces" name="province" required>
                                    <option value="">{{__('master.directory.choose_state')}}</option>
                                    @foreach ($provinces as $province)
                                    <option value="<?= $province->id; ?>"><?= $province->name; ?></option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 col-sm-12 mt-3">
                                <label class="form-label">{{__('master.directory.insert_type')}} </label>
                                <input class="form-control" name="type" value="<?= old('type'); ?>" type="text">
                            </div>
                            <div class="col-12 mt-3">
                                <label class="form-label">{{__('general.insert_name')}} </label>
                                <input class="form-control" name="name" value="<?= old('name'); ?>" type="text" required>
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
        $('.provinces').select2();
    });
</script>
@endsection