@extends('errors.layout')
@section('title', 'Sesi Berakhir')
@section('content')
    <div class="error-icon">⏰</div>
    <div class="error-code">419</div>
    <h1 class="error-title">Sesi Login Telah Berakhir</h1>
    <p class="error-desc">
        Sesi Anda sudah habis masa berlakunya demi keamanan.
        Anda akan otomatis diarahkan ke halaman login.
    </p>
    <div class="error-actions">
        <a href="{{url('/login')}}" class="btn-primary-err">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
            Login Sekarang
        </a>
    </div>
    <div class="redirect-bar" id="redirectCountdown" data-seconds="5" data-url="{{url('/login')}}">
        <div class="redirect-text">Redirect otomatis dalam <span id="countNumber">5</span> detik</div>
        <div class="progress-track"><div class="progress-fill" style="--duration:5s"></div></div>
    </div>
@endsection
