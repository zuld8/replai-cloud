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
            <form action="{{route('transaction.mua')}}" method="GET" class="card-body row">
                <div class="col-lg-3 col-sm-12">
                    <label class="form-label">{{__('sidebar.customers')}}</label>
                    <select class="form-control merchants" name="merchant">
                        <option value="">{{__('customer.search')}}</option>
                    </select>
                </div>
                <div class="col-lg-3 col-sm-12">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="process">Process</option>
                        <option value="success">Success</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-lg-3 col-sm-12">
                    <label class="form-label">{{__('transaction.start_date')}}</label>
                    <input type="datetime-local" class="form-control" name="start_date" value="{{request('start_date')}}">
                </div>
                <div class="col-lg-3 col-sm-12">
                    <label class="form-label">{{__('transaction.end_date')}}</label>
                    <div class="input-group">
                        <input type="datetime-local" class="form-control" name="end_date" value="{{request('end_date')}}">
                        <button class="btn btn-info mb-0" type="submit" id="button-addon2">
                            <i class="bx bx-search align-middle"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-xl-12 mt-2">
    <div class="card custom-card">
        <x-validation-component></x-validation-component>
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">
                <i class="bx bx-group me-2"></i>
                {{$page}}
            </div>
            <div>
                <span class="badge bg-label-info fs-6">
                    Total Transactions: {{ $transactions->count() }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="muaTransactionData" class="table table-bordered table-hover text-nowrap" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{__('general.date')}}</th>
                            <th scope="col">{{__('sidebar.business')}}</th>
                            <th scope="col">{{__('sidebar.customers')}}</th> 
                            <th scope="col">MUA Limit</th> 
                            <th scope="col">{{__('starter.total')}}</th>
                            <th scope="col">{{__('general.status')}}</th>
                            <th scope="col" class="text-center">{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{$t->created_at->format('d M Y')}}</div>
                                <small class="text-muted">{{$t->created_at->format('H:i:s')}}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2 bg-label-primary">
                                        <i class="bx bx-store"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{$t->business->name ?? '-'}}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2 bg-label-info">
                                        <i class="bx bx-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{$t->merchant->name ?? '-'}}</div>
                                        <small class="text-muted">{{$t->merchant->owner->email ?? '-'}}</small>
                                    </div>
                                </div>
                            </td> 
                            <td>
                                <span class="badge bg-primary fs-6">
                                    <i class="bx bx-group me-1"></i>
                                    {{number_format($t->new_order_mua_limit)}} Users
                                </span>
                            </td>
                            <td class="fw-semibold">
                                Rp {{number_format($t->final_total)}}
                            </td>
                            <td>
                                @if($t->status == 'pending')
                                <span class="badge bg-warning">
                                    <i class="bx bx-time-five me-1"></i>
                                    {{__('starter.pending')}}
                                </span>
                                @endif

                                @if($t->status == 'process')
                                <span class="badge bg-info">
                                    <i class="bx bx-loader-circle me-1"></i>
                                    {{__('starter.process')}}
                                </span>
                                @endif

                                @if($t->status == 'success')
                                <span class="badge bg-success">
                                    <i class="bx bx-check-circle me-1"></i>
                                    {{__('starter.complete')}}
                                </span>
                                @endif

                                @if($t->status == 'rejected')
                                <span class="badge bg-danger">
                                    <i class="bx bx-x-circle me-1"></i>
                                    {{__('starter.rejected')}}
                                </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{route('transaction.mua.detail', $t->id)}}" 
                                   class="btn btn-outline-info btn-sm" 
                                   data-bs-toggle="tooltip" 
                                   title="View Details">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bx bx-inbox bx-lg text-muted"></i>
                                <p class="text-muted mt-2">No MUA transactions found</p>
                            </td>
                        </tr>
                        @endforelse
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
        // Initialize DataTable
        $('#muaTransactionData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: 'Search MUA transactions...',
                sSearch: '',
            },
            "pageLength": 25,
            "order": [[0, "desc"]] // Sort by date descending
        });

        // Initialize Select2 for merchant search
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

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection