@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/css/pages/upselling.css')}}">
@endsection

@section('button')
<div class="btn-list">
    <a href="{{ route('broadcast.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>
        {{ __('broadcast.upselling.create_campaign_button') }}
    </a>
    <button type="button" class="btn btn-primary" onclick="refreshTable()">
        <i class="ti ti-refresh me-1"></i>
        {{ __('broadcast.upselling.refresh_button') }}
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>

     
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title">
                    <i class="ti ti-megaphone me-2"></i>
                    {{ __('broadcast.upselling.page_title') }}
                    <button type="button" id="refresh_button" class="d-none"></button>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="statusFilter">
                        <option value="">{{ __('broadcast.upselling.all_status') }}</option>
                        <option value="active">{{ __('broadcast.upselling.status_active') }}</option>
                        <option value="inactive">{{ __('broadcast.upselling.status_inactive') }}</option>
                        <option value="scheduled">{{ __('broadcast.upselling.status_scheduled') }}</option>
                        <option value="completed">{{ __('broadcast.upselling.status_completed') }}</option>
                    </select>
                    <select class="form-select form-select-sm" id="frequencyFilter">
                        <option value="">{{ __('broadcast.upselling.all_frequency') }}</option>
                        <option value="once">{{ __('broadcast.upselling.frequency_once') }}</option>
                        <option value="daily">{{ __('broadcast.upselling.frequency_daily') }}</option>
                        <option value="monthly">{{ __('broadcast.upselling.frequency_monthly') }}</option>
                        <option value="yearly">{{ __('broadcast.upselling.frequency_yearly') }}</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <input type="hidden" id="paramsData" value="<?= $params; ?>" />
                <table id="blashData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" width="25%">{{ __('broadcast.upselling.campaign_info') }}</th>
                            <th scope="col" width="20%">{{ __('broadcast.upselling.schedule_frequency') }}</th>
                            <th scope="col" width="15%">{{ __('broadcast.upselling.target_category') }}</th>
                            <th scope="col" width="15%">{{ __('broadcast.upselling.method_template') }}</th>
                            <th scope="col" width="10%">{{ __('broadcast.upselling.status') }}</th>
                            <th scope="col" width="15%">{{ __('broadcast.upselling.action') }}</th>
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
    let blash_table;

    // Translation strings dari Laravel
    const translations = {
        searchPlaceholder: "{{ __('broadcast.upselling.search_placeholder') }}",
        lengthMenu: "{{ __('broadcast.upselling.length_menu') }}",
        info: "{{ __('broadcast.upselling.info') }}",
        infoEmpty: "{{ __('broadcast.upselling.info_empty') }}",
        infoFiltered: "{{ __('broadcast.upselling.info_filtered') }}",
        paginateFirst: "{{ __('broadcast.upselling.paginate_first') }}",
        paginateLast: "{{ __('broadcast.upselling.paginate_last') }}",
        paginateNext: "{{ __('broadcast.upselling.paginate_next') }}",
        paginatePrevious: "{{ __('broadcast.upselling.paginate_previous') }}",
        confirmActivate: "{{ __('broadcast.upselling.confirm_activate') }}",
        confirmDeactivate: "{{ __('broadcast.upselling.confirm_deactivate') }}",
        confirmDelete: "{{ __('broadcast.upselling.confirm_delete') }}",
        successTitle: "{{ __('broadcast.upselling.success_title') }}",
        successDelete: "{{ __('broadcast.upselling.success_delete') }}",
        errorTitle: "{{ __('broadcast.upselling.error_title') }}",
        errorStatusChange: "{{ __('broadcast.upselling.error_status_change') }}",
        errorDelete: "{{ __('broadcast.upselling.error_delete') }}"
    };

    $(document).ready(function() {
        initDataTable(); 

        // Filter handlers
        $('#statusFilter, #frequencyFilter').on('change', function() {
            blash_table.ajax.reload();
        });
    });

    function initDataTable() {
        blash_table = $('#blashData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: translations.searchPlaceholder,
                sSearch: '',
                lengthMenu: translations.lengthMenu,
                info: translations.info,
                infoEmpty: translations.infoEmpty,
                infoFiltered: translations.infoFiltered,
                paginate: {
                    first: translations.paginateFirst,
                    last: translations.paginateLast,
                    next: translations.paginateNext,
                    previous: translations.paginatePrevious
                }
            },
            pageLength: 25,
            processing: true,
            serverSide: true,
            aaSorting: [
                [4, 'desc']
            ], // Sort by status
            ajax: {
                url: '/app/broadcast-followups?' + $("#paramsData").val(),
                data: function(d) {
                    d = datatable_pasarsafe_callback(d);
                    d.status_filter = $('#statusFilter').val();
                    d.frequency_filter = $('#frequencyFilter').val();
                }
            },
            columnDefs: [{
                targets: [4, 5],
                orderable: false,
                searchable: false,
            }],
            columns: [{
                    data: 'campaign_info',
                    name: 'name'
                },
                {
                    data: 'schedule_info',
                    name: 'schedule_frequency'
                },
                {
                    data: 'target_info',
                    name: 'category'
                },
                {
                    data: 'method_info',
                    name: 'broadcast_method'
                },
                {
                    data: 'status_info',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],
            drawCallback: function() {
                // Update statistics after table draw 
            }
        });

        $("body").on("click", "#refresh_button", function() {
            refreshTable();
        });
    }

    function refreshTable() {
        blash_table.ajax.reload(); 
    }
 
    function activationData(id, thisdata) {
        const isActive = $(thisdata).is(':checked');
        const confirmMessage = isActive ? translations.confirmActivate : translations.confirmDeactivate;

        if (!confirm(confirmMessage)) {
            $(thisdata).prop('checked', !isActive);
            return;
        }

        $.ajax({
            url: `/app/blash/status/${id}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            success: function(data) {
                toastr.success(data.message, translations.successTitle, {
                    timeOut: 5000,
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                });
                refreshTable();
            },
            error: function(xhr) {
                toastr.error(translations.errorStatusChange, translations.errorTitle);
                $(thisdata).prop('checked', !isActive);
            }
        });
    }

    function deleteCampaign(id) {
        if (!confirm(translations.confirmDelete)) {
            return;
        }

        $.ajax({
            url: `/app/broadcast-followups/delete/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function(data) {
                toastr.success(translations.successDelete, translations.successTitle);
                refreshTable();
            },
            error: function(xhr) {
                toastr.error(translations.errorDelete, translations.errorTitle);
            }
        });
    }
</script>
@endsection