@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/dropify/css/dropify.min.css')}}">
<style>
/* ═══════════════════════════════════════════════════
   PROFILE PAGE — Premium Redesign
═══════════════════════════════════════════════════ */
.profile-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    border-radius: 20px;
    padding: 36px 32px 24px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 28px;
    box-shadow: 0 8px 32px rgba(102,126,234,0.35);
    position: relative;
    overflow: hidden;
}
.profile-hero::before {
    content:''; position:absolute; top:-40px; right:-40px;
    width:200px; height:200px; background:rgba(255,255,255,0.08); border-radius:50%;
}
.profile-hero::after {
    content:''; position:absolute; bottom:-60px; left:-20px;
    width:160px; height:160px; background:rgba(255,255,255,0.06); border-radius:50%;
}
.profile-avatar-wrap { position:relative; flex-shrink:0; }
.profile-avatar-wrap img {
    width:96px; height:96px; border-radius:50%;
    border:4px solid rgba(255,255,255,0.5);
    object-fit:cover; box-shadow:0 4px 16px rgba(0,0,0,0.2);
}
.profile-avatar-wrap .online-dot {
    width:16px; height:16px; background:#2ecc71;
    border:3px solid #fff; border-radius:50%;
    position:absolute; bottom:4px; right:4px;
}
.profile-hero-info h3 { font-size:22px; font-weight:700; margin:0; }
.profile-hero-info p { margin:4px 0 0; opacity:0.85; font-size:14px; }
.profile-role-badge {
    display:inline-block; background:rgba(255,255,255,0.2);
    backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.3);
    border-radius:20px; padding:3px 14px; font-size:12px; font-weight:600;
    margin-top:8px; letter-spacing:0.5px;
}

