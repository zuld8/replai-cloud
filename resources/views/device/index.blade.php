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
    $__limitOk  = device_limitation(my_business());
    $__count    = \App\Models\WhatsappDevice::where('business_id', my_business())->count();
    $__max      = $__pkg ? $__pkg->device_limit : 0;
    $__label    = 'WA Personal';
@endphp
<div class="btn-list"> 
    @if($__limitOk)
<a href="{{ route('device.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle"></i>
        {{__('platform.whatsapp.add_connection')}}
    </a>
@else
<span
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    data-bs-title="{{ 'Limit ' . $__label . ' tercapai (' . $__count . '/' . $__max . ' akun). Upgrade paket untuk menambah.' }}"
    style="display:inline-block;opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
    <a tabindex="-1" style="pointer-events:none" onclick="return false;" href="{{ route('device.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle"></i>
        {{__('platform.whatsapp.add_connection')}}
    </a>
</span>
@endif 
</div>

@endsection

@section('content')
<div class="row">
    <!-- Card Total Device -->
    <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">{{__('platform.whatsapp.total_device')}}</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{number_format($summary['all'])}}</h4>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-outline-primary rounded p-2">
                            <i class="bx bxl-whatsapp bx-sm"></i>
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
                        <p class="card-text">{{__('platform.whatsapp.not_connected')}}</p>
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

    <!-- Card Device Terkoneksi -->
    <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">{{__('platform.whatsapp.device_connected')}}</p>
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

    <!-- Tabel Daftar Device -->
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    {{__('platform.whatsapp.connection_list')}}
                </div>
            </div>
            <div class="card-body">
                <table id="whatsappData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.number')}}</th>
                            <th scope="col">{{__('general.wa_phone')}}</th>
                            <th scope="col">{{__('platform.whatsapp.broadcast_sent_today')}}</th>
                            <th scope="col">{{__('platform.whatsapp.daily_broadcast_limit_label')}}</th>
                            <th scope="col">{{__('general.status')}}</th>
                            <th scope="col">{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($device as $w)
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="lh-1">
                                        <a href="https://wa.me/<?= $w->phone; ?>" style="color: #0d6efd; font-weight: bold;" target="_blank" rel="noopener noreferrer">
                                            <?= $w->phone; ?>
                                        </a>
                                        <p class="text-muted fs-11 mb-0 mt-1">{{$w->name}}</p>
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
                                <span class="badge bg-success">{{__('platform.whatsapp.status_active')}}</span>
                                @else
                                <span class="badge bg-danger">{{__('platform.whatsapp.status_inactive')}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    {{-- Scan QR - Always accessible for active devices --}}
                                    <a href="<?= route('device.scan', $w->id); ?>"
                                        class="btn btn-sm btn-info"
                                        title="{{__('platform.whatsapp.scan_qr')}}">
                                        <i class="bx bx-qr"></i>
                                    </a>

                                    {{-- Copy ID - Always accessible --}}
                                    <a href="javascript:void(0);"
                                        onclick="copyId('<?= $w->id; ?>')"
                                        class="btn btn-sm btn-dark"
                                        title="{{__('platform.whatsapp.copy_id')}}">
                                        <i class="bx bx-link"></i>
                                    </a>

                                    {{-- Settings - Always accessible --}}
                                    <a href="<?= route('device.setting', $w->id); ?>"
                                        class="btn btn-sm btn-info"
                                        title="{{__('platform.whatsapp.settings')}}">
                                        <i class="bx bx-cog"></i>
                                    </a>

                                    {{-- Edit - Permission: whatsapp-unofficial.edit --}} 
                                    <a href="<?= route('device.update', $w->id); ?>"
                                        class="btn btn-sm btn-warning"
                                        title="{{__('platform.whatsapp.edit_device')}}">
                                        <i class="bx bx-pencil"></i>
                                    </a> 

                                    {{-- Delete - Permission: whatsapp-unofficial.hapus --}} 
                                    <a href="<?= route('device.delete', $w->id); ?>"
                                        class="btn btn-sm btn-danger deletebutton"
                                        title="{{__('platform.whatsapp.delete_device')}}">
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
        toastr.success("{{__('platform.whatsapp.copied_device_id')}}", {
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
        $('#whatsappData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("platform.whatsapp.search_device")}}',
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
            order: [
                [0, 'asc']
            ]
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
