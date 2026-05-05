@extends('vendor.license.layouts.master')

@section('title')
Form Validation License
@endsection

@section('container')
<div class="tabs tabs-full">

    <input id="tab1" type="radio" name="tabs" class="tab-input" checked />



    <form method="post" id="activasiLicensi" class="tabs-wrap">
        <div class="tab" id="tab1content">
            @csrf

            <div class="form-group {{ $errors->has('purchase') ? ' has-error ' : '' }}">
                <label for="purchase">
                    Purchase Code
                </label>
                <input type="text" name="purchase" required id="purchase" value="" placeholder="Input Your Purchase Code" />
                @if ($errors->has('purchase'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('purchase') }}
                </span>
                @endif
            </div>

            <div class="form-group {{ $errors->has('username') ? ' has-error ' : '' }}">
                <label for="username">
                    Email
                </label>
                <input type="hidden" name="product" value="replai" />
                <input type="text" name="email" id="username" required value="" placeholder="Input Your Email" />
                @if ($errors->has('email'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('email') }}
                </span>
                @endif
            </div>


            <p class="buttons">
                <button class="button" type="submit">
                    License Activation
                    <i class="bx bx-chevron-right fa-fw" aria-hidden="true"></i>
                </button>
            </p>
        </div>

    </form>

</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/jquery/jquery.js')}}"></script>
<script src="{{ asset('assets/libs/sweetalert/sweetalert2.all.min.js') }}"></script> 
<script src="{{ asset('assets/js/installer.js') }}"></script>
@endsection