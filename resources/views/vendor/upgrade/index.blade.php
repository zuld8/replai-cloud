@extends('layouts.admin')

@section('content')
@if($response->status == true)
<div class="row row-cards">
    @if($response->current_version->status == true)
    <div class="col-lg-8">
        <div class="card card-lg">
            <div class="card-body">
                <div class="markdown">
                    <div class="text-center mb-4">
                        <img src="{{$response->current_version->image}}" class="rounded w-75 mb-3" />
                        <h2 class="mb-0">{{$response->current_version->name}}</h2>
                        <p><span>V.{{$response->current_version->code}}</span></p>
                    </div>

                    <?= $response->current_version->description; ?>

                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-lg-4">
        @if($response->to_upgrade->status == true)
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <img src="{{$response->to_upgrade->image}}" class="rounded" style="max-width:100px;" />
                    </div>
                    <div>
                        <small class="text-secondary">Upgrade Recommendations</small>
                        <h3 class="lh-1">{{$response->to_upgrade->name}}</h3>
                    </div>
                </div>
                <div class="text-secondary mb-3">
                    {{$response->to_upgrade->description}}
                </div>
                <ul class="list-unstyled space-y-1">
                    <li>
                        <i class="ti ti-key me-2 fs-16"></i>
                        Version : {{$response->to_upgrade->code}}
                    </li>
                    <li>
                        <i class="ti ti-calendar me-2 fs-16"></i>
                        Release : {{$response->to_upgrade->release}}
                    </li>
                    <li>
                        <i class="ti ti-alert-octagon me-2 fs-16"></i>
                        Minimum Version : {{$response->to_upgrade->must_version}}
                    </li>
                </ul>

            </div>
            <div class="card-footer">
                <a href="{{route('upgrade.start')}}" class="btn btn-primary w-100">
                    <i class="ti ti-copy-check me-2 fs-16"></i> Upgrade Now
                </a>
            </div>
        </div>
        @endif

        @if($response->now_version->status == true)
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <img src="{{$response->now_version->image}}" class="rounded" style="max-width:100px;" />
                    </div>
                    <div>
                        <small class="text-secondary">Latest Version</small>
                        <h3 class="lh-1">{{$response->now_version->name}}</h3>
                    </div>
                </div>
                <div class="text-secondary mb-3">
                    {{$response->now_version->description}}
                </div>
                <ul class="list-unstyled space-y-1">
                    <li>
                        <i class="ti ti-key me-2 fs-16"></i>
                        Version : {{$response->now_version->code}}
                    </li>
                    <li>
                        <i class="ti ti-calendar me-2 fs-16"></i>
                        Release : {{$response->now_version->release}}
                    </li>
                    <li>
                        <i class="ti ti-alert-octagon me-2 fs-16"></i>
                        Minimum Version : {{$response->now_version->must_version}}
                    </li>
                </ul>

            </div>
        </div>
        @endif
    </div>
</div>
@endif
@endsection