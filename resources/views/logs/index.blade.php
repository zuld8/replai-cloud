@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('logs.delete')}}?type=email" class="btn btn-danger d-none d-sm-inline-block">
        <i class="ti ti-trash"></i>
        {{__('blash.delete_all_log')}}
    </a>
    <a href="{{route('logs.delete')}}?type=email" class="btn btn-danger d-sm-none btn-icon" aria-label="{{__('general.add_data')}}">
        <i class="ti ti-trash"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-body">
                <button type="button" id="refresh_button" class="d-none"></button>
                <table id="logData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.number')}}</th>
                            <th scope="col">{{__('blash.log')}}</th>
                            <th scope="col">{{__('blash.date')}}</th>
                            <th scope="col">{{__('general.status')}}</th>
                            <th scope="col">{{__('blash.error_note')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($logs as $log)
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $log->description; ?></td>
                            <td><?= tanggal_indo(substr($log->created_at, 0, 10)); ?><?= substr($log->created_at, 11, 16); ?></td>
                            <td>
                                <?= $log->status == 'success' ? __('report.email_log.success') : ($log->status == 'pending' ? __('report.email_log.pending') : 'Error'); ?>
                            </td>
                            <td>
                                {{$log->error}}
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

        $('#logData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("blash.search_data")}}',
                sSearch: '',
            },
            "pageLength": 25,
        });

    });
</script>
@endsection