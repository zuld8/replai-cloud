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
            <form action="{{route('transaction.storage')}}" method="GET" class="card-body row">
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
                        <th>{{__('general.date')}}</th>
                        <th>{{__('transaction.from_company')}}</th>
                        <th>{{__('transaction.duration')}}</th>
                        <th>{{__('transaction.price')}}</th>
                        <th>{{__('transaction.status')}}</th>
                        <th>{{__('transaction.valid_until')}}</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr>
                        <td>
                            <div class="text-muted">
                                {{$transaction->created_at->format('d/m/Y')}}
                            </div>
                            <small class="text-muted">{{$transaction->created_at->format('H:i')}}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">

                                <div>
                                    <div class="fw-bold">{{$transaction->package->name ?? 'N/A'}}</div>
                                    <small class="text-muted">{{$transaction->storage_name ?? 'N/A'}}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($transaction->days_option == 'limited')
                            <span class="badge bg-info">{{$transaction->add_days}} {{__('transaction.days')}}</span>
                            @else
                            <span class="badge bg-success-lt">{{__('transaction.lifetime')}}</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{currency_format($transaction->price)}}</div>
                        </td>
                        <td>
                            @if($transaction->status == 'success')
                            <span class="badge bg-success">{{__('transaction.active')}}</span>
                            @elseif($transaction->status == 'pending')
                            <span class="badge bg-warning">{{__('transaction.pending')}}</span>
                            @elseif($transaction->status == 'rejected')
                            <span class="badge bg-danger">{{__('transaction.cancelled')}}</span>
                            @else
                            <span class="badge bg-secondary">{{$transaction->status}}</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->expire_date)
                            <div class="text-muted">{{date('d/m/Y', strtotime($transaction->expire_date))}}</div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="<?= route('transaction.storage.detail', $transaction->id); ?>" class="btn btn-outline-info btn-icon fs-16 ">
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