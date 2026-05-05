@extends('vendor.installer.layouts.master')

@section('template_title')
{{ trans('installer_messages.environment.menu.templateTitle') }}
@endsection

@section('title')
<i class="bx bx-cog fa-fw" aria-hidden="true"></i>
{!! trans('installer_messages.environment.menu.title') !!}
@endsection

@section('container')

<p class="text-center">
    {!! trans('installer_messages.environment.menu.desc') !!}
</p>
<p class="buttons">
    <a href="{{ route('MdhLicense::environmentWizard') }}" class="button button-wizard">
        <i class="bx bx-slider fa-fw" aria-hidden="true"></i> {{ trans('installer_messages.environment.menu.wizard-button') }}
    </a>

</p>

@endsection