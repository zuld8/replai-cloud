@extends('layouts.starter') 

@section('content')
<div class="row align-items-stretch">
    <div class="col-12 mb-4">
        <x-validation-component></x-validation-component>
    </div>

    @foreach ($businesses as $business)
    <div class="col-sm-12 col-lg-4 d-flex card-wrapper">
        <div class="card mb-4 w-100 h-100 {{ !$business->package_active ? 'border-danger' : '' }}">
            <div class="card-body d-flex flex-column justify-content-between">
                
                <!-- Header Section -->
                <div>
                    <!-- Package Badge & Price -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            @if($business->package_active)
                            <span class="badge bg-label-primary rounded-pill mb-2">
                                <i class="bx bx-crown me-1"></i>
                                {{ $business->package_active->package->name ?? '' }}
                            </span>
                            @else
                            <span class="badge bg-label-danger rounded-pill mb-2">
                                <i class="bx bx-error-circle me-1"></i>
                                {{ __('starter.no_active_package') }}
                            </span>
                            @endif
                        </div>

                        @if($business->package_active)
                        <div class="text-end">
                            <div class="d-flex align-items-start">
                                <small class="text-muted me-1">Rp</small>
                                <h4 class="mb-0 text-primary fw-bold">
                                    {{ number_format($business->package_active->price ?? 0) }}
                                </h4>
                            </div>
                            <small class="text-muted">/ {{ __('starter.monthly') }}</small>
                        </div>
                        @endif
                    </div>

                    <!-- Business Info -->
                    <div class="mb-4">
                        <h5 class="mb-2 d-flex align-items-center">
                            <i class="bx bx-briefcase text-primary me-2"></i>
                            {{ $business->name }}
                        </h5>
                        
                        @if($business->package_active)
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bx bx-calendar me-1"></i>
                            <span>{{ __('starter.expires_on') }}: <strong>{{ $business->package_active->expire_date }}</strong></span>
                        </div>
                        @else
                        <div class="alert alert-warning d-flex align-items-center p-2 mb-0" role="alert">
                            <i class="bx bx-info-circle me-2"></i>
                            <small>{{ __('starter.please_buy_package') }}</small>
                        </div>
                        @endif
                    </div>

                    <!-- Progress Section -->
                    @if($business->package_active && $business->package_active->days_option == 'limited')
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">
                                <i class="bx bx-time-five me-1"></i>
                                {{ __('starter.package_usage') }}
                            </small>
                            <span class="badge bg-label-info">{{ number_format($business->progress_day) }}%</span>
                        </div>
                        
                        <div class="progress mb-2" style="height: 10px">
                            <div class="progress-bar {{ $business->remaining_day < 7 ? 'bg-danger' : ($business->remaining_day < 14 ? 'bg-warning' : 'bg-success') }}" 
                                 role="progressbar" 
                                 style="width: {{ $business->progress_day }}%"
                                 aria-valuenow="{{ $business->progress_day }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bx bx-calendar-check me-1"></i>
                                {{ number_format($business->remaining_day) }} {{ __('starter.remaining_days') }}
                            </small>
                            @if($business->remaining_day < 7)
                            <span class="badge bg-danger">{{ __('starter.almost_expired') }}</span>
                            @elseif($business->remaining_day < 14)
                            <span class="badge bg-warning">{{ __('starter.attention') }}</span>
                            @endif
                        </div>
                    </div>

                    @elseif(!$business->package_active)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">
                                <i class="bx bx-time-five me-1"></i>
                                {{ __('starter.package_usage') }}
                            </small>
                            <span class="badge bg-label-danger">100%</span>
                        </div>
                        
                        <div class="progress mb-2" style="height: 10px">
                            <div class="progress-bar bg-danger" 
                                 role="progressbar" 
                                 style="width: 100%"
                                 aria-valuenow="100" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        
                        <small class="text-danger">
                            <i class="bx bx-error-circle me-1"></i>
                            {{ __('starter.package_inactive') }}
                        </small>
                    </div>

                    @else
                    <!-- Unlimited Package Indicator -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="text-center">
                            <i class="bx bx-infinite text-success mb-2" style="font-size: 2rem;"></i>
                            <div class="fw-semibold text-success">{{ __('starter.unlimited_package') }}</div>
                            <small class="text-muted">{{ __('starter.no_time_limit') }}</small>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="mt-4">
                    @if($business->package_active)
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('starter.business.choose', $business->id) }}" 
                               class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="bx bx-log-in-circle me-1"></i>
                                {{ __('starter.enter_business') }}
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('starter.business.detail', $business->id) }}" 
                               class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="bx bx-shopping-bag me-1"></i>
                                {{ __('starter.buy_package') }}
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="row g-2">
                        <div class="col-7">
                            <a href="{{ route('starter.business.detail', $business->id) }}" 
                               class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                <i class="bx bx-shopping-bag me-1"></i>
                                {{ __('starter.buy_package') }}
                            </a>
                        </div>
                        <div class="col-5">
                            <a href="{{ route('starter.business.delete', $business->id) }}" 
                               class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center deletebutton">
                                <i class="bx bx-trash me-1"></i>
                                {{ __('starter.delete_business') }}
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Additional Info -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            @if($business->package_active)
                            <i class="bx bx-check-circle text-success me-1"></i>
                            {{ __('starter.business_active') }}
                            @else
                            <i class="bx bx-info-circle text-warning me-1"></i>
                            {{ __('starter.activate_package') }}
                            @endif
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
   
    /* Progress bar animation */
    .progress-bar {
        transition: width 0.6s ease;
    }
    
    /* Badge styling */
    .badge.rounded-pill {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    } 
</style>
@endsection

@section('scripts')
<script>
    $(".choosepackage").on("click", function(e) {
        e.preventDefault();
        const href = $(this).attr("href");
        Swal.fire({
            title: "{{ __('general.are_you_sure') }}",
            text: "{{ __('starter.choose_package_alert') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ok",
        }).then((result) => {
            if (result.value) {
                document.location.href = href;
            }
        });
    });
    
    // Confirm delete business
    $(".deletebutton").on("click", function(e) {
        e.preventDefault();
        const href = $(this).attr("href");
        Swal.fire({
            title: "{{ __('starter.delete_business_confirm') }}",
            text: "{{ __('starter.delete_business_warning') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "{{ __('starter.yes_delete') }}",
            cancelButtonText: "{{ __('starter.cancel') }}"
        }).then((result) => {
            if (result.value) {
                document.location.href = href;
            }
        });
    });
</script>
@endsection