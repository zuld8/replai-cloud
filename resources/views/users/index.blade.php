@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/pages/user.css')}}?v={{ time() }}">
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

/* ── PLATFORM CHIPS (inline, no-cache) ────────────── */
.platform-chips{display:flex;gap:5px;justify-content:center;flex-wrap:wrap;margin:.55rem 0 .25rem}
.platform-chip{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.65rem;font-weight:700;letter-spacing:.02em;white-space:nowrap}
.pc-dot{width:6px;height:6px;border-radius:50%;display:inline-block;flex-shrink:0}
.pchip-device{background:#DCFCE7!important;color:#15803D!important}.pchip-device .pc-dot{background:#15803D!important}
.pchip-waba{background:#DBEAFE!important;color:#1D4ED8!important}.pchip-waba .pc-dot{background:#1D4ED8!important}
.pchip-telegram{background:#E0F2FE!important;color:#0369A1!important}.pchip-telegram .pc-dot{background:#0369A1!important}
.pchip-instagram{background:#FCE7F3!important;color:#BE185D!important}.pchip-instagram .pc-dot{background:#BE185D!important}
.pchip-messenger{background:#EDE9FE!important;color:#6D28D9!important}.pchip-messenger .pc-dot{background:#6D28D9!important}
.pchip-livechat{background:#FEF3C7!important;color:#92400E!important}.pchip-livechat .pc-dot{background:#92400E!important}
.card-divider{border:none;border-top:1px solid #F1F5F9;margin:.6rem 0 .4rem}

/* ── USER CARD (inline) ───────────────────────────── */
.user-card{border:1px solid #E0EEFF!important;border-radius:16px!important;box-shadow:0 4px 16px rgba(0,80,200,.09),0 1px 4px rgba(0,0,0,.05)!important;transition:transform .2s ease,box-shadow .2s ease!important;overflow:hidden!important}
.user-card:hover{transform:translateY(-6px)!important;box-shadow:0 14px 36px rgba(0,102,204,.18),0 2px 8px rgba(0,0,0,.06)!important;border-color:#A8CAFF!important}
.user-card::before{content:'';display:block;height:3px;background:linear-gradient(90deg,#0055CC,#00AAFF,#0055CC);background-size:200% 100%;animation:ua-shimmer 3s ease-in-out infinite}
@keyframes ua-shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
.user-card h6{font-size:.93rem!important;font-weight:700!important;color:#1E293B!important}
.user-card .btn-outline-primary{border-color:#0066CC!important;color:#0066CC!important;border-radius:10px!important;font-size:.79rem!important;font-weight:600!important;height:36px!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;padding:0 .9rem!important;transition:all .15s!important}
.user-card .btn-outline-primary:hover{background:linear-gradient(135deg,#0055CC,#0099FF)!important;color:#fff!important;border-color:transparent!important}
.user-card .btn-outline-danger{border-radius:10px!important;height:36px!important;width:36px!important;padding:0!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;font-size:.9rem!important;flex-shrink:0!important}
.user-card .avatar-status{position:absolute!important;bottom:3px!important;right:3px!important;width:11px!important;height:11px!important;border-radius:50%!important;background:#22C55E!important;border:2px solid #fff!important}

/* ── MODAL (inline) ───────────────────────────────── */
#addUserModal .modal-header,#editUserModal .modal-header{background:linear-gradient(135deg,#0055AA,#0099FF)!important;color:#fff!important}
#addUserModal .modal-content,#editUserModal .modal-content{border:none!important;border-radius:14px!important;overflow:hidden!important;box-shadow:0 20px 60px rgba(0,0,0,.18)!important}
#addUserModal .input-group-text,#editUserModal .input-group-text{background:#EFF6FF!important;border-color:#BFDBFE!important;color:#0066CC!important}
#addUserModal .form-control:focus,#editUserModal .form-control:focus{border-color:#0099FF!important;box-shadow:0 0 0 3px rgba(0,153,255,.13)!important}
#addUserModal .modal-footer,#editUserModal .modal-footer{background:#F8FAFC!important;border-top:1px solid #E2E8F0!important}
#addUserModal .btn-primary,#editUserModal .btn-primary{background:linear-gradient(135deg,#0055CC,#0099FF)!important;border:none!important;border-radius:10px!important;font-weight:600!important;box-shadow:0 4px 12px rgba(0,102,204,.3)!important}
.modal .form-check-input:checked{background-color:#0066CC!important;border-color:#0066CC!important}

/* ── HEADER & BUTTONS ────────────────────────────── */
.card.custom-card>.card-body.py-3{border-left:4px solid #0066CC!important}
#searchUser{border-radius:10px!important;border-color:#BFDBFE!important}
#searchUser:focus{border-color:#0099FF!important;box-shadow:0 0 0 3px rgba(0,153,255,.12)!important}
.btn-primary{background:linear-gradient(135deg,#0055CC,#0099FF)!important;border:none!important;border-radius:10px!important;font-weight:600!important;box-shadow:0 4px 14px rgba(0,102,204,.3)!important;transition:transform .15s,box-shadow .15s!important}
.btn-primary:hover{transform:translateY(-1px)!important;box-shadow:0 6px 20px rgba(0,102,204,.4)!important}

/* FIX 3: Uniform card border */
.user-card{border:1.5px solid #D1E4FF!important;border-top:none!important}
.user-card::before{height:4px!important;border-radius:0!important}
.col-xl-3 .card.custom-card.user-card{border-top:none!important}
/* FIX 4: Edit-only card — full width Edit button */
.user-card .btn-list .btn-outline-primary:only-child{width:100%!important}
/* Equal button heights */
.user-card .btn-list .btn-outline-primary,
.user-card .btn-list .btn-outline-danger{height:36px!important;display:inline-flex!important;align-items:center!important;justify-content:center!important}
.user-card .btn-list .btn-outline-primary{flex:1!important;font-weight:600!important;border-radius:10px!important;font-size:.79rem!important;border-color:#0066CC!important;color:#0066CC!important;transition:all .15s!important}
.user-card .btn-list .btn-outline-primary:hover{background:linear-gradient(135deg,#0055CC,#0099FF)!important;color:#fff!important;border-color:transparent!important;box-shadow:0 4px 14px rgba(0,102,204,.3)!important}
.user-card .btn-list .btn-outline-danger{width:36px!important;flex-shrink:0!important;border-radius:10px!important;padding:0!important;font-size:.9rem!important;transition:all .15s!important}
.user-card .btn-list .btn-outline-danger:hover{background:#EF4444!important;color:#fff!important;border-color:#EF4444!important;box-shadow:0 4px 12px rgba(239,68,68,.3)!important}
/* Breathing room */
.user-card .btn-list{margin-top:14px!important;gap:8px!important}

/* ══ FINAL POLISH: No stripe, full border + premium shadow ══ */
/* Remove top stripe animation */
.user-card::before { display: none !important; }

/* Full consistent border + floating shadow */
.user-card {
    border: 1.5px solid #DDE8FF !important;
    border-radius: 16px !important;
    box-shadow:
        0 2px 6px rgba(0, 60, 180, 0.06),
        0 6px 20px rgba(0, 80, 200, 0.08),
        0 1px 3px rgba(0, 0, 0, 0.04) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease !important;
    background: #fff !important;
}
.user-card:hover {
    transform: translateY(-6px) !important;
    border-color: #A8C8FF !important;
    box-shadow:
        0 8px 24px rgba(0, 80, 200, 0.14),
        0 16px 40px rgba(0, 60, 180, 0.10),
        0 2px 8px rgba(0, 0, 0, 0.06) !important;
}
</style>
@endsection

@section('button')
<div class="btn-list">
    @can('human-agents.tambah')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bx bx-plus-circle me-1"></i>
        {{__('auth.add_human_agent')}}
    </button>
    @endcan
</div>
@endsection

@section('content')
<div class="row">
    <!-- Header Info -->
    <div class="col-12 mb-3">
        <x-validation-component></x-validation-component>
    </div>

    <!-- Stats Summary (Optional) -->
    <div class="col-12 mb-4">
        <div class="card custom-card">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="bx bx-group me-2 text-primary"></i>{{$page}}
                        </h5>
                        <small class="text-muted">{{__('auth.total_human_agents', ['count' => count($users)])}}</small>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        <div class="input-group w-auto d-inline-flex" style="max-width: 300px;">
                            <span class="input-group-text bg-light">
                                <i class="bx bx-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchUser" placeholder="{{__('auth.search_placeholder')}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Cards Grid -->
    @forelse ($users as $user)
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4 user-item" data-name="{{strtolower($user->name)}}" data-email="{{strtolower($user->email)}}">
        <div class="card custom-card user-card h-100">
            <div class="card-body text-center p-4">
                <!-- Avatar with Status — colored initials when no custom photo -->
                <div class="avatar-wrapper mb-3" style="position:relative;display:inline-block;">
                    @if($user->image_data !== 'images/user.png')
                        <span class="avatar avatar-xxl avatar-rounded">
                            <img src="{{asset($user->image_data)}}" alt="{{$user->name}}" class="rounded-circle" style="width:72px;height:72px;object-fit:cover;">
                        </span>
                    @else
                        @php
                            $words    = array_filter(explode(' ', $user->name));
                            $initials = strtoupper(substr(array_values($words)[0], 0, 1) . (count($words) > 1 ? substr(array_values($words)[1], 0, 1) : ''));
                            $palette  = ['#0EA5E9','#8B5CF6','#EC4899','#10B981','#3B82F6','#F59E0B','#06B6D4','#EF4444','#14B8A6','#F97316'];
                            $bg       = $palette[ord($user->name[0]) % count($palette)];
                        @endphp
                        <div style="width:72px;height:72px;border-radius:50%;background:{{$bg}};display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.35rem;color:#fff;letter-spacing:-0.02em;box-shadow:0 6px 18px {{$bg}}66;flex-shrink:0;">
                            {{$initials}}
                        </div>
                    @endif
                    <span class="avatar-status" style="position:absolute;bottom:3px;right:3px;width:11px;height:11px;border-radius:50%;background:#22C55E;border:2.5px solid #fff;display:block;"></span>
                </div>

                <!-- User Name -->
                <h6 class="mb-1 fw-semibold" style="font-size:.93rem;font-weight:700;color:#1E293B;">
                    {{$user->name}}
                </h6>

                <!-- Email -->
                <p class="user-info-line mb-1" style="font-size:.73rem;color:#64748B;">
                    <i class="bx bx-envelope me-1"></i>{{$user->email}}
                </p>

                <!-- Phone -->
                @if($user->phone)
                <p class="user-info-line mb-2" style="font-size:.73rem;color:#64748B;">
                    <i class="bx bxl-whatsapp me-1"></i>{{$user->phone}}
                </p>
                @endif

                <!-- Role Badge -->
                @if($user->role === 'admin')
                <div class="mb-2">
                    <span class="badge bg-danger-transparent">
                        <i class="bx bx-shield me-1"></i>Super Admin
                    </span>
                </div>
                @elseif($user->role_access)
                <div class="mb-2">
                    <span class="badge bg-primary-transparent">
                        <i class="bx bx-user-check me-1"></i>{{$user->role_access->name}}
                    </span>
                </div>
                @endif

                <!-- Gender Badge -->
                <div class="mb-3">
                    @if($user->gender == 'male')
                    <span class="badge gender-badge" style="background:#1D4ED8!important;color:#fff!important;font-size:.69rem;padding:.3em .75em;border-radius:20px;font-weight:600;">
                        <i class="bx bx-male me-1"></i>{{__('auth.male')}}
                    </span>
                    @else
                    <span class="badge gender-badge" style="background:#DB2777!important;color:#fff!important;font-size:.69rem;padding:.3em .75em;border-radius:20px;font-weight:600;">
                        <i class="bx bx-female me-1"></i>{{__('auth.female')}}
                    </span>
                    @endif
                </div>

                <!-- Platform Chips -->
                @php $userPlatformMap = $platformMap[$user->id] ?? []; @endphp
                @if(!empty($userPlatformMap))
                <hr class="card-divider">
                <div class="platform-chips">
                    @if(isset($userPlatformMap['device']))
                    <span class="platform-chip pchip-device" title="WhatsApp Personal">
                        <span class="pc-dot"></span>WA
                    </span>
                    @endif
                    @if(isset($userPlatformMap['waba']))
                    <span class="platform-chip pchip-waba" title="WhatsApp Business API">
                        <span class="pc-dot"></span>WABA
                    </span>
                    @endif
                    @if(isset($userPlatformMap['telegram']))
                    <span class="platform-chip pchip-telegram" title="Telegram">
                        <span class="pc-dot"></span>TG
                    </span>
                    @endif
                    @if(isset($userPlatformMap['instagram']))
                    <span class="platform-chip pchip-instagram" title="Instagram DM">
                        <span class="pc-dot"></span>IG
                    </span>
                    @endif
                    @if(isset($userPlatformMap['messenger']))
                    <span class="platform-chip pchip-messenger" title="Facebook Messenger">
                        <span class="pc-dot"></span>FB
                    </span>
                    @endif
                    @if(isset($userPlatformMap['livechat']))
                    <span class="platform-chip pchip-livechat" title="Live Chat">
                        <span class="pc-dot"></span>Chat
                    </span>
                    @endif
                </div>
                @endif

                <!-- Action Buttons --> 
                <div class="btn-list d-flex gap-2 justify-content-center" style="margin-top:14px!important;">
                 
                    <button type="button"
                            class="btn btn-outline-primary btn-sm flex-fill"
                            title="{{__('auth.edit')}}"
                            onclick="openEditModal('{{ $user->id }}')">
                        <i class="bx bx-edit-alt me-1"></i>{{__('auth.edit')}}
                    </button>
                   
                        @if($user->merchant)
                            @if($user->merchant->owner->id != $user->id && my_user()->id != $user->id)
                            <a href="{{route('users.delete', $user->id)}}" 
                               class="btn btn-outline-danger btn-sm deletebutton"
                               title="{{__('auth.delete')}}">
                                <i class="bx bx-trash"></i>
                            </a>
                            @endif
                        @else
                            @if(my_user()->id != $user->id)
                            <a href="{{route('users.delete', $user->id)}}" 
                               class="btn btn-outline-danger btn-sm deletebutton"
                               title="{{__('auth.delete')}}">
                                <i class="bx bx-trash"></i>
                            </a>
                            @endif
                        @endif 
                </div> 
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-body text-center py-5">
                <i class="bx bx-user-x display-4 text-muted mb-3"></i>
                <h5 class="mb-2">{{__('auth.no_human_agents')}}</h5>
                <p class="text-muted mb-4">{{__('auth.no_human_agents_desc')}}</p>
                @can('human-agents.tambah')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bx bx-plus-circle me-1"></i>{{__('auth.add_human_agent')}}
                </button>
                @else
                <small class="text-muted">
                    <i class="bx bx-lock-alt me-1"></i>{{__('auth.no_permission_to_add')}}
                </small>
                @endcan
            </div>
        </div>
    </div>
    @endforelse

    <!-- No Results Message (Hidden by default) -->
    <div class="col-12 d-none" id="noResults">
        <div class="card custom-card">
            <div class="card-body text-center py-5">
                <i class="bx bx-search-alt display-4 text-muted mb-3"></i>
                <h5 class="mb-2">{{__('auth.no_results')}}</h5>
                <p class="text-muted">{{__('auth.no_results_desc')}}</p>
            </div>
        </div>
    </div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel"><i class="bx bx-user-plus me-2"></i>{{__('auth.add_human_agent')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= route('users.store'); ?>" enctype="multipart/form-data" method="POST" >
            @csrf
            <div class="d-none">
                <div class="card-title">
                    <i class="bx bx-user-plus me-2"></i>{{__('auth.user_profile_info')}}
                </div>
            </div>
            <div class="modal-body">
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
                                       value="{{ $d->id }}" id="cdev_{{ $d->id }}">
                                <label class="form-check-label fs-13" for="cdev_{{ $d->id }}">
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
                                       value="{{ $w->id }}" id="cwaba_{{ $w->id }}">
                                <label class="form-check-label fs-13" for="cwaba_{{ $w->id }}">
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
                                       value="{{ $t->id }}" id="ctg_{{ $t->id }}">
                                <label class="form-check-label fs-13" for="ctg_{{ $t->id }}">{{ $t->name }}</label>
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
                                       value="{{ $ig->id }}" id="cig_{{ $ig->id }}">
                                <label class="form-check-label fs-13" for="cig_{{ $ig->id }}">{{ $ig->name }}</label>
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
                                       value="{{ $m->id }}" id="cms_{{ $m->id }}">
                                <label class="form-check-label fs-13" for="cms_{{ $m->id }}">{{ $m->name }}</label>
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
                                       value="{{ $lc->id }}" id="clc_{{ $lc->id }}">
                                <label class="form-check-label fs-13" for="clc_{{ $lc->id }}">{{ $lc->name }}</label>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(!isset($platforms) || collect($platforms)->flatten()->isEmpty())
                        <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Belum ada platform terhubung untuk bisnis ini.</small>
                        @endif
                    </div>
                </div>

            </div>

            <div class="modal-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bx bx-info-circle me-1"></i>{{__('auth.required_fields')}}
                </small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bx bx-x me-1"></i>{{__('auth.cancel')}}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>{{__('auth.save_data')}}
                    </button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Search Functionality
        $('#searchUser').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            var hasResults = false;

            $('.user-item').each(function() {
                var name = $(this).data('name');
                var email = $(this).data('email');

                if (name.includes(searchText) || email.includes(searchText)) {
                    $(this).removeClass('d-none').show();
                    hasResults = true;
                } else {
                    $(this).addClass('d-none').hide();
                }
            });

            // Show/hide no results message
            if (!hasResults && searchText !== '') {
                $('#noResults').removeClass('d-none');
            } else {
                $('#noResults').addClass('d-none');
            }
        });

        // Delete Confirmation
        $('.deletebutton').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            
            Swal.fire({
                title: '{{__("auth.confirm_delete")}}',
                text: '{{__("auth.confirm_delete_text")}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{__("auth.yes_delete")}}',
                cancelButtonText: '{{__("auth.cancel")}}'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.business').select2({
            placeholder: "{{__('auth.business_placeholder')}}...",
            allowClear: true,
            dropdownParent: $('#addUserModal')
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

<!-- ══════════════════════════════════════════
     EDIT HUMAN AGENT MODAL
═══════════════════════════════════════════ -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header" style="background:linear-gradient(135deg,#0066CC,#0099FF);color:#fff;">
                <h5 class="modal-title fw-bold" id="editUserModalLabel">
                    <i class="bx bx-edit-alt me-2"></i>Edit Human Agent
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="editUserForm" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">

                    <!-- Photo -->
                    <div class="row mb-3">
                        <div class="col-sm-3 d-flex flex-column justify-content-center">
                            <label class="col-form-label fw-semibold"><i class="bx bx-image me-1"></i>Foto Profil</label>
                            <small class="d-block text-muted mt-1"><i class="bx bx-info-circle me-1"></i>JPG, PNG. Max 5MB.</small>
                        </div>
                        <div class="col-sm-9 d-flex align-items-center gap-3">
                            <!-- Unified avatar zone — always visible, click to change -->
                            <div id="editAvatarWrap" style="position:relative;width:96px;height:96px;flex-shrink:0;">
                                <!-- Placeholder (no photo) -->
                                <div id="editAvatarPlaceholder"
                                     onclick="document.getElementById('editImageInput').click()"
                                     style="width:96px;height:96px;border-radius:50%;border:2px dashed #93C5FD;background:#EFF6FF;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;">
                                    <i class="bx bx-camera" style="font-size:1.8rem;color:#3B82F6;"></i>
                                    <span style="font-size:.6rem;color:#3B82F6;font-weight:700;margin-top:2px;">Upload</span>
                                </div>
                                <!-- Photo preview (hidden initially) -->
                                <div id="editAvatarPreview" style="display:none;position:relative;width:96px;height:96px;">
                                    <img id="editImagePreview" src=""
                                         style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:2.5px solid #BFDBFE;display:block;">
                                    <!-- Hover overlay -->
                                    <div id="editCamOverlay"
                                         onclick="document.getElementById('editImageInput').click()"
                                         style="position:absolute;inset:0;border-radius:50%;background:rgba(0,80,200,0.55);display:none;align-items:center;justify-content:center;flex-direction:column;cursor:pointer;">
                                        <i class="bx bx-camera" style="font-size:1.4rem;color:#fff;"></i>
                                        <span style="font-size:.6rem;color:#fff;font-weight:600;">Ganti</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Side info + remove button -->
                            <div>
                                <p class="mb-1" style="font-size:.78rem;font-weight:600;color:#334155;">Foto Profil Agent</p>
                                <p class="mb-2" style="font-size:.72rem;color:#64748B;">Klik lingkaran untuk unggah atau ganti foto.</p>
                                <button type="button" id="editRemoveImageBtn"
                                        class="btn btn-link text-danger p-0 d-none"
                                        style="font-size:.75rem;">
                                    <i class="bx bx-trash me-1"></i>Hapus foto
                                </button>
                            </div>
                            <input type="file" id="editImageInput" name="image" accept="image/*" style="display:none;">
                        </div>
                    </div>

                    <!-- Nama -->
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-semibold"><i class="bx bx-user me-1"></i>Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                <input type="text" class="form-control" name="name" id="editName" placeholder="Contoh: John Doe" required>
                            </div>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-semibold"><i class="bx bx-male-female me-1"></i>Jenis Kelamin <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-male-female"></i></span>
                                <select class="form-control" name="gender" id="editGender" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="male">{{__('auth.male')}}</option>
                                    <option value="female">{{__('auth.female')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Akses Bisnis -->
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-semibold"><i class="bx bx-briefcase me-1"></i>Akses Bisnis <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-control select2-edit" name="business[]" id="editBusiness" multiple required>
                                @foreach($businesses as $biz)
                                <option value="{{$biz->id}}">{{$biz->name}}</option>
                                @endforeach
                            </select>
                            <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Tentukan bisnis yang dapat diakses oleh human agent ini</small>
                        </div>
                    </div>

                    <!-- Informasi Kontak Group -->
                    <div class="mb-3 px-3 py-3 rounded-3" style="background:#F8FAFF;border:1px solid #E4EDFF;">
                        <div class="fw-semibold mb-2 d-flex align-items-center gap-2" style="font-size:.7rem;color:#6B7280;text-transform:uppercase;letter-spacing:.06em;">
                            <i class="bx bx-id-card" style="font-size:.85rem;color:#3B82F6;"></i>Informasi Kontak
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-3 col-form-label fw-semibold" style="font-size:.83rem;"><i class="bx bxl-whatsapp me-1 text-success"></i>WhatsApp <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bxl-whatsapp text-success"></i></span>
                                    <input type="text" class="form-control" name="phone" id="editPhone" placeholder="+62 xxx xxx xxx" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <label class="col-sm-3 col-form-label fw-semibold" style="font-size:.83rem;"><i class="bx bx-envelope me-1 text-primary"></i>Email <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-envelope text-primary"></i></span>
                                    <input type="email" class="form-control" name="email" id="editEmail" required>
                                </div>
                                <small class="text-muted mt-1 d-block"><i class="bx bx-info-circle me-1"></i>Digunakan untuk login ke sistem</small>
                            </div>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-semibold"><i class="bx bx-lock me-1"></i>Role <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                                <select class="form-control" name="role" id="editRole" required>
                                    <option value="">{{__('general.choose')}}</option>
                                    @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Platform Access -->
                    <div class="row mb-3 mt-2" style="border-top:1px dashed #E2E8F0;padding-top:1rem;align-items:flex-start;">
                        <div class="col-sm-3">
                            <label class="col-form-label fw-semibold"><i class="bx bx-broadcast me-1"></i>Akses Platform</label>
                            <small class="d-block text-muted mt-1"><i class="bx bx-info-circle me-1"></i>Platform yang bisa diakses oleh agent ini</small>
                        </div>
                        <div class="col-sm-9" id="editPlatformSection">
                            @if(isset($platforms['devices']) && $platforms['devices']->count())
                            <div class="mb-3">
                                <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#25D366;display:inline-block"></span>WhatsApp Personal
                                </div>
                                @foreach($platforms['devices'] as $d)
                                <div class="form-check mb-1">
                                    <input class="form-check-input edit-platform-check" type="checkbox" name="devices[]" value="{{ $d->id }}" id="edev_{{ $d->id }}" data-group="devices">
                                    <label class="form-check-label fs-13" for="edev_{{ $d->id }}">{{ $d->name }} <span class="badge {{ $d->status=='active' ? 'bg-success-transparent' : 'bg-secondary-transparent' }} ms-1 fs-11">{{ $d->status }}</span></label>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(isset($platforms['wabas']) && $platforms['wabas']->count())
                            <div class="mb-3">
                                <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#0D6EFD;display:inline-block"></span>WhatsApp Business API
                                </div>
                                @foreach($platforms['wabas'] as $w)
                                <div class="form-check mb-1">
                                    <input class="form-check-input edit-platform-check" type="checkbox" name="wabas[]" value="{{ $w->id }}" id="ewaba_{{ $w->id }}" data-group="wabas">
                                    <label class="form-check-label fs-13" for="ewaba_{{ $w->id }}">+{{ $w->phone }} <span class="badge bg-primary-transparent ms-1 fs-11">WABA</span></label>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(isset($platforms['telegrams']) && $platforms['telegrams']->count())
                            <div class="mb-3">
                                <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#2CA5E0;display:inline-block"></span>Telegram
                                </div>
                                @foreach($platforms['telegrams'] as $t)
                                <div class="form-check mb-1">
                                    <input class="form-check-input edit-platform-check" type="checkbox" name="telegrams[]" value="{{ $t->id }}" id="etg_{{ $t->id }}" data-group="telegrams">
                                    <label class="form-check-label fs-13" for="etg_{{ $t->id }}">{{ $t->name }}</label>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(isset($platforms['instagrams']) && $platforms['instagrams']->count())
                            <div class="mb-3">
                                <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#E1306C;display:inline-block"></span>Instagram DM
                                </div>
                                @foreach($platforms['instagrams'] as $ig)
                                <div class="form-check mb-1">
                                    <input class="form-check-input edit-platform-check" type="checkbox" name="instagrams[]" value="{{ $ig->id }}" id="eig_{{ $ig->id }}" data-group="instagrams">
                                    <label class="form-check-label fs-13" for="eig_{{ $ig->id }}">{{ $ig->name }}</label>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(isset($platforms['livechats']) && $platforms['livechats']->count())
                            <div class="mb-3">
                                <div class="fw-semibold fs-12 text-muted mb-2 d-flex align-items-center gap-2">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#6366F1;display:inline-block"></span>Widget Live Chat
                                </div>
                                @foreach($platforms['livechats'] as $lc)
                                <div class="form-check mb-1">
                                    <input class="form-check-input edit-platform-check" type="checkbox" name="livechats[]" value="{{ $lc->id }}" id="elc_{{ $lc->id }}" data-group="livechats">
                                    <label class="form-check-label fs-13" for="elc_{{ $lc->id }}">{{ $lc->name }}</label>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(!isset($platforms) || (collect($platforms)->flatten()->isEmpty()))
                            <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Belum ada platform terhubung.</small>
                            @endif
                        </div>
                    </div>

                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Batalkan
                    </button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                        <i class="bx bx-save me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
// ── Edit User Modal Logic ───────────────────────────────────────────────────
let editCurrentUserId = null;

function openEditModal(userId) {
    editCurrentUserId = userId;

    // Reset form
    document.getElementById('editUserForm').reset();
    document.getElementById('editAvatarPreview').style.display = 'none';
    document.getElementById('editAvatarPlaceholder').style.display = 'flex';
    document.getElementById('editImagePreview').src = '';
    document.getElementById('editRemoveImageBtn').classList.add('d-none');

    // Reset all platform checkboxes
    document.querySelectorAll('.edit-platform-check').forEach(cb => cb.checked = false);

    // Reset select2
    if ($('#editBusiness').data('select2')) {
        $('#editBusiness').val(null).trigger('change');
    }

    // Show loading state
    document.getElementById('editSubmitBtn').disabled = true;
    document.getElementById('editSubmitBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading...';

    // Set form action dynamically
    const baseUrl = '{{ url("/app/users/edit") }}';
    document.getElementById('editUserForm').action = baseUrl + '/' + userId;

    // Open modal
    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();

    // Fetch user data
    const jsonUrl = '{{ url("/app/users/json") }}/' + userId;
    fetch(jsonUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        // Populate fields
        document.getElementById('editName').value   = data.name   || '';
        document.getElementById('editEmail').value  = data.email  || '';
        document.getElementById('editPhone').value  = data.phone  || '';

        // Gender
        const gSel = document.getElementById('editGender');
        for (let o of gSel.options) o.selected = (o.value === data.gender);

        // Role
        const rSel = document.getElementById('editRole');
        for (let o of rSel.options) o.selected = (o.value == data.role_id);

        // Business (Select2 multi)
        if (data.businesses && data.businesses.length) {
            $('#editBusiness').val(data.businesses).trigger('change');
        }

        // Photo preview
        if (data.photo) {
            document.getElementById('editImagePreview').src = data.photo;
            document.getElementById('editAvatarPreview').style.display = 'block';
            document.getElementById('editAvatarPlaceholder').style.display = 'none';
            document.getElementById('editRemoveImageBtn').classList.remove('d-none');
        }

        // Platform checkboxes
        if (data.userPlatforms) {
            Object.keys(data.userPlatforms).forEach(group => {
                const ids = data.userPlatforms[group] || [];
                document.querySelectorAll(`.edit-platform-check[data-group="${group}"]`).forEach(cb => {
                    cb.checked = ids.includes(cb.value);
                });
            });
        }

        // Re-enable submit
        document.getElementById('editSubmitBtn').disabled = false;
        document.getElementById('editSubmitBtn').innerHTML = '<i class="bx bx-save me-1"></i>Simpan Perubahan';
    })
    .catch(err => {
        console.error('Error loading user data:', err);
        document.getElementById('editSubmitBtn').disabled = false;
        document.getElementById('editSubmitBtn').innerHTML = '<i class="bx bx-save me-1"></i>Simpan Perubahan';
    });
}

// Edit modal image upload
document.addEventListener('DOMContentLoaded', function() {
    const editImageInput   = document.getElementById('editImageInput');
    const editImgPreview   = document.getElementById('editImagePreview');
    const editAvatarPrev   = document.getElementById('editAvatarPreview');
    const editAvatarPH     = document.getElementById('editAvatarPlaceholder');
    const editCamOverlay   = document.getElementById('editCamOverlay');
    const editRemoveBtn    = document.getElementById('editRemoveImageBtn');

    if (editImageInput) {
        editImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                editImgPreview.src = e.target.result;
                editAvatarPrev.style.display = 'block';
                editAvatarPH.style.display = 'none';
                editRemoveBtn.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        });
    }
    if (editRemoveBtn) {
        editRemoveBtn.addEventListener('click', function() {
            editImageInput.value = '';
            editImgPreview.src = '';
            editAvatarPrev.style.display = 'none';
            editAvatarPH.style.display = 'flex';
            editRemoveBtn.classList.add('d-none');
        });
    }
    // Show cam overlay on hover
    if (editAvatarPrev) {
        editAvatarPrev.addEventListener('mouseenter', () => { if(editCamOverlay) { editCamOverlay.style.display='flex'; } });
        editAvatarPrev.addEventListener('mouseleave', () => { if(editCamOverlay) { editCamOverlay.style.display='none'; } });
    }

    // Init Select2 for edit business
    if (typeof $.fn.select2 !== 'undefined') {
        $('#editBusiness').select2({
            dropdownParent: $('#editUserModal'),
            placeholder: 'Pilih Bisnis',
            allowClear: true
        });
    }
});
</script>


<script>
// ── Flash Message Display ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    @if(session('flash'))
    if (typeof toastr !== 'undefined') {
        toastr.success("{{ session('flash') }}", '', {timeOut: 4000, positionClass: 'toast-top-right'});
    } else {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('flash') }}", timer: 2500, showConfirmButton: false });
    }
    @endif

    @if(session('gagal'))
    if (typeof toastr !== 'undefined') {
        toastr.error("{{ session('gagal') }}", 'Error', {timeOut: 5000, positionClass: 'toast-top-right'});
    } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ session('gagal') }}" });
    }
    @endif
});
</script>
@endsection