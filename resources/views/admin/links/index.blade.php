@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('links.create')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-plus-circle"></i>
        {{__('general.add_data')}}
    </a>
    <a href="{{route('links.create')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('general.add_data')}}">
        <i class="bx bx-plus-circle"></i>
    </a>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
    <x-validation-component></x-validation-component>
        <div class="card custom-card">
            
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                   {{$page}}
                </div> 
            </div>
            <div class="card-body">
                <table id="linksData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.number')}}</th>
                            <th scope="col">{{__('general.name')}}</th>
                            <th scope="col">{{__('cms.position')}}</th>
                            <th scope="col">{{__('cms.page_link')}}</th>
                            <th scope="col">{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($links as $link)
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $link->name; ?></td>
                            <td><?= $link->position_name; ?></td>
                            <td> 
                                <a href="{{$link->url}}" target="_blank" class="btn btn-outline-info btn-icon fs-16 ">
                                    <i class="ti ti-link"></i>
                                </a>
                            </td>
                            <td>
                                <a href="<?= route('links.update', $link->id); ?>" class="btn btn-outline-warning btn-icon fs-16 ">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <a href="<?= route('links.delete', $link->id); ?>" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                    <i class="bx bx-trash "></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
    $(function(e) {
        'use strict';

        $('#linksData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("cms.search_link")}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });
</script>
@endsection