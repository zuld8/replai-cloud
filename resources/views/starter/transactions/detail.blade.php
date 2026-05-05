@extends('layouts.starter')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <x-validation-component></x-validation-component>
    </div>

    @if($transaction->status == 'pending')
    <!-- Form Payment -->
    <div class="col-lg-8">
        <form action="{{$setting->method == 'bank' ? route('starter.transactions.pay',$transaction->id) : route('starter.create.token',$transaction->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="card custom-card">
            @csrf
            <input type="hidden" name="additional_cost" value="0" id="addcost">
            
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-credit-card me-2"></i>
                    {{ __('starter.payment_info') }}
                </div>
            </div>

            <div class="card-body">
                @if($setting->method == 'bank')
                <!-- Section: Detail Transfer -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-transfer me-1"></i>
                        {{ __('starter.bank_transfer_detail') }}
                    </h6>
                    <div class="row">
                        <!-- Bank Name -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-buildings text-primary me-1"></i>
                                {{__('starter.insert_bank_name')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-building"></i>
                                </span>
                                <input class="form-control" 
                                       name="bank_name" 
                                       value="{{old('bank_name')}}" 
                                       type="text"
                                       placeholder="Contoh: BCA"
                                       required>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{ __('starter.sender_bank_name') }}
                            </small>
                        </div>

                        <!-- Bank Number -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-credit-card text-success me-1"></i>
                                {{__('starter.insert_bank_number')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-hash"></i>
                                </span>
                                <input class="form-control" 
                                       name="bank_number" 
                                       value="{{old('bank_number')}}" 
                                       type="text"
                                       placeholder="1234567890"
                                       required>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{ __('starter.sender_account_number') }}
                            </small>
                        </div>

                        <!-- Amount -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-money text-warning me-1"></i>
                                {{__('starter.insert_amount')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-dollar"></i>
                                </span>
                                <input class="form-control" 
                                       name="amount" 
                                       value="{{old('amount', $transaction->final_total)}}" 
                                       type="number"
                                       placeholder="{{$transaction->final_total}}"
                                       required>
                                <span class="input-group-text">Rp</span>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{ __('starter.transfer_amount') }}
                            </small>
                        </div>

                        <!-- Upload File -->
                        <div class="col-lg-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-upload text-info me-1"></i>
                                {{__('starter.upload_file')}}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-image"></i>
                                </span>
                                <input class="form-control" 
                                       name="image" 
                                       type="file"
                                       accept="image/*"
                                       required>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                {{ __('starter.payment_proof') }}
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                @endif

                <!-- Section: Choose Bank/Payment -->
                <div class="mb-3">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-wallet me-1"></i>
                        {{__('starter.choose_to_bank')}}
                    </h6>
                </div>

                <div class="row">
                    @if($setting->method == 'bank')
                    @foreach ($banks as $b)
                    @php $bankId = 'bank_' . $b->id; @endphp
                    <div class="col-lg-4 col-md-6 mb-3">
                        <input type="radio" class="btn-check" name="to_bank" id="{{ $bankId }}" value="{{ $b->id }}" autocomplete="off" required>
                        <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 payment-option" for="{{ $bankId }}">
                            <div class="mb-3">
                                <img src="{{ asset($b->logo) }}" alt="{{ $b->name }}" style="max-height: 50px; max-width: 100px;">
                            </div>
                            <div class="text-center">
                                <div class="fw-bold mb-1">{{ $b->number }}</div>
                                <small class="text-muted">{{ $b->name }}</small>
                            </div>
                        </label>
                    </div>
                    @endforeach
                    @endif

                    @if($setting->method == 'duitku')
                    @foreach(['va' => __('starter.virtual_account'), 'wallet' => __('starter.e_wallet'), 'qris' => __('starter.qris'), 'retail' => __('starter.retail')] as $key => $label)
                    @if(count($payments[$key]) > 0)
                    <div class="col-12 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="bx bx-radio-circle-marked me-1"></i>
                            {{ $label }}
                        </h6>
                        <div class="row">
                            @foreach($payments[$key] as $payment)
                            @php $paymentId = $key . '_' . $payment->paymentMethod; @endphp
                            <div class="col-lg-4 col-md-6 mb-3">
                                <input type="radio" class="btn-check" name="to_bank" id="{{ $paymentId }}" value="{{ $payment->paymentMethod }}" autocomplete="off" required>
                                <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 payment-option" for="{{ $paymentId }}">
                                    <div class="mb-3">
                                        <img src="{{ asset($payment->paymentImage) }}" alt="{{ $payment->paymentName }}" style="max-height: 50px; max-width: 100px;">
                                    </div>
                                    <div class="text-center">
                                        <div class="fw-bold mb-1">{{ $payment->paymentName }}</div>
                                        <small class="text-muted d-block mb-1">{{ $payment->paymentMethod }}</small>
                                        <span class="badge bg-label-warning">{{ __('starter.admin_fee') }}: {{currency_format($payment->totalFee)}}</span>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @endif
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="bx bx-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>{{ __('starter.payment_instructions') }}</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>{{ __('starter.payment_instruction_1') }}</li>
                            <li>{{ __('starter.payment_instruction_2') }}</li>
                            <li>{{ __('starter.payment_instruction_3') }}</li>
                            <li>{{ __('starter.payment_instruction_4') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-lock-alt"></i>
                    {{ __('starter.payment_secure') }}
                </small>
                <button class="btn btn-primary" type="submit">
                    <i class="bx bx-send me-1"></i>
                    {{__('starter.send_payment')}}
                </button>
            </div>
        </form>
    </div>
    @else
    <!-- Status Card -->
    <div class="col-lg-8">
        <div class="card custom-card">
            <div class="card-body text-center py-5">
                @if($transaction->status == 'process')
                <div class="mb-4">
                    <i class="bx bx-time-five text-warning" style="font-size: 5rem;"></i>
                </div>
                <h4 class="mb-3">{{__('starter.process_payment')}}</h4>
                <p class="text-muted">{{ __('starter.payment_processing') }}</p>
                <div class="alert alert-warning d-inline-block">
                    <i class="bx bx-info-circle me-1"></i>
                    {{ __('starter.payment_verification') }}
                </div>
                @endif

                @if($transaction->status == 'success')
                <div class="mb-4">
                    <i class="bx bx-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h4 class="mb-3">{{__('starter.complete_payment')}}</h4>
                <p class="text-muted">{{ __('starter.payment_success_msg') }}</p>
                <a href="{{ route('starter.business.index') }}" class="btn btn-primary mt-3">
                    <i class="bx bx-home me-1"></i>
                    {{ __('starter.back_to_business_list') }}
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Sidebar Summary -->
    <div class="col-lg-4">
        <div class="card custom-card border-primary mb-3">
            <div class="card-header ">
                <h6 class="mb-0">
                    <i class="bx bx-receipt me-2"></i>
                    {{ __('starter.payment_summary') }}
                </h6>
            </div>
            <div class="card-body">
                <!-- Package Info -->
                <div class="text-center mb-4 pb-3 border-bottom">
                    <div class="mb-2">
                        <span class="badge bg-label-primary fs-6">
                            {{$transaction->package->name ?? ''}}
                        </span>
                    </div>
                    <div class="d-flex justify-content-center align-items-baseline mb-2">
                        <h2 class="text-primary mb-0">
                            @if((int)($transaction->price ?? 0) > 0)
                           {{currency_format($transaction->price)}}
                            @else
                            {{ __('starter.package_free') }}
                            @endif
                        </h2>
                    </div>
                    <small class="text-muted">
                        {{$transaction->add_days > 0 ? number_format($transaction->add_days) . ' ' . __('starter.days') : __('starter.unlimited')}}
                    </small>
                </div>

                <!-- Features List -->
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex align-items-center">
                        <i class="bx bx-check-circle text-success me-2"></i>
                        <div>
                            <small class="text-muted d-block">{{ __('starter.users') }}</small>
                            <span class="fw-semibold">
                                {{$transaction->limit_user_option == 'yes' ? number_format($transaction->users_limit) : __('starter.unlimited')}}
                            </span>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="bx bx-check-circle text-success me-2"></i>
                        <div>
                            <small class="text-muted d-block">{{ __('starter.whatsapp_device') }}</small>
                            <span class="fw-semibold">
                                {{$transaction->limit_device == 'yes' ? number_format($transaction->device_limit) : __('starter.unlimited')}}
                            </span>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="bx bx-check-circle text-success me-2"></i>
                        <div>
                            <small class="text-muted d-block">{{ __('starter.ai_response') }}</small>
                            <span class="fw-semibold">
                                {{number_format($transaction->new_order_ai_response)}}
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="card custom-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bx bx-calculator me-2"></i>
                    {{ __('starter.payment_detail') }}
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">{{ __('starter.package_price') }}</span>
                        <span class="fw-semibold">{{currency_format($transaction->price)}}</span>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">{{ __('starter.tax_fee') }}</span>
                        <span class="fw-semibold">{{currency_format($transaction->tax)}}</span>
                    </li>
                    <li class="mb-3 pb-3 border-bottom"></li>
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="fw-bold">{{ __('starter.total_payment') }}</span>
                        <span class="fw-bold text-primary fs-5">{{currency_format($transaction->final_total)}}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            <i class="bx bx-calendar me-1"></i>
                            {{ __('starter.valid_until') }}
                        </span>
                        <span class="badge bg-label-info">{{substr($transaction->expire_date,0,10)}}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
/* Payment Option Styling */
.payment-option {
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 140px;
}

.payment-option:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-check:checked + .payment-option {
    background-color: #0f766e !important;
    border-color: #0f766e !important;
    color: #ffffff !important;
    box-shadow: 0 4px 16px rgba(15, 118, 110, 0.4);
}

.btn-check:checked + .payment-option .text-muted,
.btn-check:checked + .payment-option .badge {
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.2) !important;
}

.btn-check:checked + .payment-option .fw-bold {
    color: #ffffff !important;
}

/* Card hover effect */
.card.custom-card {
    transition: all 0.3s ease;
}

/* Icon animation for status */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.bx-time-five {
    animation: pulse 2s infinite;
}

/* Summary card styling */
.card.border-primary {
    border-width: 2px !important;
}
</style>
@endsection