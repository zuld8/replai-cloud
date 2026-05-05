@extends('layouts.auth')
@section('content')
<style>.logo-container{display:none!important}</style>

<div class="auth-split-wrapper">
  <div class="auth-right">
    <div class="auth-right-inner">
      <div class="auth-right-badge">🔑 New Password</div>
      <h2 class="auth-right-title">Buat Password<br>Baru</h2>
      <p class="auth-right-desc">Pilih password yang kuat untuk melindungi akun Anda.</p>
      <ul class="auth-feat-list">
        <li><span class="feat-check">✓</span> Minimal 8 karakter</li>
        <li><span class="feat-check">✓</span> Kombinasi huruf & angka</li>
        <li><span class="feat-check">✓</span> Langsung bisa login</li>
      </ul>
    </div>
    <div class="auth-orb auth-orb-1"></div>
    <div class="auth-orb auth-orb-2"></div>
    <div class="auth-orb auth-orb-3"></div>
  </div>

  <div class="auth-left" style="flex:1;overflow-y:auto;max-height:100vh;">
    <div class="auth-topbar">
      <a href="{{route('login')}}" class="auth-brand">
        <img src="{{asset($internalSetting->logo)}}" alt="Replai" style="max-height:36px;">
      </a>
    </div>
    <div style="display:flex;align-items:center;justify-content:center;flex:1;padding:40px 36px;">
      <div style="width:100%;max-width:420px;">

        <div style="text-align:center;margin-bottom:32px;">
          <div style="position:relative;display:inline-block;width:90px;height:90px;">
            <div style="position:absolute;inset:0;border-radius:50%;background:linear-gradient(135deg,rgba(26,154,90,0.1),rgba(22,163,74,0.05));animation:verify-pulse 2s ease-in-out infinite;"></div>
            <svg style="position:relative;width:70px;height:70px;margin-top:10px;" viewBox="0 0 80 80" fill="none">
              <rect x="20" y="30" width="40" height="32" rx="6" stroke="#1a9a5a" stroke-width="3" fill="none"/>
              <path d="M30 30V24c0-5.5 4.5-10 10-10s10 4.5 10 10v6" stroke="#1a9a5a" stroke-width="3" stroke-linecap="round" fill="none"/>
              <circle cx="40" cy="46" r="4" fill="#1a9a5a"/>
              <path d="M40 50v4" stroke="#1a9a5a" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
          </div>
        </div>

        <h1 style="font-size:26px;font-weight:700;color:#1a2332;text-align:center;margin-bottom:8px;">
          {{__('auth.reset_password')}}
        </h1>
        <p style="color:#64748b;text-align:center;font-size:14px;line-height:1.6;margin-bottom:28px;">
          Masukkan password baru untuk akun Anda
        </p>

        <x-validation-component></x-validation-component>

        <form action="{{ route('password.update') }}" method="POST">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <div style="margin-bottom:18px;">
            <label for="email" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{__('auth.email_label')}}</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $email ?? old('email') }}" placeholder="{{__('auth.email_placeholder')}}"
              style="padding:12px 14px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:14px;background:#f8fafc;" readonly>
          </div>

          <div style="margin-bottom:18px;">
            <label for="password" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{__('auth.password_label')}}</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="{{__('auth.password_placeholder')}}"
              style="padding:12px 14px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:14px;transition:all 0.3s;"
              onfocus="this.style.borderColor='#1a9a5a';this.style.boxShadow='0 0 0 3px rgba(26,154,90,0.1)'"
              onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
          </div>

          <div style="margin-bottom:24px;">
            <label for="password_confirmation" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{__('general.password_confirmation')}}</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{__('auth.password_confirmation_placeholder')}}"
              style="padding:12px 14px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:14px;transition:all 0.3s;"
              onfocus="this.style.borderColor='#1a9a5a';this.style.boxShadow='0 0 0 3px rgba(26,154,90,0.1)'"
              onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
          </div>

          <button type="submit" style="width:100%;padding:14px;border-radius:12px;font-weight:600;font-size:15px;background:linear-gradient(135deg,#1a9a5a,#16a34a);border:none;color:white;box-shadow:0 4px 14px rgba(26,154,90,0.3);transition:all 0.3s ease;cursor:pointer;"
            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(26,154,90,0.4)'"
            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 14px rgba(26,154,90,0.3)'">
            🔒 {{__('auth.change_password')}}
          </button>
        </form>

        <div style="text-align:center;margin-top:24px;">
          <a href="{{route('login')}}" style="color:#1a9a5a;font-weight:600;font-size:14px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
            {{__('auth.back_to_page')}} {{__('auth.login')}}
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
@keyframes verify-pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.15); opacity: 0.6; }
}
</style>
@endsection
