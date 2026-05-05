@extends('errors.layout')
@section('title', 'Akses Ditolak')
@section('content')
    <div class="error-icon">🚫</div>
    <div class="error-code">403</div>
    <h1 class="error-title">Akses Ditolak</h1>
    <p class="error-desc">
        Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
        Hubungi administrator jika Anda merasa ini adalah kesalahan.
    </p>
    <div class="error-actions">
        <a href="{{url('/')}}" class="btn-primary-err">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
            Kembali ke Dashboard
        </a>
        <a href="javascript:history.back()" class="btn-secondary-err">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
            Halaman Sebelumnya
        </a>
    </div>
@endsection
