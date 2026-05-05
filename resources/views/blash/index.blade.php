@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection


@section('button')
<div class="btn-list">
    <a href="{{ route('blash.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle"></i>
        {{ __('broadcast.wa.create_schedule_button') }}
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
                    {{__('page.wa.page')}}
                    <button type="button" id="refresh_button" class="d-none"></button>
                </div>
            </div>
            <div class="card-body">
                <input type="hidden" id="paramsData" value="<?= $params; ?>" />
                <table id="blashData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('blash.title')}}</th>
                            <th scope="col">{{__('scrapp.schedule')}}</th>
                            <th scope="col">{{__('sidebar.category')}}</th>
                            <th scope="col">{{ __('broadcast.wa.wa_group') }}</th> 
                            <th scope="col">{{__('blash.template')}}</th>
                            <th scope="col">{{__('general.status')}}</th>
                            <th scope="col">{{__('general.action')}}</th>
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
        const blash_table = $('#blashData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("blash.search")}}',
                sSearch: '',
            },
            "pageLength": 25,
            processing: true,
            serverSide: true,
            aaSorting: [
                [2, 'asc']
            ],
            ajax: {
                "url": '/app/blash?' + $("#paramsData").val(),
                "data": function(d) {
                    d = datatable_pasarsafe_callback(d);
                }
            },
            columnDefs: [{
                targets: [2],
                orderable: false,
                searchable: true,
            }, ],
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'schedule_attribute',
                    name: 'schedule_attribute'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'group',
                    name: 'group'
                }, 
                {
                    data: 'template',
                    name: 'template'
                },
                {
                    data: 'status_attribute',
                    name: 'status_attribute'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],

        });

// Setelah DataTable dirender, nonaktifkan semua toggle (checkbox)
$('#blashData').on('draw.dt', function () {
    $('#blashData input[type="checkbox"]').prop('disabled', true);
});


        $("body").on("click", "#refresh_button", function() {
            blash_table.ajax.reload();
        })

    });

    function activationData(id, thisdata) {
        $.ajax({
            url: `/app/blash/status/${id}`,
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