@extends('vendor.installer.layouts.master')

@section('template_title')
Purchase Code Activation
@endsection

@section('title')
Purchase Code Activation
@endsection

@section('container')

<div class="tabs tabs-full">

    @if ($message = Session::get('failed'))
    <div class="alert alert-danger" role="alert">
        {{$message}}
    </div>
    @endif

    <ul class="list">
        <li class="list__item success text-center">
            Site and Device Information
        </li>
        <li class="list__item success">

            @php
            $url = url()->full();
            $domain = parse_url($url, PHP_URL_HOST);
            @endphp
            Domain : {{$domain}}
        </li>
        <li class="list__item success">
            Device Name : {{getHostName()}}
        </li>
    </ul>

    <form method="post" action="{{ route('MdhLicense::licenseStore') }}" class="tabs-wrap">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group {{ $errors->has('purchase') ? ' has-error ' : '' }}">
            <label for="purchase" style="margin-bottom:0px;">
               Purchase Code
            </label>
            <small>Get Purchase Code at the Following Link <a href="https://replai.org/login" target="_blank">Replai Forum</a></small>
            <input type="text" name="purchase" id="purchase" value="" required="" placeholder="Input Purchase Code" />

            @if ($errors->has('purchase'))
            <span class="error-block">
                <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                {{ $errors->first('purchase') }}
            </span>
            @endif
        </div>
        <div class="form-group {{ $errors->has('email') ? ' has-error ' : '' }}" style="margin-top:15px;">
            <label for="username">
                Email
            </label>
            <input type="hidden" name="product" value="replai">
            <input type="text" name="email" id="username" value="" required="" placeholder="Input Your Email" />
            @if ($errors->has('email'))
            <span class="error-block">
                <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                {{ $errors->first('email') }}
            </span>
            @endif
        </div>

        <p class="text-center">
            <button class="button" type="submit">
                Verifikasi
                <i class="bx bx-chevron-right fa-fw" aria-hidden="true"></i>
            </button>
        </p>
    </form>
</div>
<!-- <p class="text-center">
    {{ trans('installer_messages.welcome.message') }}
</p>
<p class="text-center">
    <a href="{{ route('MdhLicense::requirements') }}" class="button">
        {{ trans('installer_messages.welcome.next') }}
        <i class="bx bx-chevron-right fa-fw" aria-hidden="true"></i>
    </a>
</p> -->
@endsection