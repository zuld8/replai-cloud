@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection

@section('button')
<div class="btn-list">
    <span class="d-none d-sm-inline">
        <a href="{{route('scrapping_contact.export',$scrapping->id)}}" target="_blank" class="btn btn-dark">
            <i class="ti ti-download me-1"></i> {{__('general.export_data')}}
        </a>
    </span>
    <a href="{{route('scrapping_contact')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('scrapp.back_to')}}
    </a>
    <a href="{{route('scrapping_contact')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('scrapp.back_to')}}">
        <i class="bx bx-chevron-left"></i>
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
                    {{__('page.scrapp.result')}}
                    <input type="hidden" id="scrapId" value="<?= $scrapping->id; ?>">
                </div>
            </div>
            <div class="card-body">
                <table id="storeData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.name')}}</th>
                            <th scope="col">{{__('general.telp')}}</th>
                            <th scope="col">{{__('general.email')}}</th>
                            <th scope="col">{{__('sidebar.category')}}</th> 
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
        const store_table = $('#storeData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("customer.search")}}',
                sSearch: '',
            },
            "pageLength": 25,
            processing: true,
            serverSide: true,
            aaSorting: [
                [1, 'asc']
            ],
            ajax: {
                "url": '/app/scrapping/detail/' + $("#scrapId").val(),
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'category',
                    name: 'category'
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

    });
</script>
@endsection