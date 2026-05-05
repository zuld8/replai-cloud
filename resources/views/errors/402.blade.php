@extends('errors.layout')
@section('title', 'Pembayaran Diperlukan')
@section('content')
    <div class="error-icon">💳</div>
    <div class="error-code">402</div>
    <h1 class="error-title">Upgrade Diperlukan</h1>
    <p class="error-desc">
        Fitur ini memerlukan paket berlangganan aktif.
        Upgrade paket Anda untuk mengakses seluruh fitur Replai.
    </p>
    <div class="error-actions">
        <a href="{{url('/')}}" class="btn-primary-err">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            Lihat Paket
        </a>
        <a href="javascript:history.back()" class="btn-secondary-err">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>
@endsection