/* Cards */
.profile-card {
    border-radius:16px; border:none;
    box-shadow:0 2px 16px rgba(0,0,0,0.07); overflow:hidden;
    transition:box-shadow 0.2s;
}
.profile-card:hover { box-shadow:0 6px 28px rgba(0,0,0,0.11); }
.profile-card .card-header {
    background:linear-gradient(135deg,#f8f9ff 0%,#eef0ff 100%);
    border-bottom:1.5px solid #e8eaf6; padding:18px 24px;
}
.profile-card .card-header .card-title {
    font-size:14px; font-weight:700; color:#3d3d5c;
    display:flex; align-items:center; gap:8px;
}
.profile-card .card-header .card-title i { font-size:18px; color:#667eea; }
.profile-card .card-body { padding:24px; }
.profile-card .card-footer {
    background:#fafbff; border-top:1px solid #eef0ff; padding:14px 24px;
}
.profile-card .form-label {
    font-size:12px; font-weight:600; color:#6b6b8d;
    text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;
}
.profile-card .form-control {
    border-radius:10px; border:1.5px solid #e2e8f0;
    padding:10px 14px; font-size:14px;
    transition:border-color 0.2s, box-shadow 0.2s;
}
.profile-card .form-control:focus {
    border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,0.15);
}

/* Package usage card */
.pkg-use-card {
    border-radius:20px; overflow:hidden; border:none;
    box-shadow:0 4px 24px rgba(0,0,0,0.10);
}
.pkg-use-header {
    background:linear-gradient(135deg,#1a73e8 0%,#0d47a1 100%);
    padding:24px 24px 20px; color:#fff; position:relative; overflow:hidden;
}
.pkg-use-header::before {
    content:''; position:absolute; top:-30px; right:-30px;
    width:130px; height:130px; background:rgba(255,255,255,0.1); border-radius:50%;
}
.pkg-use-header .pkg-label { font-size:11px; opacity:0.75; text-transform:uppercase; letter-spacing:1px; }
.pkg-use-header .pkg-name { font-size:22px; font-weight:800; margin:4px 0; }
.pkg-use-header .pkg-price { font-size:17px; font-weight:700; }
.pkg-use-header .pkg-period { font-size:11px; opacity:0.75; }
.pkg-status-badge {
    display:inline-flex; align-items:center; gap:6px;
    padding:3px 12px; border-radius:20px; font-size:11px; font-weight:600; margin-top:8px;
}
.pkg-status-badge.active { background:rgba(46,204,113,0.25); color:#2ecc71; border:1px solid rgba(46,204,113,0.4); }
.pkg-status-badge.expired { background:rgba(231,76,60,0.2); color:#e74c3c; border:1px solid rgba(231,76,60,0.4); }
.pkg-status-badge .dot { width:7px; height:7px; border-radius:50%; background:currentColor; }

/* Expiry bar */
.pkg-expiry { padding:14px 20px 4px; background:#fff; }
.pkg-expiry-label { display:flex; justify-content:space-between; font-size:13px; color:#9b9bbb; margin-bottom:6px; font-weight:600; }
.pkg-bar { height:6px; background:#eee; border-radius:6px; overflow:hidden; margin-bottom:4px; }
.pkg-bar-fill { height:100%; border-radius:6px; }

/* Usage list */
.pkg-usage-body { padding:4px 20px 16px; background:#fff; }
.pkg-group-label {
    display:flex; align-items:center; gap:6px;
    font-size:12px; font-weight:700; color:#a0a0c8;
    text-transform:uppercase; letter-spacing:1px;
    padding:12px 0 6px; border-bottom:1px solid #f0f2ff; margin-bottom:6px;
}

/* Usage row */
.usage-row {
    display:flex; align-items:center; gap:10px;
    padding:5px 0;
}
.usage-icon {
    width:28px; height:28px; border-radius:8px;
    display:flex; align-items:center; justify-content:center;
    font-size:13px; flex-shrink:0;
}
.usage-label { font-size:14px; color:#3d3d5c; font-weight:500; flex:1; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.usage-bar-wrap { flex:2; min-width:0; }
.usage-mini-bar { height:5px; background:#eef0ff; border-radius:4px; overflow:hidden; margin-bottom:2px; }
.usage-mini-fill { height:100%; border-radius:4px; transition:width 0.4s; }
.usage-count { font-size:13px; color:#9b9bbb; font-weight:600; text-align:right; }
.usage-count.full { color:#e74c3c; }

.pkg-feat-list { margin-bottom: 4px; }
.pkg-feat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 0;
    font-size: 13px;
    border-bottom: 1px solid #f7f7ff;
}
.pkg-feat-item:last-child { border-bottom: none; }
.pkg-feat-icon { font-size: 14px; flex-shrink: 0; width: 20px; text-align: center; }
.pkg-feat-label { flex: 1; color: #3d3d5c; font-weight: 500; }
.pkg-feat-val { font-weight: 700; color: #667eea; font-size: 12px; white-space: nowrap; }
.pkg-feat-val small { font-weight: 500; color: #9b9bbb; }
.pkg-upgrade-btn {
    display:block; text-align:center; margin:4px 20px 20px;
    padding:11px; background:linear-gradient(135deg,#667eea,#764ba2);
    color:#fff; border-radius:12px; font-weight:600; font-size:13px;
    text-decoration:none; transition:transform 0.2s, box-shadow 0.2s;
    box-shadow:0 4px 14px rgba(102,126,234,0.35);
}
.pkg-upgrade-btn:hover {
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(102,126,234,0.5);
    color:#fff; text-decoration:none;
}
.no-pkg-box {
    text-align:center; padding:36px 20px; background:#fff;
}
.no-pkg-box .icon { font-size:44px; margin-bottom:10px; }
.no-pkg-box h6 { color:#3d3d5c; font-weight:700; margin-bottom:6px; }
.no-pkg-box p { color:#9b9bbb; font-size:12px; }
</style>
@endsection

@section('content')
@php
    $user     = auth()->user();
    $bizId    = my_business();
    $business = \App\Models\Setting::where('id', $bizId)->first();
    $pkg      = $business ? $business->package_active : null;

    // Expiry
    $daysLeft   = 0;
    $daysTotal  = 30;
    $expireDate = '-';
    $isExpired  = true;
    if ($pkg) {
        $expire     = \Carbon\Carbon::parse($pkg->expire_date);
        $created    = \Carbon\Carbon::parse($pkg->created_at);
        $expireDate = $expire->format('d M Y');
        $daysLeft   = (int) max(0, now()->diffInDays($expire, false));
        $daysTotal  = max(1, (int) $created->diffInDays($expire));
        $isExpired  = $expire->isPast();
    }
    $daysPct  = min(100, $daysTotal > 0 ? round($daysLeft / $daysTotal * 100) : 0);
    $dayColor = $daysPct > 50 ? '#38ef7d' : ($daysPct > 20 ? '#f6d365' : '#f5576c');

    // Helper: bar color based on usage %
    function usageColor($used, $max) {
        if ($max <= 0) return '#38ef7d';
        $pct = ($used / $max) * 100;
        if ($pct >= 100) return '#f5576c';
        if ($pct >= 70)  return '#f6d365';
        return '#38ef7d';
    }
    function limitLabel($limit) {
        if (is_null($limit) || $limit <= 0) return '∞';
        return number_format((int)$limit);
    }
    function usagePct($used, $max) {
        if ($max <= 0) return 100;
        return min(100, round($used / $max * 100));
    }

    // Actual account counts – Platform
    $cntDevice   = $pkg ? \App\Models\WhatsappDevice::where('business_id', $bizId)->count() : 0;
    $cntWaba     = $pkg ? \App\Models\MetaAccount::where('business_id', $bizId)->count() : 0;
    $cntTelegram = $pkg ? \App\Models\TelegramKey::where('business_id', $bizId)->count() : 0;
    $cntInsta    = $pkg ? \App\Models\Meta\InstagramAccount::where('business_id', $bizId)->count() : 0;
    $cntMsg      = $pkg ? \App\Models\Meta\MessengerAccount::where('business_id', $bizId)->count() : 0;
    $cntLiveChat = $pkg ? \App\Models\LiveChat::where('business_id', $bizId)->count() : 0;
    // Actual counts – Features
    $cntChatbot  = $pkg ? \App\Models\ChatBot\ChatBot::where('business_id', $bizId)->count() : 0;
    $cntAITrain  = $pkg ? \App\Models\ChatBot\FineTunnel::where('business_id', $bizId)->count() : 0;
    $cntAgent    = $pkg ? \App\Models\User::where('business_id', $bizId)->count() : 0;
    $cntTemplate = $pkg ? \App\Models\Master\MessageTemplate::where('business_id', $bizId)->count() : 0;

    // Limits from transaction – Platform
    $limDevice   = $pkg ? (int)($pkg->device_limit        ?? 0) : 0;
    $limWaba     = $pkg ? (int)($pkg->waba_limit           ?? 0) : 0;
    $limTelegram = $pkg ? (int)($pkg->telegram             ?? 0) : 0;
    $limInsta    = $pkg ? (int)($pkg->instagram            ?? 0) : 0;
    $limMsg      = $pkg ? (int)($pkg->messanger            ?? 0) : 0;
    $limLiveChat = $pkg ? ($pkg->livechat_limit === 'yes' ? (int)($pkg->limit_livechat ?? 0) : 0) : 0;
    // Limits – Features
    $limChatbot  = $pkg ? (int)($pkg->chatbot_limit        ?? 0) : 0;
    $limAITrain  = $pkg ? (int)($pkg->ai_training_limit    ?? 0) : 0;
    $limAgent    = $pkg ? (int)($pkg->users_limit          ?? 0) : 0;
    $limTemplate = $pkg ? (int)($pkg->template_limit       ?? 0) : 0;
    // Limits – Storage & AI
    $limStorage  = $pkg ? (int)($pkg->storage              ?? 0) : 0;
    // Limits – Broadcast (quotas, not usage bars)
    $limWaBlast  = $pkg ? (int)($pkg->whatsapp_limit       ?? 0) : 0;
    $limEmail    = $pkg ? (int)($pkg->email_limit          ?? 0) : 0;
    $limScrapp   = $pkg ? (int)($pkg->scrapp_limit         ?? 0) : 0;
    $perWaBlast  = $pkg ? ($pkg->limit_whatsapp_priode     ?? '') : '';
    $perEmail    = $pkg ? ($pkg->limit_email_priode        ?? '') : '';
    $perScrapp   = $pkg ? ($pkg->limit_scrapp_priode       ?? '') : '';
    // Integration booleans
    $hasCekOngkir   = $pkg ? (bool)($pkg->cek_ongkir    ?? false) : false;
    $hasGoogleSheet = $pkg ? (bool)($pkg->google_sheet  ?? false) : false;
    // RAG
    $maxPerUpload   = $pkg ? (int)($pkg->max_per_upload ?? 0) : 0;
    $maxTotalRag    = $pkg ? (int)($pkg->max_total_rag  ?? 0) : 0;
@endphp

{{-- Hero --}}
<div class="profile-hero">
    <div class="profile-avatar-wrap">
        <img src="{{ asset($user->image_data ?? 'assets/images/avatars/1.png') }}"
             onerror="this.src='{{ asset('assets/images/avatars/1.png') }}'">
        <span class="online-dot"></span>
    </div>
    <div class="profile-hero-info">
        <h3>{{ $user->name }}</h3>
        <p>{{ $user->email }}</p>
        <span class="profile-role-badge">{{ ucfirst($user->role ?? 'User') }}</span>
        @if($pkg && !$isExpired)
        <span class="profile-role-badge" style="background:rgba(46,204,113,0.25);border-color:rgba(46,204,113,0.5);margin-left:6px;">
            📦 {{ $pkg->name }}
        </span>
        @endif
    </div>
</div>

<div class="row g-4">

    {{-- Update Profile + Change Password --}}
    <div class="col-lg-8 col-sm-12 d-flex flex-column gap-4">
        <form action="<?= route('profile.change'); ?>" enctype="multipart/form-data" method="POST" class="card profile-card">
            @csrf
            <div class="card-header">
                <div class="card-title"><i class="bx bx-user-circle"></i> {{__('general.update_profile')}}</div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">{{__('general.full_name')}}</label>
                        <input class="form-control" name="name" value="<?= auth()->user()->name; ?>" type="text" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{__('general.email')}}</label>
                        <input class="form-control" name="email" value="<?= auth()->user()->email; ?>" type="email" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{__('general.wa_phone')}}</label>
                        <input class="form-control" name="phone" value="<?= auth()->user()->phone; ?>" type="number" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{__('auth.gender')}}</label>
                        <select class="form-control" name="gender">
                            <option value="male" @if(auth()->user()->gender=='male') selected @endif>{{__('auth.male')}}</option>
                            <option value="female" @if(auth()->user()->gender=='female') selected @endif>{{__('auth.female')}}</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{__('general.photo')}}</label>
                        <input class="dropify" type="file" id="image" name="image"
                               data-default-file="{{asset(auth()->user()->image_data)}}">
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" style="border-radius:10px;padding:10px 24px;font-weight:600;">
                    <i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}
                </button>
            </div>
        </form>

        {{-- Change Password --}}
        <form action="<?= route('profile.password'); ?>" method="POST" class="card profile-card">
            @csrf
            <div class="card-header">
                <div class="card-title"><i class="bx bx-lock-alt"></i> {{__('general.change_password')}}</div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">{{__('auth.current_password')}}</label>
                        <input class="form-control" name="old_password" type="password" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{__('general.new_password')}}</label>
                        <input class="form-control" name="password" type="password" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{__('general.password_confirmation')}}</label>
                        <input class="form-control" name="confirm" type="password" required>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" style="border-radius:10px;padding:10px 24px;font-weight:600;">
                    <i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}
                </button>
            </div>
        </form>
    </div>

    {{-- Right Column: Package --}}
    <div class="col-lg-4 col-sm-12">

        {{-- Package Usage Card --}}
        <div class="pkg-use-card">
            @if($pkg)
            {{-- Header --}}
            <div class="pkg-use-header">
                <div class="pkg-label"><i class="bx bx-package"></i> Paket Aktif Saya</div>
                <div class="pkg-name">{{ $pkg->name }}</div>
                <div>
                    <span class="pkg-price">Rp {{ number_format((float)($pkg->price ?? 0), 0, ',', '.') }}</span>
                    <span class="pkg-period"> / {{ $pkg->days ?? 30 }} Hari</span>
                </div>
                @if(!$isExpired)
                    <div class="pkg-status-badge active"><span class="dot"></span> Aktif · {{ $daysLeft }} hari lagi</div>
                @else
                    <div class="pkg-status-badge expired"><span class="dot"></span> Expired</div>
                @endif
            </div>

            {{-- Expiry bar --}}
            <div class="pkg-expiry">
                <div class="pkg-expiry-label">
                    <span>Masa Aktif</span>
                    <span>Exp: {{ $expireDate }}</span>
                </div>
                <div class="pkg-bar">
                    <div class="pkg-bar-fill" style="width:{{ $daysPct }}%;background:{{ $dayColor }};"></div>
                </div>
            </div>

            {{-- Usage --}}
            <div class="pkg-usage-body">

                {{-- Platform --}}
                <div class="pkg-group-label"><i class="bx bx-devices"></i> Platform (Penggunaan Akun)</div>

                @php
                $platforms = [
                    ['icon'=>'💬','bg'=>'#e8f5e9','label'=>'WA Personal',   'used'=>$cntDevice,  'max'=>$limDevice],
                    ['icon'=>'🟢','bg'=>'#e3f2fd','label'=>'WA Business',   'used'=>$cntWaba,    'max'=>$limWaba],
                    ['icon'=>'✈️','bg'=>'#f3e5f5','label'=>'Telegram',      'used'=>$cntTelegram,'max'=>$limTelegram],
                    ['icon'=>'📷','bg'=>'#fce4ec','label'=>'Instagram',     'used'=>$cntInsta,   'max'=>$limInsta],
                    ['icon'=>'👤','bg'=>'#e3f2fd','label'=>'Messenger',     'used'=>$cntMsg,     'max'=>$limMsg],
                    ['icon'=>'💬','bg'=>'#e8f5e9','label'=>'Live Chat',     'used'=>$cntLiveChat,'max'=>$limLiveChat],
                ];
                @endphp

                @foreach($platforms as $row)
                @php
                    $pct   = usagePct($row['used'], $row['max']);
                    $color = usageColor($row['used'], $row['max']);
                    $full  = $row['max'] > 0 && $row['used'] >= $row['max'];
                @endphp
                <div class="usage-row">
                    <span class="usage-icon" style="background:{{ $row['bg'] }};">{{ $row['icon'] }}</span>
                    <span class="usage-label">{{ $row['label'] }}</span>
                    <div class="usage-bar-wrap">
                        <div class="usage-mini-bar">
                            <div class="usage-mini-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                        </div>
                        <div class="usage-count {{ $full ? 'full' : '' }}">
                            {{ $row['used'] }} / {{ limitLabel($row['max']) }}
                            @if($full) 🔴 @endif
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Features --}}
                <div class="pkg-group-label" style="margin-top:4px;"><i class="bx bx-cog"></i> Fitur (Penggunaan)</div>

                @php
                $features = [
                    ['icon'=>'🤖','bg'=>'#fff3e0','label'=>'ChatBot',        'used'=>$cntChatbot, 'max'=>$limChatbot],
                    ['icon'=>'🧠','bg'=>'#f3e5f5','label'=>'AI Training',    'used'=>$cntAITrain, 'max'=>$limAITrain],
                    ['icon'=>'👤','bg'=>'#e8f5e9','label'=>'Human Agent',    'used'=>$cntAgent,   'max'=>$limAgent],
                    ['icon'=>'📝','bg'=>'#e3f2fd','label'=>'Msg Template',   'used'=>$cntTemplate,'max'=>$limTemplate],
                ];
                @endphp

                @foreach($features as $row)
                @php
                    $pct   = usagePct($row['used'], $row['max']);
                    $color = usageColor($row['used'], $row['max']);
                    $full  = $row['max'] > 0 && $row['used'] >= $row['max'];
                @endphp
                <div class="usage-row">
                    <span class="usage-icon" style="background:{{ $row['bg'] }};">{{ $row['icon'] }}</span>
                    <span class="usage-label">{{ $row['label'] }}</span>
                    <div class="usage-bar-wrap">
                        <div class="usage-mini-bar">
                            <div class="usage-mini-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                        </div>
                        <div class="usage-count {{ $full ? 'full' : '' }}">
                            {{ $row['used'] }} / {{ limitLabel($row['max']) }}
                            @if($full) 🔴 @endif
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Storage & AI --}}
                <div class="pkg-group-label" style="margin-top:4px;"><i class="bx bx-cloud"></i> Storage & AI</div>
                <div class="pkg-feat-list">
                    <div class="pkg-feat-item">
                        <span class="pkg-feat-icon">☁️</span>
                        <span class="pkg-feat-label">Storage</span>
                        <span class="pkg-feat-val">{{ $limStorage > 0 ? $limStorage . ' MB' : '∞' }}</span>
                    </div>
                    @if($maxPerUpload || $maxTotalRag)
                    <div class="pkg-feat-item">
                        <span class="pkg-feat-icon">📄</span>
                        <span class="pkg-feat-label">RAG File</span>
                        <span class="pkg-feat-val">{{ $maxPerUpload }}MB/file · {{ $maxTotalRag }}MB</span>
                    </div>
                    @endif
                </div>

                {{-- Broadcast & Data --}}
                <div class="pkg-group-label" style="margin-top:4px;"><i class="bx bx-broadcast"></i> Broadcast & Data</div>
                <div class="pkg-feat-list">
                    <div class="pkg-feat-item">
                        <span class="pkg-feat-icon">📨</span>
                        <span class="pkg-feat-label">WA Blast</span>
                        <span class="pkg-feat-val">
                            {{ $limWaBlast <= 0 ? '∞' : number_format($limWaBlast) }}
                            @if($perWaBlast) <small>/{{ $perWaBlast }}</small> @endif
                        </span>
                    </div>
                    <div class="pkg-feat-item">
                        <span class="pkg-feat-icon">📧</span>
                        <span class="pkg-feat-label">Email Blast</span>
                        <span class="pkg-feat-val">
                            {{ $limEmail <= 0 ? '∞' : number_format($limEmail) }}
                            @if($perEmail) <small>/{{ $perEmail }}</small> @endif
                        </span>
                    </div>
                    <div class="pkg-feat-item">
                        <span class="pkg-feat-icon">🔍</span>
                        <span class="pkg-feat-label">Data Scraping</span>
                        <span class="pkg-feat-val">
                            {{ $limScrapp <= 0 ? '∞' : number_format($limScrapp) }}
                            @if($perScrapp) <small>/{{ $perScrapp }}</small> @endif
                        </span>
                    </div>
                </div>

                {{-- Integration --}}
                <div class="pkg-group-label" style="margin-top:4px;"><i class="bx bx-link"></i> Integrasi</div>
                <div class="pkg-feat-list">
                    <div class="pkg-feat-item">
                        <span class="pkg-feat-icon">{{ $hasCekOngkir ? '✅' : '⬜' }}</span>
                        <span class="pkg-feat-label" style="{{ $hasCekOngkir ? '' : 'color:#bbb;' }}">Cek Ongkir</span>
                        <span class="pkg-feat-val" style="{{ $hasCekOngkir ? 'color:#27ae60;' : 'color:#bbb;' }}">{{ $hasCekOngkir ? 'Aktif' : 'Tidak' }}</span>
                    </div>
                    <div class="pkg-feat-item">
                        <span class="pkg-feat-icon">{{ $hasGoogleSheet ? '✅' : '⬜' }}</span>
                        <span class="pkg-feat-label" style="{{ $hasGoogleSheet ? '' : 'color:#bbb;' }}">Google Sheet</span>
                        <span class="pkg-feat-val" style="{{ $hasGoogleSheet ? 'color:#27ae60;' : 'color:#bbb;' }}">{{ $hasGoogleSheet ? 'Aktif' : 'Tidak' }}</span>
                    </div>
                </div>

            </div>

            <a href="{{ route('packages') }}" class="pkg-upgrade-btn">🚀 Lihat / Upgrade Paket</a>

            @else
            <div class="no-pkg-box">
                <div class="icon">📦</div>
                <h6>Belum Ada Paket Aktif</h6>
                <p>Pilih paket yang sesuai kebutuhan bisnis kamu</p>
                <a href="{{ route('packages') }}" class="btn btn-primary" style="border-radius:10px;font-size:13px;">
                    Pilih Paket Sekarang
                </a>
            </div>
            @endif
        </div>

    </div>{{-- end pkg right col --}}
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/dropify/js/dropify.min.js')}}"></script>
<script>
    $(document).ready(function() { $('.dropify').dropify(); });
</script>
@endsection