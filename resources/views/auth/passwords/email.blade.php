@extends('layouts.auth')
@section('content')
<style>.logo-container{display:none!important}</style>

<div class="auth-split-wrapper">
  <div class="auth-right">
    <div class="auth-right-inner">
      <div class="auth-right-badge">🔐 Reset Password</div>
      <h2 class="auth-right-title">Lupa<br>Password?</h2>
      <p class="auth-right-desc">Tenang, kami akan mengirimkan link untuk reset password ke email Anda.</p>
      <ul class="auth-feat-list">
        <li><span class="feat-check">✓</span> Masukkan email terdaftar</li>
        <li><span class="feat-check">✓</span> Cek inbox untuk link reset</li>
        <li><span class="feat-check">✓</span> Buat password baru</li>
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
              <rect x="15" y="15" width="50" height="50" rx="12" stroke="#1a9a5a" stroke-width="3" fill="none"/>
              <circle cx="40" cy="35" r="8" stroke="#1a9a5a" stroke-width="2.5" fill="none"/>
              <path d="M28 55c0-6.6 5.4-12 12-12s12 5.4 12 12" stroke="#1a9a5a" stroke-width="2.5" stroke-linecap="round" fill="none"/>
              <path d="M55 30l-4 4-3-3" stroke="#1a9a5a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>

        <h1 style="font-size:26px;font-weight:700;color:#1a2332;text-align:center;margin-bottom:8px;">
          {{__('auth.forgot_password')}}
        </h1>
        <p style="color:#64748b;text-align:center;font-size:14px;line-height:1.6;margin-bottom:28px;">
          {{__('auth.input_your_mail')}}
        </p>

        @if (session('status'))
        <div style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #6ee7b7;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
          <div style="width:36px;height:36px;border-radius:50%;background:#38BDF8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"><path d="M5 13l4 4L19 7"/></svg>
          </div>
          <div style="font-size:14px;color:#065f46;font-weight:500;">{{ session('status') }}</div>
        </div>
        @endif

        <x-validation-component></x-validation-component>

        <form action="{{ route('password.email') }}" method="POST">
          @csrf
          <div style="margin-bottom:20px;">
            <label for="email" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{__('auth.email_label')}}</label>
            <div style="position:relative;">
              <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
              </span>
              <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="{{__('auth.email_placeholder')}}"
                style="padding:12px 14px 12px 44px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:14px;transition:all 0.3s;"
                onfocus="this.style.borderColor='#1a9a5a';this.style.boxShadow='0 0 0 3px rgba(26,154,90,0.1)'"
                onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
            </div>
          </div>

          <button type="submit" style="width:100%;padding:14px;border-radius:12px;font-weight:600;font-size:15px;background:linear-gradient(135deg,#1a9a5a,#16a34a);border:none;color:white;box-shadow:0 4px 14px rgba(26,154,90,0.3);transition:all 0.3s ease;cursor:pointer;"
            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(26,154,90,0.4)'"
            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 14px rgba(26,154,90,0.3)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;vertical-align:middle;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
            {{__('auth.ask_reset_password')}}
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
