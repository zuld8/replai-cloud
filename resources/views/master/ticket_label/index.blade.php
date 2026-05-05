@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection


@section('button')
<div class="btn-list">
    <a href="{{ route('ticket.label.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle"></i>
        {{ __('master.ticket_label.add_ticket_label') }}
    </a>
</div>

@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card"> 
            <div class="card-body">
                <table id="provinceData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.number')}}</th>
                            <th scope="col">{{__('general.name')}}</th>
                            <th scope="col">{{__('master.ticket_label.position')}}</th>
                            <th scope="col">{{__('master.ticket_label.color')}}</th>
                            <th scope="col">{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($labels as $label)
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $label->name; ?></td>
                            <td>
                                <span class="badge bg-secondary">{{ $label->position }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge" style="background-color: {{ $label->color }}; min-width: 60px;">&nbsp;</span>
                                    <small class="text-muted">{{ $label->color }}</small>
                                </div>
                            </td>
                            <td>
                                <a href="<?= route('ticket.label.update', $label->id); ?>" class="btn btn-outline-warning btn-icon fs-16 ">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <a href="<?= route('ticket.label.delete', $label->id); ?>" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
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

        $('#provinceData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__('master.label.search')}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });
</script>
@endsection