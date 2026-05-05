@extends('layouts.admin')

@section('styles')
<style>
    .register-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }



    .card-logo {
        text-align: center;
    }

    .card-logo img {
        max-width: 150px;
        height: auto;
    }

    .back-button {
        color: #6c757d;
        text-decoration: none;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: color 0.3s;
    }

    .back-button:hover {
        color: #1a73a8;
    }

    .card-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 20px;
        margin-top: 20px;
    }

    .card-subtitle {
        color: #6c757d;
        margin-bottom: 30px;
        font-size: 14px;
    }

    /* Step Content */
    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
    }

    /* Form Elements */
    .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control,
    .form-select {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #1a73a8;
        box-shadow: 0 0 0 3px rgba(26, 115, 168, 0.1);
    }

    .input-group-text {
        background-color: #f3f4f6;
        border: 1px solid #d1d5db;
        border-right: none;
        border-radius: 8px 0 0 8px;
        color: #6c757d;
        font-weight: 500;
    }

    .input-group .form-control {
        border-left: none;
        border-radius: 0 8px 8px 0;
    }

    .password-hint {
        font-size: 12px;
        color: #6c757d;
        margin-top: 4px;
    }

    /* Buttons */
    .btn-primary {
        background-color: #1a73a8;
        border: none;
        border-radius: 8px;
        padding: 14px;
        font-weight: 600;
        font-size: 16px;
        width: 100%;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #155a85;
    }

    .login-link {
        text-align: center;
        margin-top: 20px;
        color: #6c757d;
        font-size: 14px;
    }

    .login-link a {
        color: #1a73a8;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .register-card {
            padding: 30px 20px;
        }

        .card-title {
            font-size: 24px;
        }

        .step-label {
            font-size: 12px;
        }
    }
</style>
@endsection

@section('content')
<div class="row d-flex justify-content-center">
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>

    <div class="col-lg-8 col-sm-12">
        <div class="card custom-card">
            <form action="{{route('merchant.store')}}" method="POST" id="merchantFormData" class="card-body">
                @csrf

                <div class="step-content active" id="step-1">
 
                    <h1 class="card-title">{{__('auth.business_information')}}</h1> 

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

                </div>

                <!-- Step 2: Info Usaha -->
                <div class="step-content" id="step-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <a href="#" class="back-button" onclick="goToStep(1); return false;">
                            <i class="fas fa-arrow-left"></i> {{__('auth.previous')}}
                        </a>
                    </div>

                    <h1 class="card-title">{{__('auth.business_information')}}</h1> 

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

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save me-2"></i>{{__('general.add_data')}}
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentStep = 1;

    // Translations for alerts
    const trans = {
        alert_warning: "{{ __('auth.alert_warning') }}",
        alert_fill_required: "{{ __('auth.alert_fill_required') }}",
        alert_password_mismatch: "{{ __('auth.alert_password_mismatch') }}",
        alert_password_mismatch_desc: "{{ __('auth.alert_password_mismatch_desc') }}",
        alert_password_short: "{{ __('auth.alert_password_short') }}",
        alert_password_short_desc: "{{ __('auth.alert_password_short_desc') }}",
        alert_processing: "{{ __('auth.alert_processing') }}",
        alert_processing_desc: "{{ __('auth.alert_processing_desc') }}"
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

    // Form Submit
    document.getElementById('submit-btn').addEventListener('click', function(e) {
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
            document.getElementById('merchantFormData').submit();
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
</script>
@endsection