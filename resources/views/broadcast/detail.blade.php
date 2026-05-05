@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection

@section('button')
<div class="btn-list">
    <span class="d-none d-sm-inline">
        <a href="{{route('blash.export',$broadcast->id)}}" target="_blank" class="btn btn-dark">
            <i class="ti ti-download me-1"></i> {{__('general.export_data')}}
        </a>
    </span>
    <a href="{{route('broadcast')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        Kembali Ke Upselling Campaign
    </a>
    <a href="{{route('broadcast')}}" class="btn btn-info d-sm-none btn-icon" aria-label="Kembali Ke Halaman Broadcast Follow Up">
        <i class="bx bx-chevron-left"></i>
    </a>
    <button type="button" id="refresh_button" class="d-none"></button>
    <input type="hidden" value="<?= $broadcast->id; ?>" id="idBlash">
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    {{__('page.wa.detail')}}
                </div>
            </div>
            <div class="card-body">
                <table id="resultBlash" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.name')}}</th>
                            <th scope="col">{{__('general.wa_phone')}}</th>
                            <th scope="col">{{__('general.status')}}</th>
                            <th scope="col">{{__('blash.log')}}</th>
                            <th scope="col">{{__('blash.date')}}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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
    $(document).ready(function() {
        const result_blash_table = $('#resultBlash').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("blash.search_data")}}',
                sSearch: '',
            },
            "pageLength": 25,
            processing: true,
            serverSide: true,
            aaSorting: [
                [4, 'asc']
            ],
            ajax: {
                "url": '/app/blash/detail/' + $("#idBlash").val(),
                "data": function(d) {
                    d = datatable_pasarsafe_callback(d);
                }
            },
            columnDefs: [{
                targets: [4],
                orderable: false,
                searchable: true,
            }, ],
            columns: [{
                    data: 'store',
                    name: 'store'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'status_attribute',
                    name: 'status_attribute'
                },
                {
                    data: 'reports',
                    name: 'reports'
                },
                {
                    data: 'date',
                    name: 'date'
                },
            ],

        });

        $("body").on("click", "#refresh_button", function() {
            result_blash_table.ajax.reload();
        })

    });

    function activationData(id, thisdata) {
        $.ajax({
            url: `/app/blash/status-detail/${id}`,
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

                document.getElementById("refresh_button").click();

            },
            cache: false,
            contentType: false,
            processData: false,
        })
    }
</script>
@endsection
