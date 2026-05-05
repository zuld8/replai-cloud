@extends('layouts.starter')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">

            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    <i class="bx bx-history me-2"></i>
                    {{__('starter.transaction_history')}}
                </div>
            </div>

            <div class="card-body">
                <table id="whatsappData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('starter.business_name') }}</th>
                            <th scope="col">{{__('starter.no_ref')}}</th>
                            <th scope="col">{{__('blash.date')}}</th>
                            <th scope="col">{{__('starter.package_plan')}}</th>
                            <th scope="col">{{__('starter.price')}}</th>
                            <th scope="col">{{__('starter.tax')}}</th>
                            <th scope="col">{{__('starter.total')}}</th>
                            <th scope="col">{{__('general.status')}}</th>
                            <th scope="col">{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $t)
                        <tr>
                            <td><?= $t->business->name ?? ''; ?></td>
                            <td><?= $t->ref_no; ?></td>
                            <td><?= $t->created_at->format('Y-m-d'); ?></td>
                            <td><?= $t->package->name ?? ''; ?></td>
                            <td>{{currency_format($t->price)}}</td>
                            <td>{{currency_format($t->tax)}}</td>
                            <td>{{currency_format($t->final_total)}}</td>
                            <td>
                                @if($t->status == 'pending')
                                <span class="badge bg-warning text-orange-fg">{{__('starter.pending')}}</span>
                                @endif

                                @if($t->status == 'process')
                                <span class="badge bg-info text-indigo-fg">{{__('starter.process')}}</span>
                                @endif

                                @if($t->status == 'success')
                                <span class="badge bg-success text-lime-fg">{{__('starter.complete')}}</span>
                                @endif

                                @if($t->status == 'rejected')
                                <span class="badge bg-danger text-red-fg">{{__('starter.rejected')}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown me-1">
                                    <button type="button" class="btn btn-outline-info dropdown-toggle" id="dropdownMenuOffset" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="10,20">
                                        {{__('general.action')}}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
                                        <li>
                                            <a class="dropdown-item" href="<?= route('starter.transactions.detail', $t->id); ?>">
                                                <i class="bx bx-list-ul me-2"></i> {{__('starter.detail')}}
                                            </a>
                                        </li>
                                        @if($t->status == 'pending')
                                        <li>
                                            <a class="dropdown-item deletebutton" href="<?= route('starter.transactions.delete', $t->id); ?>">
                                                <i class="bx bx-trash me-2"></i> {{__('starter.reject_and_delete')}}
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
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
            ordering: false,
            language: {
                searchPlaceholder: '{{__("starter.search_transaction")}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

        // Delete button confirmation
        $(".deletebutton").on("click", function(e) {
            e.preventDefault();
            const href = $(this).attr("href");
            Swal.fire({
                title: "{{ __('general.are_you_sure') }}",
                text: "{{ __('starter.reject_and_delete') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "{{ __('starter.yes_delete') }}",
                cancelButtonText: "{{ __('starter.cancel') }}"
            }).then((result) => {
                if (result.value) {
                    document.location.href = href;
                }
            });
        });
    });
</script>
@endsection