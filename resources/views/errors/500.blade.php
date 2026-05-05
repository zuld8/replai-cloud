@extends('errors.layout')
@section('title', 'Terjadi Kesalahan')
@section('content')
    <div class="error-icon">⚙️</div>
    <div class="error-code glitch" data-text="500">500</div>
    <h1 class="error-title">Oops, Terjadi Kesalahan!</h1>
    <p class="error-desc">
        Server kami sedang mengalami masalah. Tim teknis sudah diberitahu.
        Halaman akan otomatis di-refresh.
    </p>
    <div class="error-actions">
        <a href="{{url('/')}}" class="btn-primary-err">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
            Kembali ke Dashboard
        </a>
        <a href="javascript:location.reload()" class="btn-secondary-err">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            Reload Sekarang
        </a>
    </div>
    <div class="redirect-bar" id="redirectCountdown" data-seconds="8" data-url="{{url('/')}}">
        <div class="redirect-text">Redirect ke dashboard dalam <span id="countNumber">8</span> detik</div>
        <div class="progress-track"><div class="progress-fill" style="--duration:8s"></div></div>
    </div>
@endsection
