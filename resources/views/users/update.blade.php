@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <x-validation-component></x-validation-component>
    </div>

    <!-- Form Edit Profil -->
    <div class="col-lg-8 col-sm-12">
        <form action="<?= route('users.edit', $user->id); ?>" enctype="multipart/form-data" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-user-circle me-2"></i>{{__('auth.user_profile_info')}}
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
                        <label for="image" class="custom-file-upload" id="uploadArea" style="<?= $user->image_data ? 'display: none;' : ''; ?>">
                            <div class="upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <div class="upload-text">{{__('auth.upload_file')}}</div>
                            <div class="file-name" id="fileName"></div>
                            <input type="file" id="image" name="image" accept="image/*">
                        </label>

                        <div class="image-preview-container {{ $user->image_data ? 'show' : '' }}" id="previewContainer">
                            <img src="{{ $user->image_data ? asset($user->image_data) : '' }}" alt="Preview" class="image-preview" id="imagePreview">
                            <br>
                            <button type="button" class="remove-image" id="removeImage">
                                <i class="bx bx-trash me-1"></i>{{__('auth.remove_image')}}
                            </button>
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
                            <input class="form-control" name="name" value="{{old('name',$user->name)}}" placeholder="{{__('auth.name_placeholder')}}" type="text" required>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.name_hint')}}
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
                                <option value="male" @if($user->gender == 'male') selected @endif>{{__('auth.male')}}</option>
                                <option value="female" @if($user->gender == 'female') selected @endif>{{__('auth.female')}}</option>
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
                        <select class="form-control business" name="business[]" multiple="multiple">
                            @foreach ($businesses as $business)
                            <option value="<?= $business->id; ?>"
                                {{ in_array($business->id, explode(',',$user->business_id)) ? 'selected' : '' }}>
                                <?= $business->name; ?>
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.business_hint_multi')}}
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
                            <input class="form-control" name="phone" id="waPhone" value="{{ old('phone', $user->phone) }}" placeholder="{{__('auth.whatsapp_placeholder_full')}}" type="text" required>
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
                            <input class="form-control" name="email" value="{{old('email',$user->email)}}" placeholder="{{__('auth.email_placeholder')}}" type="email" required>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.email_hint_system')}}
                        </small>
                    </div>
                </div>

                <!-- Role Permission -->
                @php
                    $isPrimaryUser = $user->merchant && (optional($user->merchant->owner)->id === $user->id);
                @endphp
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label fw-semibold">
                        <i class="bx bx-lock me-1"></i>Role
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        @if($isPrimaryUser)
                            {{-- 🔒 Primary/owner: role locked to Administrator --}}
                            <div class="input-group">
                                <span class="input-group-text bg-warning-subtle">
                                    <i class="bx bx-lock-alt text-warning"></i>
                                </span>
                                <div class="form-control d-flex align-items-center justify-content-between"
                                     style="background:#fffbf0;cursor:not-allowed;min-height:38px;">
                                    <span class="fw-semibold">{{ $user->role_access?->name ?? 'Administrator' }}</span>
                                    <span class="badge bg-warning text-dark ms-2" style="font-size:0.7rem;">
                                        <i class="bx bx-lock-alt me-1"></i>Terkunci
                                    </span>
                                </div>
                            </div>
                            <input type="hidden" name="role" value="{{ $user->role_id }}">
                            <small class="text-muted d-block mt-1">
                                <i class="bx bx-shield-alt me-1 text-warning"></i>
                                Role akun utama tidak dapat diubah untuk keamanan sistem.
                            </small>
                        @else
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-lock"></i>
                                </span>
                                <select class="form-control" name="role" required>
                                    <option value="">{{__('general.choose')}}</option>
                                    @foreach($roles as $role)
                                    <option value="{{$role->id}}" @if($role->id == $user->role_id) selected @endif >{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

                <!-- Platform Access -->
                <div class="row mb-3 mt-2" style="border-top:1px dashed #E2E8F0;padding-top:1rem">
                    <div class="col-sm-3">
                        <label class="col-form-label fw-semibold">
                            <i class="bx bx-broadcast me-1"></i>Akses Platform
                        </label>
                        <small class="d-block text-muted mt-1">
                            <i class="bx bx-info-circle me-1"></i>Platform yang bisa diakses oleh agent ini
                        </small>
                    </div>
                    <div class="col-sm-9">

                        {{-- WhatsApp Personal --}}
                        @if(isset($platforms['devices']) && $platforms['devices']->count())
                        <div class="mb-3">
                            <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;background:#25D366;display:inline-block"></span>
                                WhatsApp Personal
                            </div>
                            @foreach($platforms['devices'] as $d)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="devices[]"
                                       value="{{ $d->id }}" id="udev_{{ $d->id }}" {{ isset($userPlatforms['devices']) && in_array($d->id, $userPlatforms['devices']) ? 'checked' : '' }}>
                                <label class="form-check-label fs-13" for="udev_{{ $d->id }}">
                                    {{ $d->name }}
                                    <span class="badge {{ $d->status=='active' ? 'bg-success-transparent' : 'bg-secondary-transparent' }} ms-1 fs-11">{{ $d->status }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- WABA --}}
                        @if(isset($platforms['wabas']) && $platforms['wabas']->count())
                        <div class="mb-3">
                            <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;background:#0D6EFD;display:inline-block"></span>
                                WhatsApp Business API
                            </div>
                            @foreach($platforms['wabas'] as $w)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="wabas[]"
                                       value="{{ $w->id }}" id="uwaba_{{ $w->id }}" {{ isset($userPlatforms['wabas']) && in_array($w->id, $userPlatforms['wabas']) ? 'checked' : '' }}>
                                <label class="form-check-label fs-13" for="uwaba_{{ $w->id }}">
                                    +{{ $w->phone }}
                                    <span class="badge bg-primary-transparent ms-1 fs-11">WABA</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Telegram --}}
                        @if(isset($platforms['telegrams']) && $platforms['telegrams']->count())
                        <div class="mb-3">
                            <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;background:#2CA5E0;display:inline-block"></span>
                                Telegram
                            </div>
                            @foreach($platforms['telegrams'] as $t)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="telegrams[]"
                                       value="{{ $t->id }}" id="utg_{{ $t->id }}" {{ isset($userPlatforms['telegrams']) && in_array($t->id, $userPlatforms['telegrams']) ? 'checked' : '' }}>
                                <label class="form-check-label fs-13" for="utg_{{ $t->id }}">{{ $t->name }}</label>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Instagram --}}
                        @if(isset($platforms['instagrams']) && $platforms['instagrams']->count())
                        <div class="mb-3">
                            <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;background:#E1306C;display:inline-block"></span>
                                Instagram DM
                            </div>
                            @foreach($platforms['instagrams'] as $ig)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="instagrams[]"
                                       value="{{ $ig->id }}" id="uig_{{ $ig->id }}" {{ isset($userPlatforms['instagrams']) && in_array($ig->id, $userPlatforms['instagrams']) ? 'checked' : '' }}>
                                <label class="form-check-label fs-13" for="uig_{{ $ig->id }}">{{ $ig->name }}</label>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Messenger --}}
                        @if(isset($platforms['messengers']) && $platforms['messengers']->count())
                        <div class="mb-3">
                            <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;background:#0866FF;display:inline-block"></span>
                                Facebook Messenger
                            </div>
                            @foreach($platforms['messengers'] as $m)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="messengers[]"
                                       value="{{ $m->id }}" id="ums_{{ $m->id }}" {{ isset($userPlatforms['messengers']) && in_array($m->id, $userPlatforms['messengers']) ? 'checked' : '' }}>
                                <label class="form-check-label fs-13" for="ums_{{ $m->id }}">{{ $m->name }}</label>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Live Chat --}}
                        @if(isset($platforms['livechats']) && $platforms['livechats']->count())
                        <div class="mb-3">
                            <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;background:#6366F1;display:inline-block"></span>
                                Widget Live Chat
                            </div>
                            @foreach($platforms['livechats'] as $lc)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="livechats[]"
                                       value="{{ $lc->id }}" id="ulc_{{ $lc->id }}" {{ isset($userPlatforms['livechats']) && in_array($lc->id, $userPlatforms['livechats']) ? 'checked' : '' }}>
                                <label class="form-check-label fs-13" for="ulc_{{ $lc->id }}">{{ $lc->name }}</label>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(!isset($platforms) || collect($platforms)->flatten()->isEmpty())
                        <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Belum ada platform terhubung untuk bisnis ini.</small>
                        @endif
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
                        <i class="bx bx-save me-1"></i>{{__('auth.save_changes')}}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Form Ubah Password -->
    <div class="col-lg-4 col-sm-12">
        <form action="<?= route('users.password', $user->id); ?>" method="POST" class="card custom-card">
            @csrf
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-lock-alt me-2"></i>{{__('auth.change_password')}}
                </div>
            </div>
            <div class="card-body">
                <!-- New Password -->
                <div class="row mb-3">
                    <label class="col-12 col-form-label fw-semibold">
                        <i class="bx bx-lock me-1"></i>{{__('auth.new_password')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-key"></i>
                            </span>
                            <input class="form-control" name="password" id="newPassword" placeholder="{{__('auth.password_placeholder_min')}}" type="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                <i class="bx bx-hide" id="eyeIconNew"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.new_password_hint')}}
                        </small>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="row mb-3">
                    <label class="col-12 col-form-label fw-semibold">
                        <i class="bx bx-lock-open me-1"></i>{{__('auth.password_confirmation_new')}}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bx bx-check-shield"></i>
                            </span>
                            <input class="form-control" name="confirm" id="confirmPassword" placeholder="{{__('auth.password_confirmation_placeholder_new')}}" type="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="bx bx-hide" id="eyeIconConfirm"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-info-circle me-1"></i>{{__('auth.password_confirmation_hint')}}
                        </small>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i>{{__('auth.change_password')}}
                </button>
            </div>
        </form>

        <!-- User Info Card -->
        <div class="card custom-card mt-3">
            <div class="card-header">
                <div class="card-title">
                    <i class="bx bx-info-circle me-2"></i>{{__('auth.account_info_title')}}
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-lg avatar-rounded me-3">
                        <img src="{{asset($user->image_data)}}" alt="{{$user->name}}">
                    </div>
                    <div>
                        <h6 class="mb-1">{{$user->name}}</h6>
                        <p class="text-muted fs-12 mb-0">{{$user->email}}</p>
                    </div>
                </div>
                <hr class="my-3">
                <div class="mb-2">
                    <small class="text-muted d-block mb-1">
                        <i class="bx bx-id-card me-1"></i>{{__('auth.user_id')}}
                    </small>
                    <strong>{{$user->id}}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block mb-1">
                        <i class="bx bx-phone me-1"></i>{{__('auth.whatsapp')}}
                    </small>
                    <strong>{{$user->phone}}</strong>
                </div>
                <div>
                    <small class="text-muted d-block mb-1">
                        <i class="bx bx-user-pin me-1"></i>{{__('auth.gender')}}
                    </small>
                    <span class="badge {{ $user->gender == 'male' ? 'bg-primary-transparent' : 'bg-pink-transparent' }}">
                        <i class="bx {{ $user->gender == 'male' ? 'bx-male' : 'bx-female' }} me-1"></i>
                        {{ $user->gender == 'male' ? __('auth.male') : __('auth.female') }}
                    </span>
                </div>
            </div>
        </div>
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

    // Toggle Password Visibility for New Password
    document.getElementById('toggleNewPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('newPassword');
        const eyeIcon = document.getElementById('eyeIconNew');

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

    // Toggle Password Visibility for Confirm Password
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmInput = document.getElementById('confirmPassword');
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

    // Password match validation
    document.getElementById('confirmPassword').addEventListener('input', function() {
        const password = document.getElementById('newPassword').value;
        const confirm = this.value;

        if (password !== confirm && confirm !== '') {
            this.setCustomValidity('{{__("auth.password_mismatch")}}');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
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
</script>
@endsection