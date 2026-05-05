@extends('layouts.app')


@section('button')
<div class="btn-list">
    <a href="{{ route('stores') }}" class="btn btn-dark">
        <i class="bx bx-list-ul"></i>
        {{__('contact.list_view')}}
    </a>
    <a href="{{ route('stores.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle"></i>
        {{__('general.add_data')}}
    </a>
</div>

@endsection

@section('styles')

@endsection


@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="row" id="app">
            <kanban-template></kanban-template>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script> 
    window.i18n = {
        locale: '{{ app()->getLocale() }}',
        translations: @json(__('contact'))
    };
</script>
<script src="/js/kanban-template.js"></script>
@endsection