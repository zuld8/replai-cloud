@extends('layouts.auth')

@section('content')

<style>
/* Fix: Force modal above backdrop when nested in overflow container */
#packageModal {
    position: fixed !important;
    z-index: 1060 !important;
}
.modal-backdrop {
    z-index: 1055 !important;
}
</style>
<style>.logo-container{display:none!important}</style>

<div class="auth-split-wrapper">

  {{-- RIGHT: Green Panel --}}
  <div class="auth-right">
    <div class="auth-right-inner">
      <div class="auth-right-badge">🎁 10 Hari Gratis — Tanpa Kartu Kredit</div>
      <h2 class="auth-right-title">Mulai Otomatisasi<br>Bisnis Anda<br>Sekarang!</h2>
      <p class="auth-right-desc">Bergabung dengan ribuan bisnis yang sudah menggunakan Replai untuk meningkatkan penjualan dan layanan pelanggan mereka.</p>
      <ul class="auth-feat-list">
        <li><span class="feat-check">✓</span> Setup dalam 5 menit</li>
        <li><span class="feat-check">✓</span> Free Trial tanpa kartu kredit</li>
        <li><span class="feat-check">✓</span> Support tim siap membantu</li>
        <li><span class="feat-check">✓</span> 10.000+ bisnis aktif</li>
      </ul>
    </div>
    <div class="auth-orb auth-orb-1"></div>
    <div class="auth-orb auth-orb-2"></div>
    <div class="auth-orb auth-orb-3"></div>
  </div>

  {{-- LEFT: Register Form --}}
  <div class="auth-left" style="flex:1;overflow-y:auto;max-height:100vh;">
    <div class="auth-topbar">
      <a href="{{route('login')}}" class="auth-brand">
        <img src="{{asset($internalSetting->logo)}}" alt="Replai" style="max-height:36px;">
      </a>
    </div>
    <div style="padding:20px 36px 40px;">

<div class="register-container">
    <!-- Stepper -->
    <div class="stepper">
        <div class="step active" id="stepper-1">
            <div class="step-number">1</div>
            <div class="step-label">{{__('auth.account_info')}}</div>
            <div class="step-line"></div>
        </div>
        <div class="step" id="stepper-2">
            <div class="step-number">2</div>
            <div class="step-label">{{__('auth.business_info')}}</div>
            <div class="step-line"></div>
        </div>
        <div class="step" id="stepper-3">
            <div class="step-number">3</div>
            <div class="step-label">{{__('auth.choose_package')}}</div>
        </div>
    </div>

    <!-- Register Card -->
    <div class="register-card">
        <form action="{{route('register.user')}}" method="POST" id="registerFormData" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: Info Akun -->
            <div class="step-content active" id="step-1">
                <div class="d-flex justify-content-between align-items-start">
                    <a href="{{route('login')}}" class="back-button">
                        <i class="fas fa-arrow-left"></i> {{__('auth.login')}}
                    </a>

                    <div class="card-logo">
                        <img src="{{asset($internalSetting->logo)}}" style="max-width:150px" />
                    </div>
                </div>

                <h1 class="card-title">{{__('auth.create_account')}}</h1>
                <p class="card-subtitle">{{__('auth.register_subtitle')}}</p>

                <div class="auth-title-section mb-3">
                    <x-validation-component></x-validation-component>
                </div>

                <div class="mb-3">
                    <label for="nama" class="form-label">{{__('auth.name_label')}}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama" name="name" value="{{old('name')}}" placeholder="{{__('auth.name_placeholder')}}" required>
                </div>

                <div class="mb-3">
                    <label for="whatsapp" class="form-label">{{__('auth.whatsapp_label')}}<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="tel" class="form-control" id="whatsapp" name="phone" value="{{old('phone')}}" placeholder="{{__('auth.whatsapp_placeholder')}}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{__('auth.email_label')}}<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="{{__('auth.email_placeholder')}}" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{__('auth.password_label')}}<span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="{{__('auth.password_placeholder')}}" required>
                    <div class="password-hint">{{__('auth.password_hint')}}</div>
                </div>

                <div class="mb-4">
                    <label for="confirm-password" class="form-label">{{__('auth.password_confirmation_label')}}<span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm-password" name="password_confirmation" placeholder="{{__('auth.password_confirmation_placeholder')}}" required>
                </div>

                <button type="button" class="btn btn-primary" id="next-step-1">{{__('auth.next')}}</button>

                <div class="login-link">
                    {{__('auth.have_account_login')}} <a href="{{route('login')}}">{{__('auth.login')}}</a>
                </div>
            </div>

            <!-- Step 2: Info Usaha -->
            <div class="step-content" id="step-2">
                <div class="d-flex justify-content-between align-items-start">
                    <a href="#" class="back-button" onclick="goToStep(1); return false;">
                        <i class="fas fa-arrow-left"></i> {{__('auth.previous')}}
                    </a>

                    <div class="card-logo">
                        <img src="{{asset($internalSetting->logo)}}" style="max-width:150px" />
                    </div>
                </div>

                <h1 class="card-title">{{__('auth.business_information')}}</h1>
                <p class="card-subtitle">{{__('auth.register_subtitle')}}</p>

                <div class="mb-3">
                    <label for="nama-usaha" class="form-label">{{__('auth.business_name')}}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama-usaha" name="business_name" value="{{old('business_name')}}" placeholder="{{__('auth.business_name_placeholder')}}" required>
                </div>

                <div class="mb-4">
                    <label for="kategori-usaha" class="form-label">{{__('auth.business_category')}}<span class="text-danger">*</span></label>
                    <select class="form-select" id="kategori-usaha" name="category" required>
                        <option value="">{{__('auth.choose_category')}}</option>
                        @foreach ($categories as $category)
                        <option value="{{$category->id}}" @if($category->id == old('category')) selected @endif>{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>

                <button type="button" class="btn btn-primary" id="next-step-2">{{__('auth.next')}}</button>

                <div class="login-link">
                    {{__('auth.have_account_login')}} <a href="{{route('login')}}">{{__('auth.login')}}</a>
                </div>
            </div>

            <!-- Step 3: Pilih Paket -->
            <div class="step-content" id="step-3">
                <div class="d-flex justify-content-between align-items-start">
                    <a href="#" class="back-button" onclick="goToStep(2); return false;">
                        <i class="fas fa-arrow-left"></i> {{__('auth.previous')}}
                    </a>

                    <div class="card-logo">
                        <img src="{{asset($internalSetting->logo)}}" style="max-width:150px" />
                    </div>
                </div>

                <h1 class="card-title">{{__('auth.select_package')}}</h1>
                <p class="card-subtitle">{{__('auth.register_subtitle')}}</p>

                <!-- Package Selection Button -->
                <div class="package-select-btn {{$packageDetail ? 'package-selected' : ''}}" id="package-btn" data-bs-toggle="modal" data-bs-target="#packageModal">
                    <div class="package-select-content">
                        <div class="package-icon">
                            <?= $packageDetail ? '<i class="fas fa-building text-white"></i>' : '<i class="fas fa-bolt"></i>'; ?>
                        </div>
                        <div class="package-text" id="selected-package-text">{{$packageDetail ? $packageDetail->name : __('auth.select_available_package')}} {{$packageDetail && $packageDetail->trial_version != 'yes' ? number_format($packageDetail->price) : ''}}</div>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </div>


                <input type="hidden" name="package_id" id="packageId" value="{{$packageDetail ? $packageDetail->id : ''}}">
                <input type="hidden" name="nominal" id="packageAmount" value="">

                <div id="payment-section" class="d-none">
                    <div class="mb-3">
                        <label class="form-label">{{__('auth.select_payment_method')}}</label>

                        @if($setting->method == 'duitku')
                        <input type="hidden" name="payment_method" value="duitku" />

                        <div class="mb-3">

                            @if(count($payments['va']) > 0)
                            <div class="mb-3">
                                <p class="fw-semibold mb-2 text-muted small">{{__('auth.virtual_account')}}</p>
                                <div class="row g-2">
                                    @foreach($payments['va'] as $payment)
                                    <div class="col-md-6">
                                        <div class="form-check payment-method-card border rounded p-3">
                                            <input
                                                id="payment-{{$payment->paymentMethod}}"
                                                name="to_bank"
                                                type="radio"
                                                value="{{$payment->paymentMethod}}"
                                                class="form-check-input ms-1"
                                                required>
                                            <label class="form-check-label ms-1" for="payment-{{$payment->paymentMethod}}">
                                                <span class="fw-semibold">{{$payment->paymentName}}</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(count($payments['wallet']) > 0)
                            <div class="mb-3">
                                <p class="fw-semibold mb-2 text-muted small">{{__('auth.e_wallet')}}</p>
                                <div class="row g-2">
                                    @foreach($payments['wallet'] as $payment)
                                    <div class="col-md-6">
                                        <div class="form-check payment-method-card border rounded p-3">
                                            <input
                                                id="payment-{{$payment->paymentMethod}}"
                                                name="to_bank"
                                                type="radio"
                                                value="{{$payment->paymentMethod}}"
                                                class="form-check-input ms-1"
                                                required>
                                            <label class="form-check-label ms-1" for="payment-{{$payment->paymentMethod}}">
                                                <span class="fw-semibold">{{$payment->paymentName}}</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(count($payments['qris']) > 0)
                            <div class="mb-3">
                                <p class="fw-semibold mb-2 text-muted small">{{__('auth.qris')}}</p>
                                <div class="row g-2">
                                    @foreach($payments['qris'] as $payment)
                                    <div class="col-md-6">
                                        <div class="form-check payment-method-card border rounded p-3">
                                            <input
                                                id="payment-{{$payment->paymentMethod}}"
                                                name="to_bank"
                                                type="radio"
                                                value="{{$payment->paymentMethod}}"
                                                class="form-check-input ms-1"
                                                required>
                                            <label class="form-check-label ms-1" for="payment-{{$payment->paymentMethod}}">
                                                <span class="fw-semibold">{{$payment->paymentName}}</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($setting->method == 'bank')
                        <input type="hidden" name="payment_method" value="bank" />
                        <div class="row">
                            @foreach ($banks as $b)
                            <div class="col-12 mb-2">
                                <div class="form-check shipping-method-container border p-3">
                                    <input id="bank-{{$b->id}}" name="to_bank" type="radio" value="{{$b->id}}" class="form-check-input" required>
                                    <label class="form-check-label w-100" for="bank-{{$b->id}}">
                                        <p class="mb-0 fw-semibold">{{$b->name}}</p>
                                        <p class="mb-0 text-muted">{{$b->number}} ({{$b->code}})</p>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 mb-3">
                                <label class="form-label">{{__('starter.insert_bank_name')}}</label>
                                <input class="form-control" name="bank_name" value="{{old('bank_name')}}" type="text" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">{{__('starter.insert_bank_number')}}</label>
                                <input class="form-control" name="bank_number" value="{{old('bank_number')}}" type="text" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">{{__('starter.upload_file')}}</label>
                                <input class="form-control" name="image" type="file" required>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="terms-text">
                        {{__('auth.terms_agreement')}} <a href="#">{{__('auth.terms_conditions')}}</a> {{__('auth.terms_thankyou')}}
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submit-btn">{{__('auth.select_and_register')}}</button>

                <div class="login-link">
                    {{__('auth.have_account_login')}} <a href="{{route('login')}}">{{__('auth.login')}}</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Package Selection Modal -->
<div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="packageModalLabel">{{__('auth.select_package_title')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach ($packages as $package)
                    <div class="col-md-4 mb-3">
                        <div class="package-card" onclick="selectPackage(this, '{{$package->id}}',<?= (float)$package->price; ?>, '{{$package->name}}', '{{$package->trial_version}}')">
                            <div class="package-header">
                                <div class="package-icon-modal {{ $loop->index == 0 ? 'starter' : ($loop->index == 1 ? 'professional' : 'enterprise') }}">
                                    <i class="fas {{ $loop->index == 0 ? 'fa-user' : ($loop->index == 1 ? 'fa-users' : 'fa-building') }}"></i>
                                </div>
                                <div class="package-price">
                                    {{$package->trial_version == 'yes' ? __('auth.package_free') : currency_format($package->price)}}
                                    <span class="package-period">{{__('auth.package_per')}} {{$package->days_option == 'limited' ? number_format($package->add_days) . ' ' . __('auth.package_days') : __('auth.package_forever')}}</span>
                                </div>
                                <div class="package-name">{{$package->name}}</div>
                                <div class="package-description">
                                    {{$package->trial_version == 'yes' ? __('auth.package_trial_desc') : __('auth.package_business_desc')}}
                                </div>
                            </div>
                            <ul class="package-features">
                                <li>
                                    <i class="fas {{$package->storage < 1 ? 'fa-times text-danger' : 'fa-check'}}"></i>
                                    {{__('auth.storage')}} ( {{$package->storage_name}} )
                                </li>
                                <li>
                                    <i class="fas {{$package->limit_user_option == 'yes' && $package->users_limit == 0 ? 'fa-times text-danger' : 'fa-check'}}"></i>
                                    {{__('auth.feature_users')}} ({{$package->limit_user_option == 'yes' ? number_format($package->users_limit) : __('auth.unlimited')}})
                                </li>
                                <li>
                                    <i class="fas {{$package->limit_device == 'yes' && $package->device_limit == 0 ? 'fa-times text-danger' : 'fa-check'}}"></i>
                                    {{__('auth.feature_wa_device')}} ({{$package->limit_device == 'yes' ? number_format($package->device_limit) : __('auth.unlimited')}})
                                </li>
                                <li>
                                    <i class="fas {{$package->limit_whatsapp_option == 'yes' && $package->whatsapp_limit == 0 ? 'fa-times text-danger' : 'fa-check'}}"></i>
                                    {{__('auth.feature_wa_blast')}} ({{$package->limit_whatsapp_option == 'yes' ? number_format($package->whatsapp_limit) : __('auth.unlimited')}} {{$package->whatsapp_priode ? '/' . $package->whatsapp_priode : ''}})
                                </li>
                                <li>
                                    <i class="fas {{$package->limit_email_option == 'yes' && $package->email_limit == 0 ? 'fa-times text-danger' : 'fa-check'}}"></i>
                                    {{__('auth.feature_email_blast')}} ({{$package->limit_email_option == 'yes' ? number_format($package->email_limit) : __('auth.unlimited')}} {{$package->email_priode ? '/' . $package->email_priode : ''}})
                                </li>
                                <li>
                                    <i class="fas {{$package->limit_template == 'yes' && $package->template_limit == 0 ? 'fa-times text-danger' : 'fa-check'}}"></i>
                                    {{__('auth.feature_message_template')}} ({{$package->limit_template == 'yes' ? number_format($package->template_limit) : __('auth.unlimited')}})
                                </li>
                                <li>
                                    <i class="fas {{$package->limit_ai_training == 'yes' && $package->ai_training_limit == 0 ? 'fa-times text-danger' : 'fa-check'}}"></i>
                                    {{__('auth.feature_ai_training')}} ({{$package->limit_ai_training == 'yes' ? number_format($package->ai_training_limit) : __('auth.unlimited')}})
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    Rag File {{$package->max_per_upload }}/Mb Per File & {{$package->max_total_rag}}/Mb Total
                                </li>
                                <li>
                                    <i class="fas {{$package->limit_chatbot == 'yes' && $package->chatbot_limit == 0 ? 'fa-times text-danger' : 'fa-check'}}"></i>
                                    {{__('auth.feature_chatbot')}} ({{$package->limit_chatbot == 'yes' ? number_format($package->chatbot_limit) : __('auth.unlimited')}})
                                </li>
                                <li>
                                    <i class="fas {{$package->livechat_limit == 'yes' && $package->limit_livechat == 0 ? 'fa-times text-danger' : 'fa-check'}}"></i>
                                    {{__('auth.feature_livechat')}} ({{$package->livechat_limit == 'yes' ? number_format($package->limit_livechat) : __('auth.unlimited')}})
                                </li>
                            </ul>
                            <button type="button" class="btn btn-primary btn-select-package">{{__('auth.select_package')}}</button>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>

    </div>{{-- padding wrapper --}}
  </div>{{-- auth-left --}}
</div>{{-- auth-split-wrapper --}}
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    // FIX: Teleport modal to body to avoid stacking context issues
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('packageModal');
        if (modal) {
            document.body.appendChild(modal);
        }
    });

    let currentStep = 1;
    let selectedPackage = null;

    // Translation strings from Laravel
    const trans = {
        alert_warning: "{{__('auth.alert_warning')}}",
        alert_fill_required: "{{__('auth.alert_fill_required')}}",
        alert_password_mismatch: "{{__('auth.alert_password_mismatch')}}",
        alert_password_mismatch_desc: "{{__('auth.alert_password_mismatch_desc')}}",
        alert_password_short: "{{__('auth.alert_password_short')}}",
        alert_password_short_desc: "{{__('auth.alert_password_short_desc')}}",
        alert_select_package: "{{__('auth.alert_select_package')}}",
        alert_select_package_desc: "{{__('auth.alert_select_package_desc')}}",
        alert_select_payment: "{{__('auth.alert_select_payment')}}",
        alert_select_payment_desc: "{{__('auth.alert_select_payment_desc')}}",
        alert_processing: "{{__('auth.alert_processing')}}",
        alert_processing_desc: "{{__('auth.alert_processing_desc')}}",
        alert_package_selected: "{{__('auth.alert_package_selected')}}",
        alert_package_selected_desc: "{{__('auth.alert_package_selected_desc')}}",
        package_free: "{{__('auth.package_free')}}",
    };

    // Step 1 Next Button
    document.getElementById('next-step-1').addEventListener('click', function(e) {
        e.preventDefault();

        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if (!document.getElementById('nama').value || !document.getElementById('whatsapp').value ||
            !document.getElementById('email').value || !password || !confirmPassword) {
            Swal.fire({
                icon: 'warning',
                title: trans.alert_warning,
                text: trans.alert_fill_required,
                confirmButtonColor: '#1a73a8'
            });
            return;
        }

        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: trans.alert_password_mismatch,
                text: trans.alert_password_mismatch_desc,
                confirmButtonColor: '#1a73a8'
            });
            return;
        }

        if (password.length < 8) {
            Swal.fire({
                icon: 'error',
                title: trans.alert_password_short,
                text: trans.alert_password_short_desc,
                confirmButtonColor: '#1a73a8'
            });
            return;
        }

        goToStep(2);
    });

    // Step 2 Next Button
    document.getElementById('next-step-2').addEventListener('click', function(e) {
        e.preventDefault();

        const namaUsaha = document.getElementById('nama-usaha').value;
        const kategoriUsaha = document.getElementById('kategori-usaha').value;

        if (!namaUsaha || !kategoriUsaha) {
            Swal.fire({
                icon: 'warning',
                title: trans.alert_warning,
                text: trans.alert_fill_required,
                confirmButtonColor: '#1a73a8'
            });
            return;
        }

        goToStep(3);
    });

    // Form Submit
    document.getElementById('submit-btn').addEventListener('click', function(e) {
        e.preventDefault();

        if (!selectedPackage) {
            Swal.fire({
                icon: 'warning',
                title: trans.alert_select_package,
                text: trans.alert_select_package_desc,
                confirmButtonColor: '#1a73a8'
            });
            return;
        }

        const packageAmount = parseFloat(document.getElementById('packageAmount').value);

        // Jika paket berbayar, cek payment method
        if (packageAmount > 0) {
            const selectPayment = document.querySelector('select[name="to_bank"]');
            const radioPayment = document.querySelector('input[name="to_bank"]:checked');

            if (selectPayment) {
                if (!selectPayment.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: trans.alert_select_payment,
                        text: trans.alert_select_payment_desc,
                        confirmButtonColor: '#1a73a8'
                    });
                    return;
                }
            } else if (radioPayment) {
                // Already selected
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: trans.alert_select_payment,
                    text: trans.alert_select_payment_desc,
                    confirmButtonColor: '#1a73a8'
                });
                return;
            }
        }

        Swal.fire({
            title: trans.alert_processing,
            text: trans.alert_processing_desc,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>' + trans.alert_processing;

        setTimeout(function() {
            document.getElementById('registerFormData').submit();
        }, 1000);
    });

    function goToStep(step) {
        document.querySelectorAll('.step-content').forEach(el => {
            el.classList.remove('active');
        });

        document.getElementById('step-' + step).classList.add('active');

        document.querySelectorAll('.step').forEach((el, index) => {
            el.classList.remove('active', 'completed');
            if (index + 1 < step) {
                el.classList.add('completed');
            } else if (index + 1 === step) {
                el.classList.add('active');
            }
        });

        currentStep = step;

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function selectPackage(element, packageId, amount, packageName, trialVersion) {
        document.querySelectorAll('.package-card').forEach(card => {
            card.classList.remove('active');
        });

        element.classList.add('active');

        selectedPackage = packageId;
        document.getElementById('packageId').value = packageId;
        document.getElementById('packageAmount').value = amount;

        const packageBtn = document.getElementById('package-btn');
        packageBtn.classList.add('package-selected');

        const packageText = document.getElementById('selected-package-text');
        const formattedPrice = trialVersion === 'yes' ? trans.package_free : '' + new Intl.NumberFormat('id-ID').format(amount);
        packageText.textContent = packageName + ' - ' + formattedPrice;

        const icons = ['fa-user', 'fa-users', 'fa-building'];
        const randomIcon = icons[Math.floor(Math.random() * icons.length)];
        packageBtn.querySelector('.package-icon').innerHTML = `<i class="fas ${randomIcon} text-white"></i>`;

        // Show/hide payment section
        const paymentSection = document.getElementById('payment-section');
        if (amount > 0) {
            paymentSection.classList.remove('d-none');
        } else {
            paymentSection.classList.add('d-none');
        }

        const modalEl = document.getElementById('packageModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
        // Ensure backdrop and body scroll are properly cleaned up
        modalEl.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }, { once: true });

        Swal.fire({
            icon: 'success',
            title: trans.alert_package_selected,
            text: packageName + ' ' + trans.alert_package_selected_desc,
            timer: 2000,
            showConfirmButton: false
        });
    }
</script>
@endsection