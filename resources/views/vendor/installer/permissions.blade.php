@extends('vendor.installer.layouts.master')

@section('template_title')
{{ trans('installer_messages.permissions.templateTitle') }}
@endsection

@section('title')
<i class="bx bx-key fa-fw" aria-hidden="true"></i>
{{ trans('installer_messages.permissions.title') }}
@endsection

@section('container')

<ul class="list">
    @foreach($permissions['permissions'] as $permission)
    <li class="list__item list__item--permissions {{ $permission['isSet'] ? 'success' : 'error' }}">
        {{ $permission['folder'] }}
        <span>
            <i class="bx fa-fw bx-{{ $permission['isSet'] ? 'check-circle' : 'x-circle' }}"></i>
            {{ $permission['permission'] }}
        </span>
    </li>
    @endforeach
</ul>

@if ( ! isset($permissions['errors']))
<p class="buttons">
    <a href="{{ route('MdhLicense::environment') }}" class="button">
        {{ trans('installer_messages.permissions.next') }}
        <i class="bx bx-chevron-right fa-fw" aria-hidden="true"></i>
    </a>
</p>
@endif

@endsection