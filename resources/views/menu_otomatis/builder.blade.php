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
{{-- builder duluan & defer: UI muncul cepat, gak nunggu mermaid --}}
<script defer src="{{ mix('js/app-menu-builder.js') }}"></script>
{{-- mermaid nyicil di background (defer, non-blocking); dipakai cuma pas buka diagram --}}
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/mermaid/10.9.1/mermaid.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
