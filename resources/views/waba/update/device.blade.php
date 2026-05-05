@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('waba')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('master.device.back_to_device_list')}}
    </a>
    <a href="{{route('waba')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('master.device.back_to_device_list')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card">
            <div class="row g-0">
                <x-waba-sidebar-update-component idwaba="{{$meta->id}}"></x-waba-sidebar-update-component>
                <div class="col-12 col-md-10 d-flex flex-column">
                    <div class="card-body">
                        <table id="whatsappData" class="table table-bordered text-nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">{{__('general.number')}}</th>
                                    <th scope="col">{{__('general.wa_phone')}}</th>
                                    <th scope="col">{{__('master.device.daily_sent')}}</th>
                                    <th scope="col">{{__('master.device.limit_sent')}}</th>
                                    <th scope="col">{{__('general.status')}}</th>
                                    <th scope="col">{{__('blash.template')}}</th>
                                    <th scope="col">{{__('general.action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $no = 1;
                                @endphp
                                @foreach ($devices as $w)
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $w->phone; ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info"> <?= number_format($w->daily_send); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info"> <?= number_format($w->limit_per_day); ?></span>
                                    </td>
                                    <td> <?= $w->status == 'active' ? __('general.active') : __('general.no_active'); ?></td>
                                    <td> {{number_format($w->templates->count())}} </td>
                                    <td>

                                        <a href="<?= route('waba.autoreply', $w->id); ?>" class="btn btn-sm btn-warning ">
                                            <i class="bx bx-pencil"></i>
                                        </a>

                                        @if($w->status != 'active')
                                        <a href="<?= route('waba.activation', $w->id); ?>" class="btn btn-sm btn-info">
                                            <i class="bx bx-check-circle"></i>
                                        </a>
                                        @endif
                                        <a href="<?= route('waba.greeting', $w->id); ?>" class="btn btn-sm btn-info ">
                                            <i class="bx bx-chat"></i>
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
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>
<script>
    function copyId(id) {
        document.execCommand("copy");

        const tempInput = document.createElement("textarea");
        tempInput.value = id;
        document.body.appendChild(tempInput);

        tempInput.select();
        document.execCommand("copy");

        document.body.removeChild(tempInput);

        toastr.success("{{__('master.device.copied_device_id')}}", {
            timeOut: 5e3,
            closeButton: !0,
            debug: !1,
            newestOnTop: !0,
            progressBar: !0,
            positionClass: 'toast-top-right',
            preventDuplicates: !0,
            onclick: null,
            showDuration: '100',
            hideDuration: '1000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
            tapToDismiss: !1,
        })
    }

    $(function(e) {
        'use strict';

        $('#whatsappData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("master.device.search")}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });
</script>
@endsection