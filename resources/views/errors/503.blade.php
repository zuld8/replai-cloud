@extends('errors.layout')
@section('title', 'Sedang Maintenance')
@section('content')
    <div class="error-icon">🔧</div>
    <div class="error-code">503</div>
    <h1 class="error-title">Sedang Dalam Perbaikan</h1>
    <p class="error-desc">
        Kami sedang melakukan pemeliharaan untuk meningkatkan layanan.
        Kami akan segera kembali. Terima kasih atas kesabaran Anda! 🙏
    </p>
    <div class="error-actions">
        <a href="javascript:location.reload()" class="btn-primary-err">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            Cek Kembali
        </a>
    </div>
@endsection
