@extends('layouts.admin')

@section('content')
<!-- List Data -->
<div class="row">
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>
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
                        <h1 class="mb-0 text-primary" style="font-size: 2rem;">{{number_format($business->package_active->price ?? 0)}}</h1> 
                    </div>
                    @endif
                </div>
                <ul class="ps-3 g-2 my-4">
                    <li class="mb-2">{{$business->name}}</li>
                    <li class="mb-2">{{$business->package_active ? $business->package_active->expire_date : '-'}}</li>
                </ul>
                @if($business->package_active)
                @if($business->package_active->days_option == 'limited')
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span>Penggunaan ( Hari ) </span>
                    <span>{{number_format($business->progress_day)}}% Completed</span>
                </div>
                <div class="progress mb-1" style="height: 8px">
                    <div class="progress-bar" role="progressbar" style="width: <?= $business->progress_day; ?>%" aria-valuenow="{{$business->progress_day}}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span>{{number_format($business->remaining_day)}} Hari Tersisa</span>
                @endif
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
                <div class="d-grid w-100 mt-4 pt-2 d-flex justify-content-center">
                    <a href="{{route('business.detail',$business->id)}}" class="btn btn-primary w-100 me-1">
                        Informasi Detail
                    </a>
                    <a href="{{route('business.delete',$business->id)}}" class="btn btn-danger w-100 deletebutton">
                        Hapus Bisnis
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if(count($pagination['links']) > 3)
    <div class="col-12 d-flex justify-content-center">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-round pagination-primary">
                @foreach($pagination['links'] as $paginate)

                @if($paginate['url'] != null)

                @if($paginate['label'] == '&laquo; Previous')
                <li class="page-item prev">
                    <a class="page-link" href="{{$paginate['url']}}"><i class="tf-icon bx bx-chevron-left"></i></a>
                </li> 
                @endif

                @if($paginate['label'] != '&laquo; Previous' && $paginate['label'] != 'Next &raquo;')

                @if($paginate['active'] == true)
                <li class="page-item active">
                    <a class="page-link" href="#">{{$paginate['label']}}</a>
                </li>
 
                @else
                <li class="page-item">
                    <a class="page-link" href="{{$paginate['url']}}">{{$paginate['label']}}</a>
                </li> 
                @endif
                @endif

                @if($paginate['label'] == 'Next &raquo;')
                <li class="page-item next">
                    <a class="page-link" href="{{$paginate['url']}}"><i class="tf-icon bx bx-chevron-right"></i></a>
                </li> 
                @endif

                @endif

                @endforeach
               
            </ul>
        </nav>
    </div>
    @endif


</div>
<!-- End List Data -->
@endsection