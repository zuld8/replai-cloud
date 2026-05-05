{{-- Error (Merah) --}}
@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert" style="background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; padding: 1rem; margin-bottom: 1rem; margin-top: 0.5rem;">
            {{ $error }}
        </div>
    @endforeach
@endif

{{-- Flash / Success / Status (Biru Muda) --}}
@php
    $successMessage = Session::get('flash') ?? Session::get('success') ?? Session::get('status');
@endphp
@if ($successMessage)
    <div class="alert" style="background-color: #d0ebff; color: #084298; border: 1px solid #b6d4fe; padding: 1rem; margin-bottom: 1rem; margin-top: 0.5rem;">
        {{ $successMessage }}
    </div>
@endif

{{-- Warning / Gagal (Oranye) --}}
@if ($message = Session::get('gagal'))
    <div class="alert" style="background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; padding: 1rem; margin-bottom: 1rem; margin-top: 0.5rem;">
        {{ $message }}
    </div>
@endif
