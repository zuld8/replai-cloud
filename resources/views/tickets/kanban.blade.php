@extends('layouts.app')

@section('styles')

@endsection


@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="row" id="app">
            <ticket-template></ticket-template>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script> 
    window.i18n = {
        locale: '{{ app()->getLocale() }}',
        translations: @json(__('tickets'))
    };
</script>
<script src="/js/ticket-template.js"></script>
@endsection