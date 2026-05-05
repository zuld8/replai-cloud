@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
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
    $__limitOk  = telegram_limitation(my_business());
    $__count    = \App\Models\TelegramKey::where('business_id', my_business())->count();
    $__max      = $__pkg ? $__pkg->telegram : 0;
    $__label    = 'Telegram';
@endphp
<div class="btn-list">
    @if($__limitOk)
<a href="{{ route('telegram.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle"></i>
        {{__('platform.telegram.add_connection')}}
    </a>
@else
<span
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    data-bs-title="{{ 'Limit ' . $__label . ' tercapai (' . $__count . '/' . $__max . ' akun). Upgrade paket untuk menambah.' }}"
    style="display:inline-block;opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
    <a tabindex="-1" style="pointer-events:none" onclick="return false;" href="{{ route('telegram.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle"></i>
        {{__('platform.telegram.add_connection')}}
    </a>
</span>
@endif
</div>

@endsection

@section('content')
<div class="row">
    <!-- Card Total Telegram -->
    <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">{{__('platform.telegram.total_bot')}}</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{number_format($summary['all'])}}</h4>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-outline-primary rounded p-2">
                            <i class="bx bxl-telegram bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Tidak Terkoneksi -->
    <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">{{__('platform.telegram.not_connected')}}</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{number_format($summary['not_active'])}}</h4>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-outline-danger rounded p-2">
                            <i class="bx bx-info-circle bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Telegram Terkoneksi -->
    <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">{{__('platform.telegram.bot_connected')}}</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{number_format($summary['active'])}}</h4>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-outline-success rounded p-2">
                            <i class="bx bx-check-circle bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Telegram Bot -->
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    {{__('platform.telegram.connection_list')}}
                </div>
            </div>
            <div class="card-body">
                <table id="telegramData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.number')}}</th>
                            <th scope="col">{{__('platform.telegram.bot_name')}}</th>
                            <th scope="col">{{__('platform.telegram.broadcast_sent_today')}}</th>
                            <th scope="col">{{__('platform.telegram.daily_broadcast_limit_label')}}</th>
                            <th scope="col">{{__('general.status')}}</th>
                            <th scope="col">{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($telegram as $w)
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="bx bxl-telegram"></i>
                                        </span>
                                    </div>
                                    <div class="lh-1">
                                        <span class="fw-semibold">{{$w->name}}</span>
                                        <p class="text-muted fs-11 mb-0 mt-1">Bot ID: {{$w->id}}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info"><?= number_format($w->daily_send); ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info"><?= number_format($w->limit_per_day); ?></span>
                            </td>
                            <td>
                                @if($w->status == 'active')
                                    <span class="badge bg-success">{{__('platform.telegram.status_active')}}</span>
                                @else
                                    <span class="badge bg-danger">{{__('platform.telegram.status_inactive')}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="javascript:void(0);" onclick="copyId('<?= $w->id; ?>')" class="btn btn-sm btn-dark" title="{{__('platform.telegram.copy_id')}}">
                                        <i class="bx bx-link"></i>
                                    </a>
                                    <a href="<?= route('telegram.update', $w->id); ?>" class="btn btn-sm btn-warning" title="{{__('platform.telegram.edit_bot')}}">
                                        <i class="bx bx-pencil"></i>
                                    </a>
                                    <a href="<?= route('telegram.delete', $w->id); ?>" class="btn btn-sm btn-danger deletebutton" title="{{__('platform.telegram.delete_bot')}}">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
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
    function copyId(id) {
        // Create temporary textarea
        const tempInput = document.createElement("textarea");
        tempInput.value = id;
        document.body.appendChild(tempInput);

        // Select and copy
        tempInput.select();
        document.execCommand("copy");

        // Remove temporary element
        document.body.removeChild(tempInput);

        // Show success notification
        toastr.success("{{__('platform.telegram.copied_bot_id')}}", {
            timeOut: 5000,
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            preventDuplicates: true,
            onclick: null,
            showDuration: '100',
            hideDuration: '1000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
            tapToDismiss: false,
        });
    }

    $(function(e) {
        'use strict';

        // Initialize DataTable
        $('#telegramData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("platform.telegram.search_bot")}}',
                sSearch: '',
                lengthMenu: "{{__('general.show')}} _MENU_ {{__('general.entries')}}",
                info: "{{__('general.showing')}} _START_ {{__('general.to')}} _END_ {{__('general.of')}} _TOTAL_ {{__('general.entries')}}",
                infoEmpty: "{{__('general.showing')}} 0 {{__('general.to')}} 0 {{__('general.of')}} 0 {{__('general.entries')}}",
                infoFiltered: "({{__('general.filtered')}} {{__('general.from')}} _MAX_ {{__('general.total')}} {{__('general.entries')}})",
                zeroRecords: "{{__('general.no_data')}}",
                emptyTable: "{{__('general.no_data')}}",
                paginate: {
                    first: "{{__('general.first')}}",
                    last: "{{__('general.last')}}",
                    next: "{{__('general.next')}}",
                    previous: "{{__('general.previous')}}"
                }
            },
            pageLength: 10,
            order: [[0, 'asc']]
        });
    });
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipEls.forEach(function(el) { new bootstrap.Tooltip(el, {trigger: 'hover focus'}); });
});
</script>
@endpush
