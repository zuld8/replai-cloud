@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection


@section('button')
<div class="btn-list">
    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalAddTemplate" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-plus-circle"></i>
        {{__('general.add_data')}}
    </a>
    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalAddTemplate" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('general.add_data')}}">
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
                    {{__('sidebar.message_template')}}
                </div>
            </div>
            <div class="card-body">
                <table id="provinceData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.number')}}</th>
                            <th scope="col">{{__('general.name')}}</th>
                            <th scope="col">{{__('master.template.use')}}</th>
                            <th scope="col">{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($templates as $template)
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $template->name; ?></td>
                            <td>
                                <a class="text-info" href="<?= route('blash'); ?>?template=<?= $template->id; ?>">
                                    {{number_format($template->blashs->count())}} {{__('general.blash')}}
                                </a>
                            </td>
                            <td>
                                <a href="<?= route('templatemail.update', $template->id); ?>" class="btn btn-outline-warning btn-icon fs-16 ">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <a href="<?= route('templatemail.delete', $template->id); ?>" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
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
<div class="modal fade" id="modalAddTemplate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= route('templatemail.store'); ?>" method="post" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title" id="modalAddTemplateLabel">{{__('master.template.add_mail_template')}} </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body row">
                <div class="col-12">
                    <label class="form-label">{{__('general.insert_name')}}</label>
                    <input class="form-control" name="name" value="<?= old('name'); ?>" type="text" required>
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
                searchPlaceholder: '{{__("master.template.search")}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });
</script>
@endsection