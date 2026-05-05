@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <form action="{{route('transactions')}}" method="GET" class="card-body row">
                <div class="col-lg-4 col-sm-12">
                    <label class="form-label">{{__('sidebar.customers')}}</label>
                    <select class="form-control merchants" name="merchant" required>
                        <option value="">{{__('customer.search')}}</option>
                    </select>
                </div>
                <div class="col-lg-4 col-sm-12">
                    <label class="form-label">{{__('transaction.start_date')}}</label>
                    <input type="datetime-local" class="form-control" name="start_date">
                </div>
                <div class="col-lg-4 col-sm-12">
                    <label class="form-label">{{__('transaction.end_date')}}</label>
                    <div class="input-group">
                        <input type="datetime-local" class="form-control" name="end_date">
                        <button class="btn btn-info mb-0" type="submit" id="button-addon2"><i class="bx bx-search align-middle"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="col-xl-12 mt-2">
    <div class="card custom-card">
        <x-validation-component></x-validation-component>
        <div class="card-header d-flex justify-content-between">
            <div class="card-title">
                {{$page}}
            </div>
        </div>
        <div class="card-body">
            <table id="transactionData" class="table table-bordered text-nowrap" style="width:100%">
                <thead>
                    <tr>

                        <th scope="col">{{__('general.date')}}</th>
                        <th scope="col">{{__('sidebar.business')}}</th>
                        <th scope="col">{{__('sidebar.customers')}}</th>
                        <th scope="col">{{__('starter.package_plan')}}</th>
                        <th scope="col">{{__('starter.total')}}</th>
                        <th scope="col">{{__('general.status')}}</th>
                        <th scope="col">{{__('general.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions->sortByDesc('created_at') as $t)
                    <tr>

                        <td>{{substr($t->created_at,0,10)}} </td>
                        <td>{{$t->business->name ?? ''}} </td>
                        <td>{{$t->merchant->name ?? ''}}</td>
                        <td>{{$t->package->name ?? ''}}</td>
                        <td>
                            {{number_format($t->final_total)}}
                        </td>
                        <td>
                            @if($t->status == 'pending')
                            <span class="badge bg-warning text-azure-fg ">{{__('starter.pending')}}</span>
                            @endif

                            @if($t->status == 'process')
                            <span class="badge bg-info ">{{__('starter.process')}}</span>
                            @endif

                            @if($t->status == 'success')
                            <span class="badge bg-success text-azure-fg ">{{__('starter.complete')}}</span>
                            @endif

                            @if($t->status == 'rejected')
                            <span class="badge bg-red text-azure-fg ">{{__('starter.rejected')}}</span>
                            @endif
                        </td>
                        <td>
                            <a href="<?= route('transactions.detail', $t->id); ?>" class="btn btn-outline-info btn-icon fs-16 ">
                                <i class="bx bx-list-ol"></i>
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
@endsection


@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>

<script>
    $(document).ready(function() {

        $('#transactionData').DataTable({
            responsive: true,
            ordering: false,
            language: {
                searchPlaceholder: '{{__("starter.search_transaction")}}',
                sSearch: '',
            },
            "pageLength": 10,
        });

        $('.merchants').select2({
            placeholder: '{{__("customer.search")}}',
            ajax: {
                url: `/administrator/merchants/get-data`,
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id,
                            }
                        }),
                    }
                },
                cache: false,
            },
        });

    });
</script>
@endsection