@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection

@section('button')
<div class="btn-list">
    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalAddPage" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-plus-circle"></i>
        {{__('general.add_data')}}
    </a>
    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalAddPage" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('general.add_data')}}">
        <i class="bx bx-plus-circle"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <x-validation-component></x-validation-component>
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    {{$page}}
                </div>
            </div>
            <div class="card-body">
                <table id="provinceData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.number')}}</th>
                            <th scope="col">{{__('general.name')}}</th>
                            <th scope="col">{{__('cms.type')}}</th>
                            <th scope="col">{{__('cms.page_link')}}</th>
                            <th scope="col">{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($pages as $p)
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $p->name; ?></td>
                            <td><?= $p->page_name; ?></td>
                            <td>
                                <a href="{{$p->page_url}}" target="_blank" class="btn btn-outline-info btn-icon fs-16 ">
                                    <i class="ti ti-link"></i>
                                </a>
                            </td>
                            <td>
                                <a href="<?= route('pages.update', $p->id); ?>" class="btn btn-outline-warning btn-icon fs-16 ">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <a href="<?= route('pages.delete', $p->id); ?>" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
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

<!-- Modal For add Template -->
<div class="modal fade" id="modalAddPage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= route('pages.store'); ?>" method="post" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title" id="modalAddPageLabel">{{__('cms.create_page')}} </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body row">
                <div class="col-12">
                    <label class="form-label">{{__('general.insert_name')}}</label>
                    <input class="form-control" name="page_name" value="<?= old('name_name'); ?>" type="text" required>
                </div>
                <div class="col-12 mt-3">
                    <label class="form-label">{{__('cms.type')}}</label>
                    <select class="form-control" name="page_type">
                        <option value="home">{{__('cms.page.home')}}</option>
                        <option value="pricing">{{__('cms.page.pricing')}}</option>
                        <option value="contact">{{__('cms.page.contact')}}</option>
                        <option value="page">{{__('cms.page.other')}}</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-outline-primary">{{__('general.add_data')}}</button>
            </div>
        </form>
    </div>
</div>
<!-- End For Add Template -->
@endsection


@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
    $(function(e) {
        'use strict';

        $('#provinceData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("cms.search")}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });
</script>
@endsection