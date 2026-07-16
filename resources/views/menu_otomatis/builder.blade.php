@extends('layouts.app')

@section('content')
<div id="app"
    data-flow-id="{{ $flowId ?? '' }}"
    data-devices='{{ json_encode($devices ?? []) }}'
    data-csrf="{{ csrf_token() }}">
    <menu-builder></menu-builder>
</div>
@endsection

@section('scripts')
<script src="{{ mix('js/app-menu-builder.js') }}"></script>
@endsection
