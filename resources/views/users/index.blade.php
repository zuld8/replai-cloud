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

/* ── Header panel ─────────────────────────────────────────── */
.agt-header{background:linear-gradient(145deg,#1E2A3B 0%,#162032 100%);border:1px solid rgba(255,255,255,.07);border-radius:14px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.agt-header-left{display:flex;align-items:center;gap:12px}
.agt-header-icon{width:42px;height:42px;border-radius:11px;background:rgba(46,141,225,.15);color:#4BA3EF;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.agt-header-title{font-size:1.05rem;font-weight:700;color:#F1F5F9;line-height:1.2}
.agt-header-sub{font-size:.78rem;color:#94A3B8;margin-top:2px}
.agt-header-right{display:flex;align-items:center;gap:18px;flex-wrap:wrap}
.agt-quota{text-align:right;line-height:1.3}
.agt-quota-label{font-size:.62rem;color:#94A3B8;text-transform:uppercase;letter-spacing:.05em;font-weight:600}
.agt-quota-count{font-size:.82rem;color:#F1F5F9;font-weight:700}
.agt-quota-bar{width:120px;height:5px;background:rgba(255,255,255,.12);border-radius:4px;overflow:hidden;margin-top:5px;margin-left:auto}
.agt-quota-fill{height:100%;background:#2E8DE1;border-radius:4px;transition:width .4s}
.agt-quota-fill.full{background:#EF4444}

/* ── Agent Cards v2 ─────────────────────────────────────── */
.agt-card {
    background: linear-gradient(145deg, #1E2A3B 0%, #162032 100%);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    padding: 16px;
    position: relative;
    transition: border-color .2s, box-shadow .2s;
    height: 100%;
    box-sizing: border-box;
}
.agt-card:hover {
    border-color: rgba(46,141,225,0.35);
    box-shadow: 0 6px 24px rgba(0,0,0,0.28);
}
.agt-top { display: flex; gap: 12px; align-items: flex-start; }
.agt-avatar {
    width: 52px; height: 52px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1.05rem; color: #fff;
    flex-shrink: 0; position: relative; overflow: hidden;
}
.agt-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.agt-dot { position: absolute; bottom: 2px; right: 2px;
    width: 11px; height: 11px; border-radius: 50%; border: 2px solid #1E2A3B; }
.agt-dot-inline { display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
.agt-dot-on, .agt-dot-inline.agt-dot-on { background: #22C55E; }
.agt-dot-off, .agt-dot-inline.agt-dot-off { background: #475569; }
.agt-info { flex: 1; min-width: 0; }
.agt-name-row { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; margin-bottom: 2px; }
.agt-name { font-size: .9rem; font-weight: 700; color: #F1F5F9; line-height: 1.3; }
.agt-badge { font-size: .6rem; padding: 2px 6px; border-radius: 9px; font-weight: 700; display: inline-flex; align-items: center; gap: 2px; }
.agt-badge-owner { background: rgba(251,191,36,0.15); color: #FBBF24; border: 1px solid rgba(251,191,36,0.3); }
.agt-email { font-size: .71rem; color: #94A3B8; margin-bottom: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100%; }
.agt-role { font-size: .7rem; color: #2E8DE1; }
.agt-menu { margin-left: auto; flex-shrink: 0; }
.agt-menu-btn { background: none; border: none; color: #64748B; padding: 4px 6px;
    border-radius: 6px; cursor: pointer; transition: all .15s; line-height: 1; }
.agt-menu-btn:hover { background: rgba(255,255,255,0.08); color: #F1F5F9; }
.agt-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 12px 0 9px; }
.agt-channels-label { font-size: .59rem; font-weight: 700; color: #475569;
    letter-spacing: .08em; text-transform: uppercase; margin-bottom: 8px; }
.agt-channel-row { display: flex; align-items: center; gap: 9px; margin-bottom: 7px; }
.agt-ch-icon { width: 30px; height: 30px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; flex-shrink: 0; }
.agt-ch-body { flex: 1; min-width: 0; }
.agt-ch-name { font-size: .72rem; font-weight: 600; color: #CBD5E1; line-height: 1.3; }
.agt-ch-ident { font-size: .67rem; color: #64748B; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.agt-ch-status { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
.agt-status-txt { font-size: .64rem; color: #64748B; white-space: nowrap; }
.agt-ch-more { font-size: .64rem; color: #475569; margin-top: -2px; margin-bottom: 5px; }
.agt-footer { display: flex; justify-content: space-between; font-size: .64rem;
    color: #475569; margin-top: 11px; padding-top: 9px;
    border-top: 1px solid rgba(255,255,255,0.05); flex-wrap: wrap; gap: 4px; }
/* Kuota bar */
.agt-quota-bar { background: rgba(255,255,255,0.1); border-radius: 4px; height: 5px; width: 100px; overflow: hidden; margin-top: 3px; }
.agt-quota-fill { height: 100%; border-radius: 4px; background: #2E8DE1; transition: width .4s; }
.agt-quota-fill.full { background: #EF4444; }
/* Dropdown dark */
.agt-card .dropdown-menu {
    background: #1A2535; border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px; min-width: 155px; padding: 4px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}
.agt-card .dropdown-item { color: #CBD5E1; font-size: .78rem; border-radius: 6px; padding: 6px 10px; }
.agt-card .dropdown-item:hover { background: rgba(255,255,255,0.07); color: #F1F5F9; }
.agt-card .dropdown-item.text-danger { color: #F87171 !important; }
.agt-card .dropdown-item.text-danger:hover { background: rgba(239,68,68,0.12); }
.agt-card .dropdown-divider { border-color: rgba(255,255,255,0.07); margin: 3px 0; }
</style>
@endsection

@section('button')
@endsection

@section('content')
<div class="row">
    <!-- Header Info -->
    <div class="col-12 mb-3">
        <x-validation-component></x-validation-component>
    </div>

    {{-- HEADER PANEL --}}
    <div class="col-12 mb-3">
        <div class="agt-header">
            <div class="agt-header-left">
                <span class="agt-header-icon"><i class="bx bx-group"></i></span>
                <div>
                    <div class="agt-header-title">Kelola pengguna</div>
                    <div class="agt-header-sub">Human agent yang bisa balas chat di CRM</div>
                </div>
            </div>
            <div class="agt-header-right">
                @isset($userQuota)
                @if($userQuota['limit'])
                @php $qPct = min(100, round(($userQuota['count']/$userQuota['limit'])*100)); $qFull = $userQuota['count'] >= $userQuota['limit']; @endphp
                <div class="agt-quota">
                    <div class="agt-quota-label">Kuota paket</div>
                    <div class="agt-quota-count">{{ $userQuota['count'] }} / {{ $userQuota['limit'] }} terpakai</div>
                    <div class="agt-quota-bar"><div class="agt-quota-fill {{ $qFull ? 'full' : '' }}" style="width:{{ $qPct }}%"></div></div>
                </div>
                @endif
                @endisset
                @can('human-agents.tambah')
                @if(isset($qFull) && $qFull)
                <button type="button" class="btn btn-secondary" disabled title="Paket penuh, upgrade untuk tambah agen"><i class="bx bx-plus-circle me-1"></i>Tambah agen</button>
                @else
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bx bx-plus-circle me-1"></i>Tambah agen</button>
                @endif
                @endcan
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="col-12 mb-3">
        <div class="input-group" style="max-width:380px;">
            <span class="input-group-text" style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.1);"><i class="bx bx-search" style="color:#64748B;"></i></span>
            <input type="text" class="form-control" id="searchUser" placeholder="Cari nama atau email..." style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.1);color:#F1F5F9;">
        </div>
    </div>

    <!-- User Cards Grid v2 -->
    @forelse ($users as $user)
    @php
        $words    = array_filter(explode(' ', $user->name));
        $initials = strtoupper(substr(array_values($words)[0], 0, 1) . (count($words) > 1 ? substr(array_values($words)[1], 0, 1) : ''));
        $palette  = ['#0EA5E9','#8B5CF6','#EC4899','#10B981','#3B82F6','#F59E0B','#06B6D4','#EF4444','#14B8A6','#F97316'];
        $bgClr    = $palette[ord($user->name[0]) % count($palette)];
        $isOwner  = $user->merchant && optional($user->merchant->owner)->id === $user->id;
        $channels = $platformMap[$user->id] ?? [];
        $chats7d  = $chatCount7d[$user->id] ?? 0;
        $lastAct  = $user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->diffForHumans() : '-';
        $hasPhoto = $user->image_data && !in_array($user->image_data, ['images/user.png','uploads/image.jpg','']);
        $chIcons  = ['waba'=>'bxl-whatsapp','device'=>'bx-mobile','telegram'=>'bxl-telegram','instagram'=>'bxl-instagram','messenger'=>'bxl-messenger','livechat'=>'bx-chat'];
        $chColors = ['waba'=>'#25D366','device'=>'#128C7E','telegram'=>'#2AABEE','instagram'=>'#C13584','messenger'=>'#0099FF','livechat'=>'#F97316'];
    @endphp
    <div class="col-xl-4 col-lg-6 col-12 mb-3 user-item"
         data-name="{{ strtolower($user->name) }}"
         data-email="{{ strtolower($user->email) }}"
         data-user-id="{{ $user->id }}">
        <div class="agt-card">
            {{-- Top: avatar + info + menu --}}
            <div class="agt-top">
                <div class="agt-avatar" style="background:{{ $bgClr }};">
                    @if($hasPhoto)
                        <img src="{{ asset($user->image_data) }}" alt="{{ $user->name }}">
                    @else
                        {{ $initials }}
                    @endif
                    <span class="agt-dot agt-dot-on"></span>
                </div>
                <div class="agt-info">
                    <div class="agt-name-row">
                        <span class="agt-name">{{ $user->name }}</span>
                        @if($isOwner)<span class="agt-badge agt-badge-owner"><i class="bx bx-crown"></i> Owner</span>@endif
                    </div>
                    <div class="agt-email" title="{{ $user->email }}">{{ $user->email }}</div>
                    <div class="agt-role">
                        @if($user->role === 'admin')<i class="bx bx-shield me-1"></i>Administrator
                        @elseif($user->role_access)<i class="bx bx-user-check me-1"></i>{{ $user->role_access->name }}
                        @else<i class="bx bx-user me-1"></i>Agen
                        @endif
                    </div>
                </div>
                {{-- ⋮ kebab menu --}}
                <div class="agt-menu dropdown">
                    <button class="agt-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" id="agtMenu-{{ $user->id }}">
                        <i class="bx bx-dots-vertical-rounded" style="font-size:1.2rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="agtMenu-{{ $user->id }}">
                        @can('human-agents.edit')
                        <li><a class="dropdown-item" href="#" onclick="openEditModal('{{ $user->id }}'); return false;"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                        @if(!$isOwner || auth()->id() === $user->id)
                        <li><a class="dropdown-item" href="#" onclick="openPasswordModal('{{ $user->id }}'); return false;"><i class="bx bx-key me-2"></i>Reset Password</a></li>
                        @endif
                        @endcan
                        @can('human-agents.hapus')
                        @if(!$isOwner && my_user()->id !== $user->id)
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger deletebutton" href="#"
                               data-delete-url="{{ route('users.delete', $user->id) }}"
                               data-delete-token="{{ csrf_token() }}"><i class="bx bx-trash me-2"></i>Hapus</a></li>
                        @endif
                        @endcan
                    </ul>
                </div>
            </div>

            {{-- Channels --}}
            @if(!empty($channels))
            <div class="agt-divider"></div>
            <div class="agt-channels-label">KANAL YANG DIPEGANG</div>
            @php $shownCh = array_slice($channels, 0, 3); $moreCh = count($channels) - 3; @endphp
            @foreach($shownCh as $ch)
            @php
                $icon     = $chIcons[$ch['type']] ?? 'bx-plug';
                $color    = $chColors[$ch['type']] ?? '#64748B';
                $isActive = in_array($ch['status'] ?? '', ['active','CONNECTED','all','chatbot','ai']);
            @endphp
            <div class="agt-channel-row">
                <div class="agt-ch-icon" style="background:{{ $color }}22;color:{{ $color }};"><i class="bx {{ $icon }}"></i></div>
                <div class="agt-ch-body">
                    <div class="agt-ch-name">{{ $ch['label'] }}</div>
                    @if($ch['ident'])<div class="agt-ch-ident">{{ $ch['ident'] }}</div>@endif
                </div>
                <div class="agt-ch-status">
                    <span class="agt-dot-inline {{ $isActive ? 'agt-dot-on' : 'agt-dot-off' }}"></span>
                    <span class="agt-status-txt">{{ $isActive ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </div>
            @endforeach
            @if($moreCh > 0)<div class="agt-ch-more">+{{ $moreCh }} kanal lainnya</div>@endif
            @endif

            {{-- Footer --}}
            <div class="agt-footer">
                <span><i class="bx bx-time-five me-1"></i>{{ $lastAct }}</span>
                <span><i class="bx bx-conversation me-1"></i>{{ $chats7d }} chat · 7 hari</span>
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

{{-- Change Password Modal --}}
<div class="modal fade" id="changePassModal" tabindex="-1" aria-labelledby="changePassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="background:#1E2A3B;border:1px solid rgba(255,255,255,0.1);border-radius:14px;">
            <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,0.07);">
                <h5 class="modal-title text-white" id="changePassModalLabel"><i class="bx bx-key me-2"></i>Reset Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePassForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size:.78rem;">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" id="cpPassword" required minlength="8"
                               style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1);color:#F1F5F9;"
                               placeholder="Min. 8 karakter">
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted" style="font-size:.78rem;">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="confirm" id="cpConfirm" required
                               style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1);color:#F1F5F9;"
                               placeholder="Ulangi password">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,0.07);">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-save me-1"></i>Simpan</button>
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

        // Delete — A5: POST fetch (bukan GET)
        $(document).off('click.deleteuser');
        $('.deletebutton').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var url   = $(this).data('delete-url');
            var token = $(this).data('delete-token');
            if (!url) return;
            if (window.confirm('Hapus pengguna ini? Tindakan tidak dapat dibatalkan.')) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: '_token=' + encodeURIComponent(token)
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) { location.reload(); }
                    else { alert(data.message || 'Gagal menghapus pengguna'); }
                })
                .catch(function() { alert('Gagal menghapus pengguna'); });
            }
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
                            {{-- Normal role select (non-primary users) --}}
                            <div class="input-group" id="editRoleSelectWrap">
                                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                                <select class="form-control" id="editRole">
                                    <option value="">{{__('general.choose')}}</option>
                                    @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Locked role display (primary/owner user) --}}
                            <div id="editRoleLockedWrap" class="d-none">
                                <div class="input-group">
                                    <span class="input-group-text bg-warning-subtle">
                                        <i class="bx bx-lock-alt text-warning"></i>
                                    </span>
                                    <div class="form-control d-flex align-items-center justify-content-between"
                                         style="background:#fffbf0;cursor:not-allowed;min-height:38px;">
                                        <span id="editRoleLockedName" class="fw-semibold"></span>
                                        <span class="badge bg-warning text-dark ms-2" style="font-size:0.7rem;">
                                            <i class="bx bx-lock-alt me-1"></i>Terkunci
                                        </span>
                                    </div>
                                </div>
                                <input type="hidden" name="role" id="editRoleHidden">
                                <small class="text-muted d-block mt-1">
                                    <i class="bx bx-shield-alt me-1 text-warning"></i>
                                    Role akun utama tidak dapat diubah untuk keamanan sistem.
                                </small>
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

function openPasswordModal(userId) {
    var baseUrl = '{{ url("/app/users/change-password") }}/' + userId;
    document.getElementById('changePassForm').action = baseUrl;
    document.getElementById('cpPassword').value = '';
    document.getElementById('cpConfirm').value  = '';
    new bootstrap.Modal(document.getElementById('changePassModal')).show();
}

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

        // Role — lock for primary/owner user
        const rSel     = document.getElementById('editRole');
        const lockWrap = document.getElementById('editRoleLockedWrap');
        const selWrap  = document.getElementById('editRoleSelectWrap');
        if (data.is_primary) {
            // Show locked display, hide select
            selWrap.classList.add('d-none');
            lockWrap.classList.remove('d-none');
            rSel.removeAttribute('required');
            document.getElementById('editRoleLockedName').textContent = data.role_name || 'Administrator';
            document.getElementById('editRoleHidden').value = data.role_id;
        } else {
            // Show normal select
            selWrap.classList.remove('d-none');
            lockWrap.classList.add('d-none');
            rSel.setAttribute('required', '');
            for (let o of rSel.options) o.selected = (o.value == data.role_id);
            // FIX: always sync hidden input so only one role value is submitted
            document.getElementById('editRoleHidden').value = data.role_id || '';
        }

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
    // FIX: Keep editRoleHidden in sync when select changes
    const editRoleSel = document.getElementById('editRole');
    if (editRoleSel) {
        editRoleSel.addEventListener('change', function() {
            document.getElementById('editRoleHidden').value = this.value;
        });
    }

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