@extends('layouts.crm')

@section('styles')
<link href="{{asset('assets/css/pages/crm.css')}}" rel="stylesheet">
@endsection
@section('content')
<div id="app" class="row"></div>

@endsection

@section('scripts')
<script>
    // Pass Laravel translations to Vue
    window.i18n = {
        locale: '{{ app()->getLocale() }}',
        translations: @json(__('crm'))
    };
</script>
<script src="/js/app-crm.js?v={{ filemtime(public_path('js/app-crm.js')) }}"></script>
@endsection