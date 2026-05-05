@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.welcome.templateTitle') }}
@endsection

@section('title')
    Scrab N Blash Installation
@endsection

@section('container')
    <p class="text-center">
      {{ trans('installer_messages.welcome.message') }}
    </p>
    <p class="text-center">
      <a href="{{ route('MdhLicense::license') }}" class="button">
        Validasi Kode Pembelian
        <i class="bx bx-chevron-right fa-fw" aria-hidden="true"></i>
      </a>
    </p>
@endsection
