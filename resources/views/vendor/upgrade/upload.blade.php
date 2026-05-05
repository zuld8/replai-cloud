@extends('layouts.minimal')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/dropify/css/dropify.min.css')}}">
@endsection

@section('content')
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="#" class="navbar-brand navbar-brand-autodark d-flex justify-content-center">
                <img src="{{asset($settings->logo)}}" class="w-50">
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body text-center py-4 p-sm-5">
                <img src="{{$information->image ?? ''}}" class="w-100 rounded" />
                <h1 class="mt-5">Upload Upgrade File {{$information->code}} </h1>
                <p class="text-secondary">Upload Upgrade File Version {{$information->code}}. You are prohibited from upgrading to a lower or higher version than recommended. </p>
            </div>
            <form class="card-body py-4 p-sm-5 row" action="{{route('upgrade.versions.upload_start')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="col-12">
                    <x-validation-component></x-validation-component>
                </div>
                <div class="col-12">
                    <input type="hidden" name="version" value="{{$information->code}}" />
                    <input class="dropify" type="file" id="image" name="file" data-default-file="">
                </div>

                <div class="col-12 mt-3">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="ti ti-upload me-2 fs-16"></i> Upload File
                    </button>
                </div>
            </form>
        </div>
        <div class="row align-items-center mt-3">
            <div class="col-4">
                <div class="progress">
                    <div class="progress-bar" style="width: 10%" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" aria-label="25% Complete">
                        <span class="visually-hidden">10% Complete</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="btn-list justify-content-end">
                    <input type="hidden" id="urlback" value="{{route('upgrade.start')}}" />
                    <a href="{{route('upgrade.start')}}" class="btn btn-link link-secondary backtostart">
                        Back To Previous Page
                    </a>
                </div>
            </div>
        </div>
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