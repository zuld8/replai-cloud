@extends('errors.layout')
@section('title', 'Terlalu Banyak Permintaan')
@section('content')
    <div class="error-icon">🛑</div>
    <div class="error-code">429</div>
    <h1 class="error-title">Terlalu Banyak Permintaan</h1>
    <p class="error-desc">
        Anda telah mengirim terlalu banyak permintaan.
        Halaman akan otomatis di-refresh setelah cooldown.
    </p>
    <div class="error-actions">
        <a href="{{url('/')}}" class="btn-primary-err">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
            Dashboard
        </a>
    </div>
    <div class="redirect-bar" id="redirectCountdown" data-seconds="10" data-url="javascript:location.reload()">
        <div class="redirect-text">Auto-retry dalam <span id="countNumber">10</span> detik</div>
        <div class="progress-track"><div class="progress-fill" style="--duration:10s"></div></div>
    </div>
@endsection
