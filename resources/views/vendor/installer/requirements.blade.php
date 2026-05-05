@extends('vendor.installer.layouts.master')

@section('template_title')
{{ trans('installer_messages.requirements.templateTitle') }}
@endsection

@section('title')
<i class="bx bx-list-ul fa-fw" aria-hidden="true"></i>
{{ trans('installer_messages.requirements.title') }}
@endsection

@section('container')

@foreach($requirements['requirements'] as $type => $requirement)
<ul class="list">
    <li class="list__item list__title  {{ $phpSupportInfo['supported'] ? 'success' : 'error' }}">
        <strong>{{ ucfirst($type) }}</strong>
        @if($type == 'php')
        <strong>
            <small>
                (version {{ $phpSupportInfo['minimum'] }} required)
            </small>
        </strong>
        <span class="float-right">
            <strong>
                {{ $phpSupportInfo['current'] }}
            </strong>
            <i class="bx bx-{{ $phpSupportInfo['supported'] ? 'check-circle' : 'x-circle' }} row-icon" aria-hidden="true"></i>
        </span>
        @endif
    </li>
    @foreach($requirements['requirements'][$type] as $extention => $enabled)
    <li class="list__item d-flex justify-content-between {{ $enabled ? 'success' : 'error' }}">
        {{ $extention }}
        <i class="bx bx-{{ $enabled ? 'check-circle' : 'x-circle' }} row-icon float-right" aria-hidden="true"></i>
    </li>
    @endforeach
</ul>
@endforeach

@if ( ! isset($requirements['errors']) && $phpSupportInfo['supported'] )
<p class="buttons">
    <a class="button" href="{{ route('MdhLicense::permissions') }}">
        {{ trans('installer_messages.requirements.next') }}
        <i class="bx bx-chevron-right fa-fw" aria-hidden="true"></i>
    </a>
</p>
@endif

@endsection