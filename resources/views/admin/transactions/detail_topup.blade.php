@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/select2/select2.min.css')}}">
@endsection

@section('button')
<div class="btn-list">
    @if($transaction->status == 'pending' || $transaction->status == 'process')
    <a href="{{route('transactions.payment.approval', $transaction->id)}}" 
       class="btn btn-success d-none d-sm-inline-block approvebutton">
        <i class="bx bx-check-circle"></i>
        Approve Payment
    </a>
    @endif
    
    <a href="{{route('transaction.topup')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('transaction.back_to')}}
    </a>

    <a href="{{route('transaction.topup')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('transaction.back_to')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header d-md-flex d-block">
                <div class="h5 mb-0 d-sm-flex d-block align-items-center">
                    <div class="avatar avatar-lg bg-label-success me-3">
                        <i class="bx bx-coin-stack bx-lg"></i>
                    </div>
                    <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                        <div class="h6 fw-semibold mb-0">
                            AI Credit TopUp Reference: 
                            <span class="text-primary">#{{$transaction->ref_no}}</span>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-calendar me-1"></i>
                            {{$transaction->created_at->format('d F Y, H:i')}}
                        </small>
                    </div>
                </div>
                <div class="ms-auto mt-md-0 mt-2">
                    @if($transaction->status == 'success')
                    <span class="badge bg-success fs-6">
                        <i class="bx bx-check-circle me-1"></i>
                        Approved
                    </span>
                    @elseif($transaction->status == 'process')
                    <span class="badge bg-info fs-6">
                        <i class="bx bx-loader-circle me-1"></i>
                        In Review
                    </span>
                    @elseif($transaction->status == 'pending')
                    <span class="badge bg-warning fs-6">
                        <i class="bx bx-time-five me-1"></i>
                        Pending Payment
                    </span>
                    @else
                    <span class="badge bg-danger fs-6">
                        <i class="bx bx-x-circle me-1"></i>
                        Rejected
                    </span>
                    @endif
                </div>
            </div>
            
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-12 m-0">
                        <x-validation-component></x-validation-component>
                    </div>

                    <!-- Company & Customer Info -->
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body">
                                        <p class="text-muted mb-3 fw-semibold">
                                            <i class="bx bx-buildings me-1"></i>
                                            {{__('transaction.from_company')}}:
                                        </p>
                                        <p class="fw-bold mb-2 fs-5">{{$setting->app_name}}</p>
                                        <p class="mb-1 text-muted">
                                            <i class="bx bx-map me-1"></i>
                                            {{$setting->contact_address}}
                                        </p>
                                        <p class="mb-1 text-muted">
                                            <i class="bx bx-envelope me-1"></i>
                                            {{$setting->email_contect}}
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="bx bx-phone me-1"></i>
                                            {{$setting->phone_contact}}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-6 mt-sm-0 mt-3">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body">
                                        <p class="text-muted mb-3 fw-semibold">
                                            <i class="bx bx-store me-1"></i>
                                            {{__('sidebar.customers')}}:
                                        </p>
                                        <p class="fw-bold mb-2 fs-5">{{$transaction->business->name ?? '-'}}</p>
                                        <p class="text-muted mb-1">
                                            <i class="bx bx-map me-1"></i>
                                            {{$transaction->merchant->address ?? '-'}}
                                        </p>
                                        <p class="text-muted mb-1">
                                            <i class="bx bx-phone me-1"></i>
                                            {{$transaction->merchant->owner->phone ?? '-'}}
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="bx bx-envelope me-1"></i>
                                            {{$transaction->merchant->owner->email ?? '-'}}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-12 col-sm-12 mt-lg-0 mt-3">
                                <div class="card bg-success bg-gradient text-white border-0 h-100">
                                    <div class="card-body">
                                        <p class="text-white-50 mb-3 fw-semibold">
                                            <i class="bx bx-coin-stack me-1"></i>
                                            AI Credit Package:
                                        </p>
                                        <h2 class="text-white mb-2">
                                            {{number_format($transaction->new_order_ai_response)}}
                                        </h2>
                                        <p class="text-white-50 mb-0">AI Response Credits</p>
                                        <hr class="border-white-50 my-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-white-50">Total Amount:</span>
                                            <span class="fw-bold text-white fs-5">
                                                Rp {{number_format($transaction->final_total)}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Timeline -->
                    <div class="col-lg-12 mt-4">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="mb-3">
                                    <i class="bx bx-time me-2"></i>
                                    Transaction Timeline
                                </h6>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-primary me-2">
                                                <i class="bx bx-calendar-plus"></i>
                                            </div>
                                            <div>
                                                <p class="fw-semibold text-muted mb-0">{{__('transaction.create_date')}}:</p>
                                                <p class="fs-6 mb-0">
                                                    {{$transaction->created_at->format('d M Y')}}
                                                    <span class="text-muted fs-12">
                                                        {{$transaction->created_at->format('H:i')}}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-info me-2">
                                                <i class="bx bx-credit-card"></i>
                                            </div>
                                            <div>
                                                <p class="fw-semibold text-muted mb-0">{{__('transaction.payment_date')}}:</p>
                                                <p class="fs-6 mb-0">
                                                    @if($transaction->payment)
                                                        {{$transaction->payment->created_at->format('d M Y')}}
                                                        <span class="text-muted fs-12">
                                                            {{$transaction->payment->created_at->format('H:i')}}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">Awaiting Payment</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-success me-2">
                                                <i class="bx bx-check-circle"></i>
                                            </div>
                                            <div>
                                                <p class="fw-semibold text-muted mb-0">{{__('transaction.status')}}:</p>
                                                <p class="fs-6 mb-0 fw-semibold">
                                                    @if($transaction->status == 'success')
                                                        <span class="text-success">{{__('starter.complete')}}</span>
                                                    @elseif($transaction->status == 'process')
                                                        <span class="text-info">{{__('starter.process')}}</span>
                                                    @elseif($transaction->status == 'pending')
                                                        <span class="text-warning">{{__('starter.pending')}}</span>
                                                    @else
                                                        <span class="text-danger">Rejected</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Table -->
                    <div class="col-lg-12 mt-3">
                        <h6 class="mb-3">
                            <i class="bx bx-receipt me-2"></i>
                            Transaction Details
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60%">Description</th>
                                        <th class="text-center">AI Credits</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-label-success me-3">
                                                    <i class="bx bx-coin-stack"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-semibold">TopUp AI Response Credits</p>
                                                    <small class="text-muted">
                                                        Additional AI credits for chatbot responses
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-success fs-6">
                                                +{{number_format($transaction->new_order_ai_response)}} Credits
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            Rp {{number_format($transaction->final_total)}}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2" class="text-end">{{__('starter.total')}}:</th>
                                        <th class="text-end fs-5 text-success">
                                            Rp {{number_format($transaction->final_total)}}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    @if($transaction->payment)
    <div class="col-xl-12 mt-4">
        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-credit-card me-2"></i>
                    {{__('transaction.payments')}}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>{{__('transaction.from_bank')}}</th>
                                <th>{{__('transaction.to_bank')}}</th>
                                <th>{{__('transaction.bank_sending')}}</th>
                                <th>{{__('transaction.amount_payment')}}</th>
                                <th>{{__('transaction.proof_deliver')}}</th>
                                <th class="text-center">{{__('general.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{$transaction->payment->bank_name}}</div>
                                    <small class="text-muted">Sender's Bank</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{$transaction->payment->bank->name ?? '-'}}</div>
                                    <small class="text-muted">Recipient's Bank</small>
                                </td>
                                <td>
                                    <code>{{$transaction->payment->bank_number}}</code>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        Rp {{number_format($transaction->payment->amount)}}
                                    </span>
                                </td>
                                <td>
                                    @if($transaction->payment->file)
                                    <a href="{{asset($transaction->payment->file)}}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show me-1"></i>
                                        View
                                    </a>
                                    <a href="{{asset($transaction->payment->file)}}" 
                                       download 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bx bx-download me-1"></i>
                                        Download
                                    </a>
                                    @else
                                    <span class="text-muted">No proof</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($transaction->status == 'process')
                                    <a href="{{route('transactions.payment.approval', $transaction->id)}}" 
                                       class="btn btn-sm btn-success approvebutton">
                                        <i class="bx bx-check-circle me-1"></i>
                                        Approve
                                    </a>
                                    <a href="{{route('transactions.payment.rejected', $transaction->payment->id)}}" 
                                       class="btn btn-sm btn-danger deletebutton">
                                        <i class="bx bx-x-circle me-1"></i>
                                        Reject
                                    </a>
                                    @else
                                    <span class="badge bg-label-success">
                                        <i class="bx bx-check"></i> Verified
                                    </span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(".approvebutton").on("click", function(e) {
        e.preventDefault();
        const href = $(this).attr("href");
        Swal.fire({
            title: "Approve AI Credit TopUp?",
            text: "This will add " + "{{number_format($transaction->new_order_ai_response)}}" + " AI credits to the account.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Approve!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.value) {
                document.location.href = href;
            }
        });
    });

    $(".deletebutton").on("click", function(e) {
        e.preventDefault();
        const href = $(this).attr("href");
        Swal.fire({
            title: "{{__('general.are_you_sure')}}",
            text: "{{__('transaction.warning_action')}}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, Reject",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.value) {
                document.location.href = href;
            }
        });
    });
</script>
@endsection