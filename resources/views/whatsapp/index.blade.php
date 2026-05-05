@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
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
@endsection

@section('content')
@php
    $__pkg      = \App\Models\Setting::where('id', my_business())->first(['id'])?->package_active;
    $__limitOk  = device_limitation(my_business());
    $__count    = \App\Models\WhatsappDevice::where('business_id', my_business())->count();
    $__max      = $__pkg ? $__pkg->device_limit : 0;
    $__label    = 'WA Personal';
@endphp
<!-- Start::app-content -->
<!-- Start::row-1 -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <x-validation-component></x-validation-component>
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    Daftar Akun Whatsapp
                </div>
                @if($__limitOk)
                <a href="<?= route('whatsapp.create'); ?>" class="btn btn-primary">
                    <i class="bx bx-plus-circle"></i> Tambah Akun
                </a>
                @else
                <span
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    data-bs-title="{{ 'Limit ' . $__label . ' tercapai (' . $__count . '/' . $__max . ' akun). Upgrade paket untuk menambah.' }}"
                    style="display:inline-block;opacity:0.5;cursor:not-allowed;filter:grayscale(0.4);">
                    <a tabindex="-1" style="pointer-events:none" onclick="return false;" href="<?= route('whatsapp.create'); ?>" class="btn btn-primary">
                        <i class="bx bx-plus-circle"></i> Tambah Akun
                    </a>
                </span>
                @endif
            </div>
            <div class="card-body">
                <table id="whatsappData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">No.Phone</th>
                            <th scope="col">Pengiriman Hari ini</th>
                            <th scope="col">Batas Pengiriman Harian</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($whatsapp as $w)
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $w->phone; ?></td>
                            <td><?= number_format($w->daily_send); ?></td>
                            <td><?= number_format($w->limit_per_day); ?></td>
                            <td>
                                <div class="toggle mb-3 <?= $w->status == 'active' ? 'on' : ''; ?>" onclick="activationData('<?= $w->id; ?>',this)">
                                    <span></span>
                                </div>
                            </td>
                            <td>
                                <a href="<?= route('whatsapp.update', $w->id); ?>" class="btn btn-outline-warning btn-icon fs-16 ">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <a href="<?= route('whatsapp.delete', $w->id); ?>" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
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
<!--End::row-1 -->

</div>
</div>
<!-- End::app-content -->

@endsection


@section('scripts')

<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
    function activationData(id, thisdata) {
        $.ajax({
            url: `/app/whatsapp-account/status/${id}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                Accept: 'application/json',
                'Content-Type': 'application/json',
                timeout: 0,
            },
            data: '',
            success: function(data) {
                toastr.success(data.message, {
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


            },
            cache: false,
            contentType: false,
            processData: false,
        })
    }

    $(function(e) {
        'use strict';

        $('#whatsappData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: 'Cari Akun Whatsapp...',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });
</script>
@endsection