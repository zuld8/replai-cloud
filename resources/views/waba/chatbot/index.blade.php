@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection


@section('button')
<div class="btn-list">
    <button id="deleteSelected" class="btn btn-danger d-none">
        <i class="ti ti-trash fs-16 me-2"></i> {{__("general.delete_choosed")}}
    </button>
    <a href="{{ route('waba.chatbot.create',$meta->id) }}" class="btn btn-primary">
        <i class="bx bx-plus-circle fs-16 me-1"></i>
        Buat Auto-Reply (Manual)
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
                <div class="col-12 col-md-10">
                    <div class="card-body table-responsive">
                        <input type="hidden" id="paramsData" value="<?= $params; ?>" />
                        <button class="d-none" id="refresh_button"></button>
                        <table id="chatbotData" class="table table-bordered text-nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </th>
                                    <th scope="col">{{__('chatbot.keyword')}}</th>
                                    <th scope="col">{{__('chatbot.method_reply')}}</th> 
                                    <th scope="col">{{__('general.action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
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
    $(document).ready(function() {
        const chatbot_table = $('#chatbotData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("blash.search_data")}}',
                sSearch: '',
            },
            "pageLength": 25,
            processing: true,
            serverSide: true,
            aaSorting: [
                [0, 'asc']
            ],
            ajax: {
                "url": '/app/waba/chatbot/<?= $meta->id; ?>?' + $("#paramsData").val(),
                "data": function(d) {
                    d = datatable_pasarsafe_callback(d);
                }
            },
            columnDefs: [{
                targets: [1],
                orderable: false,
                searchable: true,
            }, ],
            columns: [{
                    data: 'id',
                    render: function(data) {
                        return `<input class="form-check-input row-checkbox" type="checkbox" id="checkAll" value="${data}">`;
                    },
                    orderable: false
                },
                {
                    data: 'keyword',
                    name: 'keyword'
                },
                {
                    data: 'method',
                    name: 'method'
                }, 
                {
                    data: 'action',
                    name: 'action'
                }
            ],

        });

        $("body").on("click", "#refresh_button", function() {
            chatbot_table.ajax.reload();
        });

        $('#checkAll').on('change', function() {
            $('.row-checkbox').prop('checked', this.checked);
            toggleDeleteButton();
        });

        // Handle individual checkbox change
        $('body').on('change', '.row-checkbox', function() {
            if ($('.row-checkbox:checked').length === $('.row-checkbox').length) {
                $('#checkAll').prop('checked', true);
            } else {
                $('#checkAll').prop('checked', false);
            }
            toggleDeleteButton();
        });

        // Toggle delete button visibility
        function toggleDeleteButton() {
            if ($('.row-checkbox:checked').length > 0) {
                $('#deleteSelected').removeClass('d-none');
            } else {
                $('#deleteSelected').addClass('d-none');
            }
        }

        $('#deleteSelected').on('click', function() {
            const selectedIds = $('.row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedIds.length > 0) {
                Swal.fire({
                    title: 'Are You Sure ?',
                    text: 'Deleted data cannot be recovered',
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: 'Ok',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: `/app/auto-reply/chatbot/delete-multiple`,
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data: {
                                ids: selectedIds
                            },
                            success: function(data) {
                                if (data.success) {
                                    toastr.success(data.message);
                                    chatbot_table.ajax.reload();
                                    $('#deleteSelected').addClass('d-none');
                                    $('#checkAll').prop('checked', false);
                                } else {
                                    toastr.error(data.message);
                                }
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.message || '{{ __("Terjadi kesalahan") }}');
                            }
                        });
                    }
                });

            }
        });


    });

    function deleteData(id) {
        $.ajax({
            url: `/app/auto-reply/chatbot/delete/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                Accept: 'application/json',
                'Content-Type': 'application/json',
                timeout: 0,
            },
            data: '',
            success: function(data) {
                if (!data.status) {
                    toastr.error(data.message, {
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
                } else {
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
                }


            },
            cache: false,
            contentType: false,
            processData: false,
        })
    }
</script>
@endsection