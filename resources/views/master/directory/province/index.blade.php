@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/buttons.bootstrap5.min.css')}}">
@endsection


@section('content')
<!-- List Data -->
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    {{__('page.state.page')}}
                </div>
            </div>
            <div class="card-body">
                <table id="provinceData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">{{__('general.id')}}</th>
                            <th scope="col">{{__('general.name')}}</th> 
                            <th scope="col">{{__('master.directory.total_city')}}</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($provinces as $province)
                        <tr>
                            <td><?= $province->id; ?></td>
                            <td><?= $province->name; ?></td> 
                            <td>
                                <a class="text-info" href="<?= route('directory.cities'); ?>?province=<?= $province->id; ?>">
                                    {{number_format($province->cities->count())}} {{__('master.directory.city')}}
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
<!-- End List Data -->
 
@endsection


@section('scripts')

<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
    
    $(function(e) {
        'use strict';

        $('#provinceData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: '{{__("master.directory.search_state")}}',
                sSearch: '',
            },
            "pageLength": 25,
        });

    });
</script>
@endsection