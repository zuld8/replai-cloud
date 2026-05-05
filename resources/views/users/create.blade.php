@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
<style>
/* ═══ CARD HEADER ═══ */
form.card .card-header {
    background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%) !important;
    border-bottom: 1px solid #BFDBFE !important;
    padding: 1rem 1.5rem !important;
}
form.card .card-header .card-title {
    color: #1D4ED8 !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
}

/* ═══ COMPACT CIRCULAR PHOTO UPLOAD ═══ */
.custom-file-upload {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    width: 110px !important;
    height: 110px !important;
    border-radius: 50% !important;
    border: 2.5px dashed #BAE6FD !important;
    background: #F0F9FF !important;
    margin: 0 !important;
    padding: 0 !important;
    cursor: pointer !important;
    transition: border-color 0.2s, background 0.2s !important;
    text-align: center !important;
}
.custom-file-upload:hover {
    border-color: #0EA5E9 !important;
    background: #E0F2FE !important;
}
.custom-file-upload .upload-icon svg {
    width: 28px !important;
    height: 28px !important;
    stroke: #0EA5E9 !important;
    display: block !important;
}
.custom-file-upload .upload-text {
    font-size: 0.62rem !important;
    color: #0EA5E9 !important;
    font-weight: 600 !important;
    margin-top: 4px !important;
    line-height: 1.2 !important;
}
.custom-file-upload .file-name {
    display: none !important;
}
.custom-file-upload input[type="file"] {
    position: absolute !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    pointer-events: none !important;
}

/* ═══ CIRCULAR PREVIEW ═══ */
.image-preview-container {
    display: none;
    flex-direction: column;
    align-items: center;
}
.image-preview-container.show {
    display: flex !important;
}
.image-preview {
    width: 110px !important;
    height: 110px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    border: 3px solid #BFDBFE !important;
    box-shadow: 0 4px 16px rgba(14,165,233,0.18) !important;
}
.remove-image {
    margin-top: 8px !important;
    font-size: 0.7rem !important;
    color: #EF4444 !important;
    background: none !important;
    border: 1px solid #FCA5A5 !important;
    border-radius: 20px !important;
    padding: 2px 12px !important;
    cursor: pointer !important;
    transition: background 0.15s !important;
}
.remove-image:hover {
    background: #FEF2F2 !important;
}

/* ═══ PHOTO ROW — center vertically ═══ */
#uploadArea, #previewContainer {
    margin-top: 4px !important;
}

/* ═══ INPUT GROUP ICONS ═══ */
.input-group-text {
    color: #0EA5E9 !important;
    background: #F0F9FF !important;
    border-color: #E2E8F0 !important;
    min-width: 38px !important;
    justify-content: center !important;
}

/* ═══ FORM FIELDS ═══ */
.form-control:focus, .form-select:focus {
    border-color: #0EA5E9 !important;
    box-shadow: 0 0 0 3px rgba(14,165,233,0.1) !important;
}

/* ═══ LABELS ═══ */
.col-form-label.fw-semibold {
    color: #374151 !important;
    font-size: 0.875rem !important;
}

/* ═══ SECTION SEPARATOR via spacing ═══ */
.row.mb-3:nth-child(4) {
    padding-top: 0.5rem !important;
    border-top: 1px dashed #E2E8F0 !important;
    margin-top: 0.5rem !important;
}
.row.mb-3:nth-child(7) {
    padding-top: 0.5rem !important;
    border-top: 1px dashed #E2E8F0 !important;
    margin-top: 0.5rem !important;
}

/* ═══ CARD FOOTER ═══ */
.card-footer {
    background: #F8FAFC !important;
    border-top: 1px solid #E2E8F0 !important;
}

/* ═══ SUBMIT BUTTON ═══ */
.card-footer .btn-primary {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
    box-shadow: 0 4px 12px rgba(14,165,233,0.25) !important;
}

/* ═══ HINT TEXT ═══ */
small.text-muted {
    font-size: 0.72rem !important;
    margin-top: 3px !important;
    display: inline-block !important;
}
</style>
@endsection

