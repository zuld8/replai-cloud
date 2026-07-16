@extends('layouts.app')

@section('content')
<p id="blade-check" style="color:green;font-weight:bold;padding:10px">✅ Blade rendered OK</p>
<div id="app"
    data-flow-id="{{ $flowId ?? '' }}"
    data-devices='{{ json_encode($devices ?? []) }}'
    data-csrf="{{ csrf_token() }}">
    <menu-builder></menu-builder>
</div>
<script>
// Inline debug — runs BEFORE app-menu-builder.js
window.__mbDebug = {
    appEl: !!document.getElementById('app'),
    flowId: document.getElementById('app')?.dataset?.flowId,
};
console.log('[MenuBuilder] pre-mount:', window.__mbDebug);
window.addEventListener('error', function(e) {
    document.getElementById('blade-check').innerHTML +=
        '<br>❌ JS Error: ' + e.message + ' @ ' + e.filename + ':' + e.lineno;
});
</script>
@endsection

@section('scripts')
<script src="{{ mix('js/app-menu-builder.js') }}" onerror="document.getElementById('blade-check').innerHTML += '<br>❌ Script 404: app-menu-builder.js gagal load'"></script>
@endsection
