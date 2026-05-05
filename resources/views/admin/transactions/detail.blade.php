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
    
    <a href="{{route('transactions')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('transaction.back_to')}}
    </a>

    <a href="{{route('transactions')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('transaction.back_to')}}">
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
                    <div class="avatar avatar-lg bg-label-info me-3">
                        <i class="bx bx-package bx-lg"></i>
                    </div>
                    <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                        <div class="h6 fw-semibold mb-0">
                            Subscription Reference: 
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
                        Active
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
                                <div class="card bg-info bg-gradient text-white border-0 h-100">
                                    <div class="card-body">
                                        <p class="text-white-50 mb-3 fw-semibold">
                                            <i class="bx bx-package me-1"></i>
                                            Subscription Package:
                                        </p>
                                        <h2 class="text-white mb-2">
                                            {{$transaction->package->name ?? 'Package'}}
                                        </h2>
                                        <p class="text-white-50 mb-0">
                                            @if($transaction->add_days > 0)
                                                {{$transaction->add_days}} Days Duration
                                            @else
                                                Lifetime Package
                                            @endif
                                        </p>
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
                                    <div class="col-lg-3">
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
                                    
                                    <div class="col-lg-3">
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

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-warning me-2">
                                                <i class="bx bx-calendar-check"></i>
                                            </div>
                                            <div>
                                                <p class="fw-semibold text-muted mb-0">Expire Date:</p>
                                                <p class="fs-6 mb-0">
                                                    @if($transaction->expire_date)
                                                        {{date('d M Y', strtotime($transaction->expire_date))}}
                                                    @else
                                                        <span class="badge bg-label-success">Lifetime</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-3">
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
                                        <th width="45%">{{__('package.name')}}</th>
                                        <th class="text-center">{{__('starter.price')}}</th>
                                        <th class="text-center">{{__('package.add_days')}}</th>
                                        <th class="text-end">{{__('starter.subtotal')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-label-info me-3">
                                                    <i class="bx bx-package"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-semibold">{{$transaction->package->name ?? 'Package'}}</p>
                                                    <small class="text-muted">
                                                        @if($transaction->days_option == 'limited')
                                                            Limited Time Package
                                                        @else
                                                            Lifetime Package
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-semibold">
                                            Rp {{number_format($transaction->package->price ?? 0)}}
                                        </td>
                                        <td class="text-center">
                                            @if($transaction->add_days > 0)
                                                <span class="badge bg-label-primary">
                                                    {{number_format($transaction->add_days)}} Days
                                                </span>
                                            @else
                                                <span class="badge bg-label-success">
                                                    <i class="bx bx-infinite"></i> Lifetime
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold">
                                            Rp {{number_format($transaction->package->price ?? 0)}}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th colspan="3" class="text-end">{{__('starter.subtotal')}}:</th>
                                        <th class="text-end">Rp {{number_format($transaction->package->price ?? 0)}}</th>
                                    </tr>
                                    @if($transaction->tax > 0)
                                    <tr class="table-light">
                                        <th colspan="3" class="text-end">
                                            {{__('starter.tax')}} ({{number_format($transaction->tax)}}%):
                                        </th>
                                        <th class="text-end">
                                            Rp {{number_format(($transaction->package->price ?? 0) * $transaction->tax / 100)}}
                                        </th>
                                    </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <th colspan="3" class="text-end fs-5">{{__('starter.total')}}:</th>
                                        <th class="text-end fs-5 text-info">
                                            Rp {{number_format($transaction->final_total)}}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Package Features -->
                    <div class="col-lg-12 mt-3">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="mb-3">
                                    <i class="bx bx-list-check me-2"></i>
                                    Package Features & Limits
                                </h6>
                                <div class="row">
                                    @if($transaction->users_limit > 0)
                                    <div class="col-md-3 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-primary me-2">
                                                <i class="bx bx-user"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Users</small>
                                                <strong>{{number_format($transaction->users_limit)}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($transaction->device_limit > 0)
                                    <div class="col-md-3 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-success me-2">
                                                <i class="bx bx-devices"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Devices</small>
                                                <strong>{{number_format($transaction->device_limit)}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($transaction->whatsapp_limit > 0)
                                    <div class="col-md-3 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-info me-2">
                                                <i class="bx bxl-whatsapp"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">WhatsApp</small>
                                                <strong>{{number_format($transaction->whatsapp_limit)}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($transaction->ai_response > 0)
                                    <div class="col-md-3 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-warning me-2">
                                                <i class="bx bx-coin-stack"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">AI Credits</small>
                                                <strong>{{number_format($transaction->ai_response)}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($transaction->chatbot_limit > 0)
                                    <div class="col-md-3 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-danger me-2">
                                                <i class="bx bx-bot"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Chatbots</small>
                                                <strong>{{number_format($transaction->chatbot_limit)}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($transaction->storage > 0)
                                    <div class="col-md-3 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-secondary me-2">
                                                <i class="bx bx-hdd"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Storage</small>
                                                <strong>
                                                    @if($transaction->storage < 1000)
                                                        {{$transaction->storage}} MB
                                                    @else
                                                        {{number_format($transaction->storage / 1000, 1)}} GB
                                                    @endif
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($transaction->mua_limit > 0)
                                    <div class="col-md-3 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-label-primary me-2">
                                                <i class="bx bx-group"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">MUA Limit</small>
                                                <strong>{{number_format($transaction->mua_limit)}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
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
            title: "Approve Subscription?",
            html: `
                <div class="text-start">
                    <p>This will activate the subscription with following details:</p>
                    <ul class="list-unstyled">
                        <li><i class="bx bx-package text-primary"></i> Package: <strong>{{$transaction->package->name ?? 'Package'}}</strong></li>
                        @if($transaction->add_days > 0)
                        <li><i class="bx bx-calendar text-info"></i> Duration: <strong>{{$transaction->add_days}} Days</strong></li>
                        @endif
                        <li><i class="bx bx-user text-success"></i> Customer: <strong>{{$transaction->business->name ?? '-'}}</strong></li>
                    </ul>
                </div>
            `,
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