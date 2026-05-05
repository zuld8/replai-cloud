@extends('layouts.auth')
@section('content')
<style>.logo-container{display:none!important}</style>

<div class="auth-split-wrapper">
  <div class="auth-right">
    <div class="auth-right-inner">
      <div class="auth-right-badge">🛡️ Konfirmasi</div>
      <h2 class="auth-right-title">Verifikasi<br>Identitas</h2>
      <p class="auth-right-desc">Demi keamanan, masukkan password Anda untuk melanjutkan.</p>
      <ul class="auth-feat-list">
        <li><span class="feat-check">✓</span> Keamanan akun terjaga</li>
        <li><span class="feat-check">✓</span> Proteksi data sensitif</li>
        <li><span class="feat-check">✓</span> Verifikasi sekali saja</li>
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
              <path d="M40 12L60 22v16c0 14-8.5 26-20 30-11.5-4-20-16-20-30V22L40 12z" stroke="#1a9a5a" stroke-width="3" fill="none"/>
              <path d="M32 42l6 6 12-12" stroke="#1a9a5a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>

        <h1 style="font-size:26px;font-weight:700;color:#1a2332;text-align:center;margin-bottom:8px;">
          {{ __('Confirm Password') }}
        </h1>
        <p style="color:#64748b;text-align:center;font-size:14px;line-height:1.6;margin-bottom:28px;">
          {{ __('Please confirm your password before continuing.') }}
        </p>

        <x-validation-component></x-validation-component>

        <form method="POST" action="{{ route('password.confirm') }}">
          @csrf
          <div style="margin-bottom:24px;">
            <label for="password" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password"
              style="padding:12px 14px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:14px;transition:all 0.3s;"
              onfocus="this.style.borderColor='#1a9a5a';this.style.boxShadow='0 0 0 3px rgba(26,154,90,0.1)'"
              onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
            @error('password')
              <div style="color:#ef4444;font-size:13px;margin-top:4px;">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" style="width:100%;padding:14px;border-radius:12px;font-weight:600;font-size:15px;background:linear-gradient(135deg,#1a9a5a,#16a34a);border:none;color:white;box-shadow:0 4px 14px rgba(26,154,90,0.3);transition:all 0.3s ease;cursor:pointer;"
            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(26,154,90,0.4)'"
            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 14px rgba(26,154,90,0.3)'">
            🛡️ {{ __('Confirm Password') }}
          </button>

          @if (Route::has('password.request'))
          <div style="text-align:center;margin-top:20px;">
            <a href="{{ route('password.request') }}" style="color:#1a9a5a;font-weight:600;font-size:14px;text-decoration:none;">
              {{ __('Forgot Your Password?') }}
            </a>
          </div>
          @endif
        </form>

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
