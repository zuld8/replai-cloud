@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <x-validation-component></x-validation-component>
    </div>

    @if($transaction->status == 'pending')
    <!-- Form Payment -->
    <div class="col-lg-12">
        <form action="{{$setting->method == 'bank' ? route('storage.store',$transaction->id) : route('storage.create.token',$transaction->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="card custom-card">
            @csrf
            
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-hdd me-2"></i>
                    Pembayaran Storage Tambahan
                </div>
            </div>

            <div class="card-body">
                @if($setting->method == 'bank')
                <!-- Section: Detail Transfer -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bx bx-transfer me-1"></i>
                        Detail Transfer Bank
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
                                Nama bank pengirim Anda
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
                                Nomor rekening pengirim
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
                                       value="{{old('amount')}}" 
                                       type="number"
                                       placeholder="Masukkan nominal transfer"
                                       required>
                                <span class="input-group-text">Rp</span>
                            </div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                Nominal yang Anda transfer
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
                                Bukti transfer dalam format gambar (JPG, PNG)
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
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <input type="radio" class="btn-check" name="to_bank" id="bank_{{$b->id}}" value="{{$b->id}}" autocomplete="off" required>
                        <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 payment-option" for="bank_{{$b->id}}">
                            <div class="mb-3">
                                <img src="{{asset($b->logo)}}" alt="{{$b->name}}" style="max-height: 50px; max-width: 100px;">
                            </div>
                            <div class="text-center">
                                <div class="fw-bold mb-1">{{$b->number}}</div>
                                <small class="text-muted d-block">{{$b->name}}</small>
                                <small class="text-muted">({{$b->code}})</small>
                            </div>
                        </label>
                    </div>
                    @endforeach
                    @endif

                    @if($setting->method == 'duitku')
                    <!-- Virtual Account -->
                    @if(count($payments['va']) > 0)
                    <div class="col-12 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="bx bx-radio-circle-marked me-1"></i>
                            Virtual Account
                        </h6>
                        <div class="row">
                            @foreach($payments['va'] as $payment)
                            @php $paymentId = 'va_' . $payment->paymentMethod; @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <input type="radio" class="btn-check" name="to_bank" id="{{ $paymentId }}" value="{{ $payment->paymentMethod }}" autocomplete="off" required>
                                <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 payment-option" for="{{ $paymentId }}">
                                    <div class="mb-3">
                                        <img src="{{ asset($payment->paymentImage) }}" alt="{{ $payment->paymentName }}" style="max-height: 50px; max-width: 100px;">
                                    </div>
                                    <div class="text-center">
                                        <div class="fw-bold mb-1">{{ $payment->paymentName }}</div>
                                        <small class="text-muted d-block mb-1">{{ $payment->paymentMethod }}</small>
                                        <span class="badge bg-label-warning">Admin: {{currency_format($payment->totalFee)}}</span>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- E-Wallet -->
                    @if(count($payments['wallet']) > 0)
                    <div class="col-12 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="bx bx-radio-circle-marked me-1"></i>
                            E-Wallet
                        </h6>
                        <div class="row">
                            @foreach($payments['wallet'] as $payment)
                            @php $paymentId = 'wallet_' . $payment->paymentMethod; @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <input type="radio" class="btn-check" name="to_bank" id="{{ $paymentId }}" value="{{ $payment->paymentMethod }}" autocomplete="off" required>
                                <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 payment-option" for="{{ $paymentId }}">
                                    <div class="mb-3">
                                        <img src="{{ asset($payment->paymentImage) }}" alt="{{ $payment->paymentName }}" style="max-height: 50px; max-width: 100px;">
                                    </div>
                                    <div class="text-center">
                                        <div class="fw-bold mb-1">{{ $payment->paymentName }}</div>
                                        <small class="text-muted d-block mb-1">{{ $payment->paymentMethod }}</small>
                                        <span class="badge bg-label-warning">Admin: {{currency_format($payment->totalFee)}}</span>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- QRIS -->
                    @if(count($payments['qris']) > 0)
                    <div class="col-12 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="bx bx-radio-circle-marked me-1"></i>
                            QRIS
                        </h6>
                        <div class="row">
                            @foreach($payments['qris'] as $payment)
                            @php $paymentId = 'qris_' . $payment->paymentMethod; @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <input type="radio" class="btn-check" name="to_bank" id="{{ $paymentId }}" value="{{ $payment->paymentMethod }}" autocomplete="off" required>
                                <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 payment-option" for="{{ $paymentId }}">
                                    <div class="mb-3">
                                        <img src="{{ asset($payment->paymentImage) }}" alt="{{ $payment->paymentName }}" style="max-height: 50px; max-width: 100px;">
                                    </div>
                                    <div class="text-center">
                                        <div class="fw-bold mb-1">{{ $payment->paymentName }}</div>
                                        <small class="text-muted d-block mb-1">{{ $payment->paymentMethod }}</small>
                                        <span class="badge bg-label-warning">Admin: {{currency_format($payment->totalFee)}}</span>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Retail -->
                    @if(count($payments['retail']) > 0)
                    <div class="col-12 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="bx bx-radio-circle-marked me-1"></i>
                            Retail
                        </h6>
                        <div class="row">
                            @foreach($payments['retail'] as $payment)
                            @php $paymentId = 'retail_' . $payment->paymentMethod; @endphp
                            <div class="col-lg-6 mb-3">
                                <input type="radio" class="btn-check" name="to_bank" id="{{ $paymentId }}" value="{{$payment->paymentMethod}}" autocomplete="off" required>
                                <label class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-between p-3 payment-option-retail" for="{{ $paymentId }}">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-store-alt fs-4 me-3"></i>
                                        <div>
                                            <div class="fw-bold">{{$payment->paymentName}}</div>
                                            <small class="text-muted">{{$payment->paymentMethod}}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">{{currency_format($payment->totalFee)}}</div>
                                        <small class="text-muted">Biaya Admin</small>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endif
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="bx bx-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>Informasi Pembayaran Storage:</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>Storage tambahan akan aktif setelah pembayaran diverifikasi</li>
                            <li>Transfer sesuai dengan nominal yang tertera</li>
                            <li>Upload bukti transfer yang jelas dan terbaca</li>
                            <li>Verifikasi pembayaran dalam 1x24 jam</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-lock-alt"></i>
                    Pembayaran Aman & Terenkripsi
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
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body text-center py-5">
                @if($transaction->status == 'process')
                <div class="mb-4">
                    <i class="bx bx-time-five text-warning" style="font-size: 5rem;"></i>
                </div>
                <h4 class="mb-3">{{__('starter.process_payment')}}</h4>
                <p class="text-muted">Pembayaran storage Anda sedang dalam proses verifikasi</p>
                <div class="alert alert-warning d-inline-block">
                    <i class="bx bx-info-circle me-1"></i>
                    Storage akan aktif setelah verifikasi selesai (1x24 jam)
                </div>
                @endif

                @if($transaction->status == 'success')
                <div class="mb-4">
                    <i class="bx bx-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h4 class="mb-3">{{__('starter.complete_payment')}}</h4>
                <p class="text-muted">Storage tambahan berhasil diaktifkan!</p>
                <div class="alert alert-success d-inline-block">
                    <i class="bx bx-check-circle me-1"></i>
                    Kapasitas storage Anda telah ditambahkan
                </div>
                <div class="mt-3">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="bx bx-home me-1"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>


@endsection

@section('scripts')
@endsection