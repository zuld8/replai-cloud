@extends('layouts.auth')

@section('content')
<style>
/* Override auth.blade.php header di login page */
.logo-container { display: none !important; }
body { background: #fff !important; }
.auth-btn-trial {
    display: block;
    background: linear-gradient(135deg, #00b09b, #96c93d);
    color: #fff !important;
    font-weight: 700;
    font-size: .95rem;
    text-align: center;
    padding: 14px 20px;
    border-radius: 12px;
    text-decoration: none !important;
    box-shadow: 0 4px 16px rgba(0,176,155,.35);
    transition: transform .2s, box-shadow .2s;
    letter-spacing: .3px;
}
.auth-btn-trial:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,176,155,.45);
}
.auth-trial-cta { margin-top: 20px; }
</style>

<div class="auth-split-wrapper">

    {{-- LEFT: Form Panel --}}
    <div class="auth-left">
        <div class="auth-topbar">
            <a href="{{route('login')}}" class="auth-brand">
                <img src="{{asset($internalSetting->logo)}}" alt="Replai" style="max-height:38px;">
            </a>
            {{-- Language Selector --}}
            <div class="dropdown">
                <button class="lang-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fe fe-globe"></i>
                    @if(current_lang()=='id')<img src="{{asset('assets/img/flags/indonesia.png')}}" class="flag-img" alt="ID">
                    @elseif(current_lang()=='en')<img src="{{asset('assets/img/flags/english.png')}}" class="flag-img" alt="EN">
                    @elseif(current_lang()=='hi')<img src="{{asset('assets/img/flags/india.png')}}" class="flag-img" alt="HI">
                    @elseif(current_lang()=='pt')<img src="{{asset('assets/img/flags/portugal.png')}}" class="flag-img" alt="PT">
                    @elseif(current_lang()=='es')<img src="{{asset('assets/img/flags/spanyol.png')}}" class="flag-img" alt="ES">
                    @elseif(current_lang()=='de')<img src="{{asset('assets/img/flags/de.svg')}}" class="flag-img" alt="DE">
                    @elseif(current_lang()=='ar')<img src="{{asset('assets/img/flags/arab.png')}}" class="flag-img" alt="AR">
                    @elseif(current_lang()=='ja')<img src="{{asset('assets/img/flags/jp.svg')}}" class="flag-img" alt="JA">
                    @elseif(current_lang()=='nl')<img src="{{asset('assets/img/flags/nl.svg')}}" class="flag-img" alt="NL">
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end lang-dropdown">
                    @foreach([['id','indonesia.png'],['en','english.png'],['hi','india.png'],['pt','portugal.png'],['es','spanyol.png'],['de','de.svg'],['ar','arab.png'],['ja','jp.svg'],['nl','nl.svg']] as $lng)
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('setlang',$lng[0]) }}">
                            <img src="{{asset('assets/img/flags/'.$lng[1])}}" style="width:18px;height:13px;object-fit:cover;border-radius:2px;">
                            {{ strtoupper($lng[0]) }}
                            @if(current_lang()==$lng[0])<i class="bx bx-check ms-auto text-success"></i>@endif
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="auth-form-area">
            <div class="auth-form-box">
                <span class="auth-greeting">👋 Selamat Datang Kembali!</span>
                <h1 class="auth-title">Masuk ke Dashboard</h1>
                <p class="auth-subtitle">Masukkan detail akun Anda untuk mengakses dashboard.</p>

                <x-validation-component></x-validation-component>

                <form action="{{ route('login') }}" method="POST" class="mt-3">
                    @csrf
                    <div class="auth-field">
                        <label class="auth-label">{{__('auth.email_label')}}</label>
                        <input type="email" class="auth-input" name="email" id="email"
                               value="{{old('email')}}" placeholder="nama@email.com" autocomplete="email">
                    </div>
                    <div class="auth-field">
                        <label class="auth-label">{{__('auth.password_label')}}</label>
                        <div class="auth-input-wrap">
                            <input type="password" class="auth-input" name="password" id="password"
                                   placeholder="••••••••" autocomplete="current-password">
                            <button type="button" class="toggle-eye" onclick="togglePass('password',this)" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="auth-row-between">
                        <label class="auth-remember">
                            <input type="checkbox" name="remember"> Ingat Saya
                        </label>
                        <a href="{{ route('password.request') }}" class="auth-forgot">{{__('auth.forget_password')}}</a>
                    </div>
                    <button type="submit" class="auth-btn-primary">Masuk Dashboard</button>
                </form>

                <p class="auth-switch-text">
                    Belum punya akun? <a href="{{route('register')}}" class="auth-link">Buat Akun Baru</a>
                </p>
                <p class="auth-copy">© {{date('Y')}} Replai. All rights reserved.</p>
            </div>
        </div>
    </div>

    {{-- RIGHT: Green Marketing Panel --}}
    <div class="auth-right">
        <div class="auth-right-inner">
            <div class="auth-right-badge">✦ Platform All-in-One</div>
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.35);border-radius:30px;padding:6px 16px;font-size:.8rem;font-weight:700;color:#fff;margin-bottom:16px;backdrop-filter:blur(8px)">
                🎁 <span>10 Hari Gratis — Mulai Sekarang</span>
            </div>
            <h2 class="auth-right-title">Tingkatkan Penjualan<br>& Layanan 24/7<br>dengan Replai AI</h2>
            <p class="auth-right-desc">Platform CRM + AI Agent + Omni Channel untuk mengelola percakapan pelanggan, broadcast pesan, dan meningkatkan penjualan secara otomatis.</p>
            <ul class="auth-feat-list">
                <li><span class="feat-check">✓</span> Broadcast Unlimited tanpa batas</li>
                <li><span class="feat-check">✓</span> AI Chatbot Auto Reply 24/7</li>
                <li><span class="feat-check">✓</span> Real-time Analytics & CRM</li>
                <li><span class="feat-check">✓</span> Multi-Channel: WA, IG, Telegram</li>
            </ul>
        </div>
        <div class="auth-orb auth-orb-1"></div>
        <div class="auth-orb auth-orb-2"></div>
        <div class="auth-orb auth-orb-3"></div>
    </div>

</div>

<script>
function togglePass(id,btn){
    const i=document.getElementById(id);
    i.type=i.type==='password'?'text':'password';
    btn.innerHTML=i.type==='text'?'<i class="fas fa-eye-slash"></i>':'<i class="fas fa-eye"></i>';
}
</script>
@endsection
