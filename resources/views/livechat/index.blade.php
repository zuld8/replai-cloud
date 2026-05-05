@extends('layouts.app')

@section('styles') 
<link rel="stylesheet" href="{{asset('assets/css/pages/livechat.css')}}"> 
@endsection

@section('button')
<style>
.btn-limit-disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
    filter: grayscale(0.5);
    display: inline-block;
}
.btn-limit-disabled > * {
    pointer-events: none;
}
</style>
@php
    $__pkg      = \App\Models\Setting::where('id', my_business())->first(['id'])?->package_active;
    $__limitOk  = live_chat_limitation(my_business());
    $__count    = \App\Models\LiveChat::where('business_id', my_business())->count();
    $__max      = $__pkg ? $__pkg->livechat_limit : 0;
    $__label    = 'Live Chat';
@endphp
<div class="btn-list">
    @if($__limitOk)
<a href="{{ route('livechat.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle me-1"></i>
        {{ __('livechat.create_widget') }}
    </a>
@else
<span
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    data-bs-title="{{ 'Limit ' . $__label . ' tercapai (' . $__count . '/' . $__max . ' akun). Upgrade paket untuk menambah.' }}"
    style="display:inline-block;opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
    <a tabindex="-1" style="pointer-events:none" onclick="return false;" href="{{ route('livechat.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle me-1"></i>
        {{ __('livechat.create_widget') }}
    </a>
</span>
@endif
</div>

@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <x-validation-component></x-validation-component>
    </div>

    @forelse ($livechats as $chat)
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
        <div class="card custom-card livechat-card h-100">
            <div class="card-body text-center p-4">
                <!-- Avatar with Status -->
                <div class="avatar-wrapper mb-3">
                    <span class="avatar avatar-xxl avatar-rounded">
                        <img src="{{asset($chat->image_data)}}" alt="{{$chat->name}}" class="rounded-circle">
                    </span>
                    <span class="avatar-status" title="{{ __('livechat.active') }}"></span>
                </div>

                <!-- Widget Name -->
                <h6 class="mb-1 fw-semibold text-truncate">
                    {{$chat->name}}
                </h6>
                <p class="text-muted fs-12 mb-3">
                    <i class="bx bx-bot me-1"></i>{{$chat->type_name}}
                </p>

                <!-- AI Training Badge -->
                @if($chat->finetunnel)
                <div class="mb-3">
                    <span class="badge bg-info-transparent rounded-pill">
                        <i class="bx bx-brain me-1"></i>{{$chat->finetunnel->name}}
                    </span>
                </div>
                @else
                <div class="mb-3">
                    <span class="badge bg-secondary-transparent rounded-pill">
                        <i class="bx bx-info-circle me-1"></i>{{ __('livechat.no_ai_training') }}
                    </span>
                </div>
                @endif

                <!-- Stats -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="stat-item border rounded p-2">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="bx bx-envelope-open text-warning fs-20 me-2"></i>
                                <div class="text-start">
                                    <p class="mb-0 fs-11 text-muted">{{ __('dashboard.open') }}</p>
                                    <h6 class="mb-0 fw-semibold">{{number_format($chat->history->where('status','open')->count())}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-item border rounded p-2">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="bx bx-check-circle text-success fs-20 me-2"></i>
                                <div class="text-start">
                                    <p class="mb-0 fs-11 text-muted">{{ __('dashboard.resolved') }}</p>
                                    <h6 class="mb-0 fw-semibold">{{number_format($chat->history->where('status','resolved')->count())}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="btn-list d-flex gap-2 justify-content-center">
                    <a href="{{route('livechat.update',$chat->id)}}" class="btn btn-primary btn-sm flex-fill">
                        <i class="bx bx-edit-alt me-1"></i>{{ __('livechat.detail') }}
                    </a>
                    <a href="{{route('livechat.delete',$chat->id)}}" class="btn btn-outline-danger btn-sm deletebutton" >
                        <i class="bx bx-trash"></i>
                    </a>
                </div>
            </div>
 
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body text-center py-5">
                <i class="bx bx-message-square-x display-4 text-muted mb-3"></i>
                <h5 class="mb-2">{{ __('livechat.no_widget_yet') }}</h5>
                <p class="text-muted mb-4">{{ __('livechat.no_widget_description') }}</p>
                <a href="{{ route('livechat.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus-circle me-1"></i>{{ __('livechat.create_widget') }}
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if(count($pagination['links']) > 3)
<div class="row">
    <div class="col-12 d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-round pagination-primary mb-0">
                @foreach($pagination['links'] as $paginate)
                    @if($paginate['url'] != null)
                        @if($paginate['label'] == '&laquo; Previous')
                        <li class="page-item prev {{ $paginate['active'] ? 'disabled' : '' }}">
                            <a class="page-link" href="{{$paginate['url']}}">
                                <i class="bx bx-chevron-left"></i>
                            </a>
                        </li>
                        @endif

                        @if($paginate['label'] != '&laquo; Previous' && $paginate['label'] != 'Next &raquo;')
                            @if($paginate['active'] == true)
                            <li class="page-item active">
                                <a class="page-link" href="javascript:void(0)">{{$paginate['label']}}</a>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{$paginate['url']}}">{{$paginate['label']}}</a>
                            </li>
                            @endif
                        @endif

                        @if($paginate['label'] == 'Next &raquo;')
                        <li class="page-item next">
                            <a class="page-link" href="{{$paginate['url']}}">
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipEls.forEach(function(el) { new bootstrap.Tooltip(el, {trigger: 'hover focus'}); });
});
</script>
@endpush
