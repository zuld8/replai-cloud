@extends('layouts.app')


@section('button')
<div class="btn-list">
    <a href="{{route('templates')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i>
        {{__('template.back_to_list')}}
    </a>
    <a href="{{route('templates')}}" class="btn btn-info d-sm-none btn-icon" aria-label="{{__('template.back_to_list')}}">
        <i class="bx bx-chevron-left"></i>
    </a>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/pages/template.css')}}">
@endsection


@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="row" id="app">
            <whatsapp-template></whatsapp-template>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script> 
    window.i18n = {
        locale: '{{ app()->getLocale() }}',
        translations: @json(__('template'))
    };
</script>
<script src="/js/whatsapp-template.js"></script>
@endsection