@section('content')
<div class="row d-flex justify-content-center">
    <div class="col-xl-9">
        <x-validation-component></x-validation-component>
        <form action="<?= route('users.store'); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-user-plus me-2"></i>{{__('auth.user_profile_info')}}
                </div>
            </div>
            <div class="card-body">
                <!-- Foto Profil -->
                <div class="row mb-4">
                    <div class="col-sm-3">
                        <label class="col-form-label fw-semibold">
                            <i class="bx bx-image me-1"></i>{{__('auth.profile_photo')}}
                        </label>
                        <small class="d-block text-muted mt-1">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.profile_photo_hint')}}
                        </small>
                    </div>
                    <div class="col-sm-9">
                        <label for="image" class="custom-file-upload" id="uploadArea">
                            <div class="upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <div class="upload-text">{{__('auth.upload_file')}}</div>
                            <div class="file-name" id="fileName"></div>
                            <input type="file" id="image" name="image" accept="image/*">
                        </label>

                        <div class="image-preview-container" id="previewContainer">
                            <img src="" alt="Preview" class="image-preview" id="imagePreview">
                            <br>
                            <button type="button" class="remove-image" id="removeImage">{{__('auth.remove_image')}}</button>
                        </div>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-semibold">
                        <i class="bx bx-user me-1"></i>{{__('auth.full_name')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-user-circle"></i>
                            </span>
                            <input class="form-control" name="name" value="{{old('name')}}" placeholder="{{__('auth.full_name_placeholder')}}" type="text" required>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.full_name_hint')}}
                        </small>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-semibold">
                        <i class="bx bx-male-female me-1"></i>{{__('auth.gender_label')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-user-pin"></i>
                            </span>
                            <select class="form-control" name="gender" required>
                                <option value="">{{__('auth.gender_placeholder')}}</option>
                                <option value="male">{{__('auth.male')}}</option>
                                <option value="female">{{__('auth.female')}}</option>
                            </select>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.gender_hint')}}
                        </small>
                    </div>
                </div>

                <!-- Akses Bisnis -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-semibold">
                        <i class="bx bx-briefcase me-1"></i>{{__('auth.business_access')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control business" name="business[]" multiple="multiple" required>
                            @foreach ($businesses as $business)
                            <option value="<?= $business->id; ?>" selected><?= $business->name; ?></option>
                            @endforeach
                        </select>
                        {{-- business pre-selected = owner's accessible businesses --}}
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.business_hint')}}
                        </small>
                    </div>
                </div>

                <!-- No. WhatsApp -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-semibold">
                        <i class="bx bxl-whatsapp me-1"></i>{{__('auth.whatsapp_number')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-phone"></i>
                            </span>
                            <input class="form-control" name="phone" id="waPhone" value="{{ old('phone') }}" placeholder="{{__('auth.whatsapp_placeholder_full')}}" type="text" required>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.whatsapp_hint')}}
                        </small>
                    </div>
                </div>

                <!-- Alamat Email -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-semibold">
                        <i class="bx bx-envelope me-1"></i>{{__('auth.email_address')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-at"></i>
                            </span>
                            <input class="form-control" name="email" value="{{old('email')}}" placeholder="{{__('auth.email_placeholder_full')}}" type="email" required>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.email_hint_login')}}
                        </small>
                    </div>
                </div>

                <!-- Password -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-semibold">
                        <i class="bx bx-lock me-1"></i>{{__('auth.password')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-key"></i>
                            </span>
                            <input class="form-control" name="password" id="password" placeholder="{{__('auth.password_placeholder_min')}}" type="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bx bx-hide" id="eyeIcon"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.password_hint_full')}}
                        </small>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-semibold">
                        <i class="bx bx-lock-open me-1"></i>{{__('auth.password_confirmation')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-check-shield"></i>
                            </span>
                            <input class="form-control" name="confirm" id="confirm" placeholder="{{__('auth.password_confirmation_placeholder')}}" type="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirm">
                                <i class="bx bx-hide" id="eyeIconConfirm"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.password_confirmation_hint')}}
                        </small>
                    </div>
                </div>

                <!-- Role Permission -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-semibold">
                        <i class="bx bx-lock me-1"></i>Role
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-lock"></i>
                            </span>
                            <select class="form-control" name="role" required>
                                <option value="">{{__('general.choose')}}</option>
                                @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option> 
                                @endforeach
                            </select>
                        </div> 
                    </div>
                </div>

            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle me-1"></i>{{__('auth.required_fields')}}
                </small>
                <div class="d-flex gap-2">
                    <a href="{{ route('users') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x me-1"></i>{{__('auth.cancel')}}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>{{__('auth.save_data')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.business').select2({
            placeholder: "{{__('auth.business_placeholder')}}...",
            allowClear: true
        });
    });

    // Toggle Password Visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('bx-hide');
            eyeIcon.classList.add('bx-show');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('bx-show');
            eyeIcon.classList.add('bx-hide');
        }
    });

    document.getElementById('toggleConfirm').addEventListener('click', function() {
        const confirmInput = document.getElementById('confirm');
        const eyeIconConfirm = document.getElementById('eyeIconConfirm');

        if (confirmInput.type === 'password') {
            confirmInput.type = 'text';
            eyeIconConfirm.classList.remove('bx-hide');
            eyeIconConfirm.classList.add('bx-show');
        } else {
            confirmInput.type = 'password';
            eyeIconConfirm.classList.remove('bx-show');
            eyeIconConfirm.classList.add('bx-hide');
        }
    });


    // Image Upload Preview
    const imageInput = document.getElementById('image');
    const uploadArea = document.getElementById('uploadArea');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const fileName = document.getElementById('fileName');
    const removeImageBtn = document.getElementById('removeImage');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('{{__("auth.file_too_large")}}');
                this.value = '';
                return;
            }

            // Show file name
            fileName.textContent = file.name;

            // Read and preview image
            const reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                previewContainer.classList.add('show');
                uploadArea.style.display = 'none';
            };

            reader.readAsDataURL(file);
        }
    });

    // Remove image
    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.src = '';
        fileName.textContent = '';
        previewContainer.classList.remove('show');
        uploadArea.style.display = 'inline-block';
    });

    // Password match validation
    document.getElementById('confirm').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirm = this.value;

        if (password !== confirm && confirm !== '') {
            this.setCustomValidity('{{__("auth.password_mismatch")}}');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });
</script>
@endsection