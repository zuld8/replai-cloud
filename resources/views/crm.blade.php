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
<script>
    // INLINE DEBUG - runs from Blade, not cached with app-crm.js
    (function() {
        var d = document.createElement('div');
        d.id = '__blade_debug';
        d.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:2147483647;background:#7c3aed;color:white;padding:4px 8px;font-size:11px;font-weight:bold;font-family:monospace;pointer-events:none;';
        d.textContent = '🟣 BLADE SCRIPT @ {{ date("H:i:s") }} | JS v={{ filemtime(public_path("js/app-crm.js")) }}';
        document.body.appendChild(d);
        // After 3 sec - check if Vue mounted
        setTimeout(function() {
            var app = document.getElementById('app');
            var appInner = app ? app.innerHTML.length : -1;
            var hasDbgBar = !!document.getElementById('__app_crm_debug');
            d.textContent = '🟣 #app innerHTML length: ' + appInner + ' | gc-debug-bar created: ' + hasDbgBar + ' | Vue mounted: ' + (appInner > 10 ? 'YES' : 'NO');
            d.style.background = appInner > 10 ? '#15803d' : '#dc2626';
        }, 3000);
    })();
</script>
<script src="/js/app-crm.js?v={{ filemtime(public_path('js/app-crm.js')) }}&nc={{ rand(1,99999) }}"></script>
@endsection