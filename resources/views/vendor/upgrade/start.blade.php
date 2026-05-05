@extends('layouts.minimal')

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
                <h1 class="mt-5">Upgrade Version</h1>
                <p class="text-secondary">Carrying out the Version Upgrade Process is a risky action if it fails, therefore make sure the upgrade process is carried out to completion.</p>
            </div>
            <div class="hr-text hr-text-center hr-text-spaceless">START</div>
            <div class="card-body d-flex justify-content-center">
                <a href="{{route('upgrade.versions.upload')}}" class="btn btn-info w-100 me-2">
                    <i class="ti ti-upload me-2 fs-16"></i> Manual Upload
                </a>
                <a href="{{route('upgrade.versions.download')}}" class="btn btn-info w-100">
                    <i class="ti ti-download me-2 fs-16"></i> Auto Download
                </a>
            </div>
        </div>
        <div class="row align-items-center mt-3">
            <div class="col-4">
                <div class="progress">
                    <div class="progress-bar" style="width: 0%" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" aria-label="25% Complete">
                        <span class="visually-hidden">0% Complete</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="btn-list justify-content-end">
                    <a href="{{route('upgrade.versions')}}" class="btn btn-link link-secondary">
                        Back To Previous Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection