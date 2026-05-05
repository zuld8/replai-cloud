@extends('layouts.admin')

@section('button')
<div class="btn-list">
    <a href="{{route('package.storage.create')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-plus-circle"></i>
        {{__('general.add_data')}}
    </a>
    <a href="{{route('package.storage.create')}}" class="btn btn-primary d-sm-none btn-icon" aria-label="{{__('general.add_data')}}">
        <i class="bx bx-plus-circle"></i>
    </a>
</div>
@endsection

@section('content') 
<div class="row"> 
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>
    @forelse ($packages as $package)
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
        <div class="card card-package h-100 shadow-sm hover-shadow-lg transition">
            <div class="card-body d-flex flex-column">
                <!-- Header -->
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-md bg-primary-lt rounded me-3">
                        <i class="bx bx-data fs-3 text-primary"></i>
                    </div>
                    <div class="flex-fill">
                        <h3 class="card-title mb-1">{{$package->name}}</h3>
                        <div class="text-muted small">
                            @if($package->days_option == 'limited')
                            <i class="bx bx-time-five"></i> {{number_format($package->add_days)}} {{__('package.day')}}
                            @else
                            <i class="bx bx-infinite"></i> {{__('package.lifetime')}}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Price -->
                <div class="mb-4">
                    <div class="d-flex align-items-baseline">
                        <h2 class="mb-0 fw-bold text-primary">{{currency_format($package->price)}}</h2>
                        <span class="text-muted ms-2">
                            @if($package->days_option == 'limited')
                            / {{number_format($package->add_days)}} {{__('package.day')}}
                            @else
                            / {{__('package.lifetime')}}
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Features -->
                <div class="mb-4 flex-fill">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center mb-2">
                            @if($package->storage > 0)
                            <span class="badge bg-success-lt me-2">
                                <i class="bx bx-check"></i>
                            </span>
                            <span>Storage: <strong>{{$package->storage_name}}</strong></span>
                            @else
                            <span class="badge bg-danger-lt me-2">
                                <i class="bx bx-x"></i>
                            </span>
                            <span class="text-muted">{{__('package.without_storage')}}</span>
                            @endif
                        </li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="mt-auto">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{route('package.storage.update', $package->id)}}" class="btn btn-warning w-100">
                                <i class="bx bx-edit-alt"></i> {{__('package.edit_general')}}
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{route('package.storage.delete', $package->id)}}"
                                class="btn btn-danger w-100"
                                onclick="return confirm('{{__('package.delete_confirmation')}}');">
                                <i class="bx bx-trash"></i> {{__('package.delete_general')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Badge Corner -->
            @if($package->days_option == 'unlimited')
            <div class="ribbon ribbon-top ribbon-bookmark bg-primary">
                <i class="bx bx-star"></i>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="empty">
            <div class="empty-icon">
                <i class="bx bx-package fs-1"></i>
            </div>
            <p class="empty-title">{{__('package.no_package_storage')}}</p>
            <p class="empty-subtitle text-muted">
                {{__('package.start_adding_storage')}}
            </p>
            <div class="empty-action">
                <a href="{{route('package.storage.create')}}" class="btn btn-primary">
                    <i class="bx bx-plus-circle"></i>
                    {{__('package.add_first_package')}}
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Custom CSS -->
<style>
    .card-package {
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .card-package:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .hover-shadow-lg {
        transition: box-shadow 0.3s ease;
    }

    .transition {
        transition: all 0.3s ease;
    }

    .avatar-md {
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-primary-lt {
        background-color: rgba(32, 107, 196, 0.1);
    }

    .bg-success-lt {
        background-color: rgba(5, 205, 153, 0.1);
        color: #05cd99;
    }

    .bg-danger-lt {
        background-color: rgba(214, 57, 57, 0.1);
        color: #d63939;
    }

    .ribbon {
        position: absolute;
        top: 0.75rem;
        right: -0.25rem;
        z-index: 1;
        padding: 0.25rem 0.75rem;
        font-size: 0.625rem;
        font-weight: 600;
        line-height: 1.5;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        background: #206bc4;
        border-radius: 0.25rem;
    }

    .ribbon-bookmark:before {
        position: absolute;
        right: 0;
        bottom: -0.5rem;
        width: 0;
        height: 0;
        content: "";
        border-style: solid;
        border-width: 0.5rem 0.25rem 0 0.25rem;
        border-color: #1a569e transparent transparent transparent;
    }

    .empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-icon {
        margin-bottom: 1rem;
        color: #ccc;
    }

    .page-header {
        margin-bottom: 1.5rem;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }
</style>
@endsection