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
<script src="https://cdnjs.cloudflare.com/ajax/libs/mermaid/10.9.1/mermaid.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  // Mermaid diinit sebelum Vue bundle, dengan securityLevel:'loose' biar click handler jalan
  mermaid.initialize({ startOnLoad: false, securityLevel: 'loose', theme: 'base',
    logLevel: 'error',
    themeVariables: { fontSize: '13px', fontFamily: 'Inter, system-ui, sans-serif' }
  });
</script>
<script src="{{ mix('js/app-menu-builder.js') }}"></script>
@endsection
