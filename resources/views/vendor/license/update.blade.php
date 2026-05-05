@extends('vendor.license.layouts.master')

@section('title')
Update Validation License
@endsection

@section('container')
<div class="tabs tabs-full">

    <input id="tab1" type="radio" name="tabs" class="tab-input" checked />

    <form method="post" id="updateLicense" class="tabs-wrap">
        <div class="tab" id="tab1content">
            @csrf

            <ul class="list">
                <li class="list__item success text-center">
                    Site And Device Information
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

            <div class="form-group {{ $errors->has('purchase') ? ' has-error ' : '' }}">
                <label for="purchase">
                    Purchase Code
                </label>
                <input type="text" name="purchase" required id="purchase" value="<?= $license->purchase; ?>" placeholder="Input Your Purchase Code" />
                @if ($errors->has('purchase'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('purchase') }}
                </span>
                @endif
            </div>

            <div class="form-group {{ $errors->has('email') ? ' has-error ' : '' }}">
                <label for="username">
                    Email 
                </label>
                <input type="hidden" name="product" value="replai"/>
                <input type="text" name="email" id="username" required value="<?= $license->email; ?>" placeholder="Input Your Email" />
                @if ($errors->has('email'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('email') }}
                </span>
                @endif
            </div>


            <div class="buttons">
                <button class="button" type="submit" id="submitLoading">
                    License Activation
                    <i class="bx bx-chevron-right fa-fw" aria-hidden="true"></i>
                </button>
            </div>
        </div>

    </form>

</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/jquery/jquery.js')}}"></script>
<script src="{{ asset('assets/libs/sweetalert/sweetalert2.all.min.js') }}"></script> 
<script src="{{ asset('assets/js/installer.js') }}"></script>
@endsection