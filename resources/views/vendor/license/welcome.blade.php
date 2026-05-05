@extends('vendor.license.layouts.master')


@section('template_title')
{{ trans('installer_messages.welcome.templateTitle') }}
@endsection

@section('title')
Verifikasi Lisensi Pembelian
@endsection

@section('container')
<p class="text-center">
To use this product, you need to verify your purchase license. <a href="https://product.mdh-digital.com/" target="_blank">Member Area</a>
</p>
<p class="text-center">
  <a href="{{ route('license.validation') }}" class="button">
  Continue
    <i class="bx bx-chevron-right fa-fw" aria-hidden="true"></i>
  </a>
</p>
@endsection