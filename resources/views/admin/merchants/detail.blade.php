@extends('layouts.admin')

@section('button')
<div class="btn-list">
    <a href="{{route('merchants')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('customer.back_to_list')}}
    </a>
    <a href="{{route('merchants')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('customer.back_to_list')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-xxl-12 col-xl-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body d-sm-flex align-items-top p-4   main-profile-cover">
                <p class="avatar avatar-xxl avatar-rounded online me-3 my-auto">
                    <img src="{{asset($merchant->owner->image_data ?? '')}}" alt="">
                </p>
                <div class="flex-fill main-profile-info my-auto">
                    <h5 class="fw-semibold mb-1 ">{{$merchant->owner->name ?? ''}}</h5>
                    <div>
                        <p class="mb-1 text-muted">{{__('customer.owner')}}</p>
                        <div class="fs-12 op-7 mb-0 d-flex">
                            <p class="me-3 mb-0">{{__('customer.register_date')}} : {{$merchant->created_at->format('Y-m-d')}}</p>
                        </div>
                    </div>
                </div>
                <div class="main-profile-info ms-auto">
                    <div class="">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex mb-0 ms-auto">
                                <div class="me-4">
                                    <p class="fw-bold fs-20  text-shadow mb-0">{{$merchant->name}}</p>
                                    <p class="mb-0 fs-12 text-muted ">Nama Pemilik</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-top justify-content-between">
                    <div>
                        <span class="badge bg-outline-primary rounded p-2">
                            <i class="bx bx-user-circle bx-sm"></i>
                        </span>
                    </div>
                    <div class="flex-fill ms-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <p class="text-muted mb-0">Human Agent
                                </p>
                                <h3 class="fw-semibold mb-0">
                                    {{number_format($data['users'])}}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-top justify-content-between">
                    <div>
                        <span class="badge bg-outline-primary rounded p-2">
                            <i class="bx bx-building bx-sm"></i>
                        </span>
                    </div>
                    <div class="flex-fill ms-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <p class="text-muted mb-0">Unit Bisnis
                                </p>
                                <h3 class="fw-semibold mb-0">
                                    {{number_format($data['business'])}}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-top justify-content-between">
                    <div>
                        <span class="badge bg-outline-primary rounded p-2">
                            <i class="bx bx-layout bx-sm"></i>
                        </span>
                    </div>
                    <div class="flex-fill ms-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <p class="text-muted mb-0">Earning Package
                                </p>
                                <h3 class="fw-semibold mb-0">
                                    {{number_format($data['package'])}}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-lg-6">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-top justify-content-between">
                    <div>
                        <span class="badge bg-outline-primary rounded p-2">
                            <i class="bx bx-coin bx-sm"></i>
                        </span>
                    </div>
                    <div class="flex-fill ms-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <p class="text-muted mb-0">Earning TopUp
                                </p>
                                <h3 class="fw-semibold mb-0">
                                    {{number_format($data['topup'])}}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<div class="row mb-4">

    @foreach ($businesses as $business)
    <div class="col-sm-12 col-lg-4">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    @if($business->package_active)
                    <span class="badge bg-outline-primary">{{$business->package_active->package->name ?? ''}}</span>
                    @else
                    <span class="badge bg-outline-danger">Tidak Ada Paket</span>
                    @endif

                    @if($business->package_active)
                    <div class="d-flex justify-content-center">
                        <sup class="h5 pricing-currency mt-3 mb-0 me-1 text-primary">Rp</sup>
                        <h1 class="display-5 mb-0 text-primary">{{number_format($business->package_active->price ?? 0)}}</h1>
                        <sub class="fs-6 pricing-duration mt-auto mb-3">/{{number_format($business->package_active->add_days)}} Hari</sub>
                    </div>
                    @endif
                </div>
                <ul class="ps-3 g-2 my-4">
                    <li class="mb-2">{{$business->name}}</li>
                    <li class="mb-2">{{$business->package_active ? $business->package_active->expire_date : '-'}}</li>
                </ul>
                @if($business->package_active)
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span>Penggunaan ( Hari ) </span>
                    <span>{{number_format($business->progress_day)}}% Completed</span>
                </div>
                <div class="progress mb-1" style="height: 8px">
                    <div class="progress-bar" role="progressbar" style="width: <?= $business->progress_day; ?>%" aria-valuenow="{{$business->progress_day}}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span>{{number_format($business->remaining_day)}} Hari Tersisa</span>
                @else
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span>Penggunaan ( Hari )</span>
                    <span>100% Completed</span>
                </div>
                <div class="progress mb-1" style="height: 8px">
                    <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span>0 Hari Tersisa</span>
                @endif
                <div class="d-grid w-100 mt-4 pt-2">
                    <a href="{{route('business.detail',$business->id)}}" class="btn btn-primary">
                        Informasi Detail
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection


@section('scripts')

@endsection