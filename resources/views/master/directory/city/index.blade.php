@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection



@section('content')

<!-- List Data -->
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    {{__('page.city.page')}}
                    <button type="button" id="refresh_button" class="d-none"></button>
                </div> 
            </div>
            <div class="card-body">
                <input type="hidden" id="paramsData" value="<?= $params; ?>" />
                <table id="cityData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.id')}}</th>
                            <th scope="col">{{__('general.name')}}</th>
                            <th scope="col">{{__('sidebar.state')}}</th> 
                            <th scope="col">{{__('master.directory.total_district')}}</th> 
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End List Data -->
 

@endsection


@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
    $(document).ready(function() {
        const city_table = $('#cityData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("master.directory.search_city")}}',
                sSearch: '',
            },
            "pageLength": 25,
            processing: true,
            serverSide: true,
            aaSorting: [
                [1, 'asc']
            ],
            ajax: {
                "url": '/app/master/directory/cities?' + $("#paramsData").val(),
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
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'province',
                    name: 'province'
                }, 
                {
                    data: 'districts',
                    name: 'districts'
                },
            ],

        });

        $("body").on("click", "#refresh_button", function() {
            city_table.ajax.reload();
        })

    });
 
</script>
@endsection