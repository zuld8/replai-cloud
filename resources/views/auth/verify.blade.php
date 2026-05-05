@extends('layouts.auth')
@section('content')
<style>.logo-container{display:none!important}</style>

<div class="auth-split-wrapper">

  {{-- RIGHT: Green Panel --}}
  <div class="auth-right">
    <div class="auth-right-inner">
      <div class="auth-right-badge">✉️ Email Verification</div>
      <h2 class="auth-right-title">Satu Langkah<br>Lagi!</h2>
      <p class="auth-right-desc">Verifikasi email Anda untuk mengaktifkan akun dan mulai menggunakan semua fitur Replai.</p>
      <ul class="auth-feat-list">
        <li><span class="feat-check">✓</span> Cek inbox email Anda</li>
        <li><span class="feat-check">✓</span> Klik link verifikasi</li>
        <li><span class="feat-check">✓</span> Mulai gunakan Replai!</li>
      </ul>
    </div>
    <div class="auth-orb auth-orb-1"></div>
    <div class="auth-orb auth-orb-2"></div>
    <div class="auth-orb auth-orb-3"></div>
  </div>

  {{-- LEFT: Verify Content --}}
  <div class="auth-left" style="flex:1;overflow-y:auto;max-height:100vh;">
    <div class="auth-topbar">
      <a href="{{route('login')}}" class="auth-brand">
        <img src="{{asset($internalSetting->logo)}}" alt="Replai" style="max-height:36px;">
      </a>
    </div>
    <div style="padding:40px 36px 40px;">
      <div style="max-width:480px;margin:0 auto;">

        {{-- Email Icon Animation --}}
        <div style="text-align:center;margin-bottom:32px;">
          <div class="verify-icon-wrapper">
            <div class="verify-icon-bg"></div>
            <svg class="verify-icon" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="10" y="20" width="60" height="40" rx="6" stroke="#1a9a5a" stroke-width="3" fill="none"/>
              <path d="M10 26L40 45L70 26" stroke="#1a9a5a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="62" cy="22" r="10" fill="#1a9a5a"/>
              <path d="M58 22L61 25L67 19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>

        <h1 style="font-size:28px;font-weight:700;color:#1a2332;text-align:center;margin-bottom:12px;">
          {{__('auth.email_verify')}}
        </h1>

        <p style="color:#64748b;text-align:center;font-size:15px;line-height:1.6;margin-bottom:28px;">
          {{__('auth.verify_desc')}}
        </p>

        {{-- Success Alert --}}
        @if (session('resent'))
        <div style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #6ee7b7;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
          <div style="width:36px;height:36px;border-radius:50%;background:#38BDF8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"><path d="M5 13l4 4L19 7"/></svg>
          </div>
          <div style="font-size:14px;color:#065f46;font-weight:500;">
            {{__('auth.after_send_verify')}}
          </div>
        </div>
        @endif

        <x-validation-component></x-validation-component>

        {{-- Steps Info --}}
        <div style="background:#f8fafc;border-radius:16px;padding:24px;margin-bottom:28px;">
          <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:16px;">
            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#1a9a5a,#16a34a);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">1</div>
            <div>
              <div style="font-weight:600;color:#1a2332;font-size:14px;">Buka Email Anda</div>
              <div style="color:#94a3b8;font-size:13px;margin-top:2px;">Cek inbox atau folder spam</div>
            </div>
          </div>
          <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:16px;">
            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#1a9a5a,#16a34a);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">2</div>
            <div>
              <div style="font-weight:600;color:#1a2332;font-size:14px;">Klik Link Verifikasi</div>
              <div style="color:#94a3b8;font-size:13px;margin-top:2px;">Klik tombol atau link yang ada di email</div>
            </div>
          </div>
          <div style="display:flex;align-items:flex-start;gap:16px;">
            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#1a9a5a,#16a34a);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">3</div>
            <div>
              <div style="font-weight:600;color:#1a2332;font-size:14px;">Akun Aktif!</div>
              <div style="color:#94a3b8;font-size:13px;margin-top:2px;">Langsung masuk dan mulai gunakan Replai</div>
            </div>
          </div>
        </div>

        {{-- Resend Button --}}
        <form action="<?= route('verification.resend'); ?>" method="POST" style="margin-bottom:16px;">
          @csrf
          <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;border-radius:12px;font-weight:600;font-size:15px;background:linear-gradient(135deg,#1a9a5a,#16a34a);border:none;box-shadow:0 4px 14px rgba(26,154,90,0.3);transition:all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(26,154,90,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 14px rgba(26,154,90,0.3)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;vertical-align:middle;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
            {{__('auth.send_try_verify')}}
          </button>
        </form>

        {{-- Back to Login --}}
        <div style="text-align:center;margin-top:20px;">
          <span style="color:#94a3b8;font-size:14px;">{{__('auth.back_to_page')}}</span>
          <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
             style="color:#1a9a5a;font-weight:600;font-size:14px;text-decoration:none;">
            {{__('auth.login')}}
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
.verify-icon-wrapper {
  position: relative;
  display: inline-block;
  width: 100px;
  height: 100px;
}

.verify-icon-bg {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(26,154,90,0.1), rgba(22,163,74,0.05));
  animation: verify-pulse 2s ease-in-out infinite;
}

.verify-icon {
  position: relative;
  width: 80px;
  height: 80px;
  margin-top: 10px;
  animation: verify-float 3s ease-in-out infinite;
}

@keyframes verify-pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.15); opacity: 0.6; }
}

@keyframes verify-float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-6px); }
}
</style>
@endsection
