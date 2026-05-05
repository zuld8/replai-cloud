/* Replai CRM - Theme Presets v2 */
(function(){
  var THEMES = {
    'sky-blue': {
      name: 'Sky Blue', emoji: '🔵',
      primary: '14,165,233', hex: '#0EA5E9', dark: '#0284C7', light: '#38BDF8',
      bg10: '#F0F9FF', bg20: '#E0F2FE', bg30: '#BAE6FD'
    },
    'violet-dream': {
      name: 'Violet Dream', emoji: '🟣',
      primary: '168,85,247', hex: '#A855F7', dark: '#7C3AED', light: '#C084FC',
      bg10: '#FAF5FF', bg20: '#F3E8FF', bg30: '#D8B4FE'
    },
    'emerald': {
      name: 'Emerald', emoji: '🟢',
      primary: '16,185,129', hex: '#10B981', dark: '#059669', light: '#34D399',
      bg10: '#F0FDF4', bg20: '#DCFCE7', bg30: '#A7F3D0'
    },
    'sunset': {
      name: 'Sunset', emoji: '🟠',
      primary: '249,115,22', hex: '#F97316', dark: '#EA580C', light: '#FB923C',
      bg10: '#FFF7ED', bg20: '#FFEDD5', bg30: '#FDBA74'
    },
    'rose': {
      name: 'Rose', emoji: '🔴',
      primary: '244,63,94', hex: '#F43F5E', dark: '#E11D48', light: '#FB7185',
      bg10: '#FFF1F2', bg20: '#FFE4E6', bg30: '#FECDD3'
    }
  };

  function buildCSS(t) {
    var p = t.primary, h = t.hex, d = t.dark, l = t.light;
    var bg1 = t.bg10, bg2 = t.bg20, bg3 = t.bg30;
    return [
      /* === SIDEBAR === */
      '.app-sidebar{background:linear-gradient(180deg,#fff 0%,#F8FAFC 40%,' + bg1 + ' 100%)!important}',
      '.app-sidebar .main-sidebar-header{background:linear-gradient(135deg,' + bg1 + ' 0%,' + bg2 + ' 100%)!important}',
      '.app-sidebar .side-menu__item.active{background:linear-gradient(135deg,' + h + ',' + l + ')!important;box-shadow:0 4px 12px rgba(' + p + ',0.35)!important;color:#fff!important}',
      '.app-sidebar .side-menu__item.active .side-menu__icon,.app-sidebar .side-menu__item.active .side-menu__label{color:#fff!important}',
      '.app-sidebar .side-menu__item.active:hover,.app-sidebar .side-menu__item.active:hover .side-menu__label,.app-sidebar .side-menu__item.active:hover .side-menu__icon{color:#fff!important}',
      '.app-sidebar .side-menu__item:hover:not(.active){background:rgba(' + p + ',0.1)!important;color:' + d + '!important}',
      '.app-sidebar .side-menu__item:hover:not(.active) .side-menu__label{color:' + d + '!important}',
      '.app-sidebar .side-menu__item:hover:not(.active) .side-menu__icon,.app-sidebar .side-menu__item:hover:not(.active) .side-menu__angle{color:' + h + '!important}',
      '.app-sidebar .simplebar-scrollbar::before{background:rgba(' + p + ',0.25)!important}',
      '.app-sidebar .side-menu__item:hover:not(.active) *{color:' + d + '!important}',
      '.app-sidebar .side-menu__item:hover:not(.active) .side-menu__icon{color:' + h + '!important}',
      '.app-sidebar .btn-icon.btn-outline-light:hover{background:' + bg1 + '!important;border-color:' + bg3 + '!important}',
      '.app-sidebar .badge.bg-primary-transparent{background:linear-gradient(135deg,' + bg2 + ',' + bg1 + ')!important;color:' + d + '!important}',
      '.app-sidebar .slide-menu{background:rgba(' + p + ',0.03)!important}',
      '.app-sidebar .slide-menu .nav-sub-link{color:#475569!important}',
      '.app-sidebar .slide-menu .nav-sub-link:hover{color:' + d + '!important;background:rgba(' + p + ',0.06)!important}',
      '.app-sidebar .slide-menu .nav-sub-link.active{color:#fff!important;background:linear-gradient(135deg,' + h + ',' + l + ')!important;border-radius:6px!important}',
      '.app-sidebar .side-menu__item.active .side-menu__label,.app-sidebar .side-menu__item.active .side-menu__icon,.app-sidebar .side-menu__item.active .side-menu__angle{color:#fff!important}',
      '.app-sidebar .has-sub.open > .side-menu__item{background:rgba(' + p + ',0.06)!important}',
      '.app-sidebar .has-sub.open > .side-menu__item .side-menu__label,.app-sidebar .has-sub.open > .side-menu__item .side-menu__icon{color:' + d + '!important}',

      /* === BUTTONS === */
      '.btn-primary{background-color:' + h + '!important;border-color:' + h + '!important}',
      '.btn-primary:hover,.btn-primary:focus{background-color:' + d + '!important;border-color:' + d + '!important;box-shadow:0 4px 12px rgba(' + p + ',0.35)!important}',
      '.btn-outline-primary{color:' + h + '!important;border-color:' + h + '!important}',
      '.btn-outline-primary:hover{background-color:' + h + '!important;color:#fff!important}',
      '.btn-success,.btn-success:hover{background:linear-gradient(135deg,' + h + ',' + l + ')!important;border:none!important;box-shadow:0 2px 8px rgba(' + p + ',0.3)!important}',
      '.btn-outline-success{color:' + h + '!important;border-color:' + h + '!important}',
      '.btn-outline-success:hover{background-color:' + h + '!important;color:#fff!important}',

      /* === BADGES === */
      '.badge.bg-primary,.bg-primary:not(.btn){background-color:' + h + '!important}',
      '.bg-primary-transparent{background-color:rgba(' + p + ',0.1)!important;color:' + d + '!important}',

      /* === TEXTS & LINKS === */
      '.text-primary{color:' + h + '!important}',
      '.text-success:not([style*="22C55E"]){color:' + h + '!important}',
      'a{color:' + h + '}',
      'a:hover{color:' + d + '}',
      '.breadcrumb-item.active,.breadcrumb-item a{color:' + h + '!important}',

      /* === FORMS === */
      '.form-control:focus,.form-select:focus{border-color:' + h + '!important;box-shadow:0 0 0 3px rgba(' + p + ',0.12)!important}',
      '.form-check-input:checked{background-color:' + h + '!important;border-color:' + h + '!important}',

      /* === TABLES & PAGINATION === */
      '.table tbody tr:hover{background-color:' + bg1 + '!important}',
      '.page-item.active .page-link{background-color:' + h + '!important;border-color:' + h + '!important}',
      '.page-link{color:' + h + '!important}',
      '.page-link:hover{color:' + d + '!important;background-color:' + bg1 + '!important}',

      /* === TABS & NAVS === */
      '.nav-tabs .nav-link.active{color:' + h + '!important;border-bottom-color:' + h + '!important}',
      '.nav-pills .nav-link.active{background-color:' + h + '!important}',

      /* === PROGRESS & MISC === */
      '.progress-bar{background-color:' + h + '!important}',
      '.dropdown-item.active,.dropdown-item:active{background-color:' + h + '!important}',
      '.list-hover-focus-bg{background-color:' + bg1 + '!important}',
      '::-webkit-scrollbar-thumb{background:rgba(' + p + ',0.2)}',
      '::-webkit-scrollbar-thumb:hover{background:rgba(' + p + ',0.4)}',

      /* === DASHBOARD SPECIFIC === */
      '.dash-greeting{background:linear-gradient(135deg,' + d + ' 0%,' + h + ' 50%,' + l + ' 100%)!important}',
      '.date-filter-btn.active{background:linear-gradient(135deg,' + h + ',' + l + ')!important;color:#fff!important;border-color:transparent!important;box-shadow:0 2px 6px rgba(' + p + ',0.3)!important}',
      '.date-filter-btn:hover{background:' + bg1 + '!important;color:' + d + '!important;border-color:' + bg3 + '!important}',
      '.chart-selector:hover,.chart-selector:focus,#chartViewSelector:hover,#chartViewSelector:focus{border-color:' + h + '!important;box-shadow:0 0 0 3px rgba(' + p + ',0.1)!important}',
      '.dash-label-card .card-title{color:' + d + '!important}',
      '.dash-label-card .card-header{background:linear-gradient(135deg,' + bg1 + ',' + bg2 + ')!important}',
      '.dash-log-item:hover{background:' + bg1 + '!important;border-color:' + bg3 + '!important}',

      /* === DASHBOARD CARDS (Pesan Baru/Belum) === */
      'div[style*="linear-gradient"][style*="DBEAFE"],div[style*="linear-gradient"][style*="0284C7"]{background:linear-gradient(135deg,' + d + ',' + h + ')!important}',
      '#broadcastStatusSection h6{color:#fff!important}',
      '#broadcastStatusSection small{color:rgba(255,255,255,0.8)!important}',

      /* === HEADER & BREADCRUMB === */
      '.app-header{background:linear-gradient(135deg,' + h + ',' + l + ')!important;border-bottom:none!important;box-shadow:0 2px 12px rgba(' + p + ',0.15)!important}',
      '.app-header .nav-link,.app-header .header-link,.app-header .header-element .header-link{color:rgba(255,255,255,0.85)!important}',
      '.app-header .nav-link:hover,.app-header .header-link:hover{color:#fff!important}',
      '.app-header .header-link-icon:hover,.app-header .header-link:hover .header-link-icon{background-color:rgba(0,0,0,0.12)!important;border-radius:50%!important;transition:all 0.2s ease!important}',
      '.app-header .header-link:hover{background:transparent!important}',
      '[data-header-styles=light] .main-header-container .header-link-icon:hover{background-color:rgba(0,0,0,0.12)!important}',
      '[data-header-styles=color] .main-header-container .header-link-icon:hover{background-color:rgba(0,0,0,0.12)!important}',

      '.app-header .nav-link.active,.app-header .active{color:#fff!important}',
      '.app-header .dropdown-toggle::after{color:rgba(255,255,255,0.7)!important}',
      '.app-header .header-element .header-link .bx,.app-header .header-element .header-link i{color:rgba(255,255,255,0.85)!important}',
      '.app-header .header-element .header-link:hover .bx,.app-header .header-element .header-link:hover i{color:#fff!important}',
      '[data-header-styles="gradient"] .app-header{background:linear-gradient(135deg,' + h + ',' + d + ')!important}',
      '[data-header-styles="gradient"] .app-header .main-header-container .header-element .header-link .header-link-icon{color:rgba(255,255,255,0.85)!important}',
      '.app-header .header-brand-img{filter:brightness(0) invert(1)!important}',
      '.main-header-container .header-element .header-link{color:rgba(255,255,255,0.85)!important}',
      '.header-profile-info .fw-semibold,.header-profile-info span{color:#fff!important}',
      '.page-header-breadcrumb{background:transparent!important}',
      '.breadcrumb-item a{color:' + h + '!important}',
      '.breadcrumb-item.active{color:' + d + '!important}',
      '.breadcrumb-item+.breadcrumb-item::before{color:' + h + '!important}',
      '.page-header .page-title{color:' + d + '!important}',

      /* === DARK MODE === */
      '[data-theme-mode="dark"] .app-sidebar{background:linear-gradient(180deg,#1a1e2e 0%,#151827 50%,#0f111a 100%)!important;border-right:1px solid rgba(' + p + ',0.15)!important}',
      '[data-theme-mode="dark"] .app-sidebar .main-sidebar-header{background:linear-gradient(135deg,#1a1e2e,#151827)!important;border-bottom:1px solid rgba(' + p + ',0.1)!important}',
      '[data-theme-mode="dark"] .app-sidebar .side-menu__label,[data-theme-mode="dark"] .app-sidebar .side-menu__icon{color:rgba(255,255,255,0.7)!important}',
      '[data-theme-mode="dark"] .app-sidebar .side-menu__item:hover:not(.active){background:rgba(' + p + ',0.15)!important}',
      '[data-theme-mode="dark"] .app-sidebar .side-menu__item:hover:not(.active) *{color:rgba(255,255,255,0.9)!important}',
      '[data-theme-mode="dark"] .app-sidebar .side-menu__item:hover:not(.active) .side-menu__icon{color:' + l + '!important}',
      '[data-theme-mode="dark"] .app-sidebar .side-menu__item.active{background:linear-gradient(135deg,' + h + ',' + d + ')!important;box-shadow:0 4px 15px rgba(' + p + ',0.4)!important}',
      '[data-theme-mode="dark"] .app-sidebar .side-menu__angle{color:rgba(255,255,255,0.5)!important}',
      '[data-theme-mode="dark"] .app-sidebar .slide-menu{background:rgba(0,0,0,0.15)!important}',
      '[data-theme-mode="dark"] .app-sidebar .nav-sub-link{color:rgba(255,255,255,0.6)!important}',
      '[data-theme-mode="dark"] .app-sidebar .nav-sub-link:hover{color:' + l + '!important}',
      '[data-theme-mode="dark"] .app-sidebar .badge.bg-primary-transparent{background:rgba(' + p + ',0.2)!important;color:' + l + '!important}',
      '[data-theme-mode="dark"] .app-sidebar .side-menu__category{color:rgba(255,255,255,0.35)!important}',
      '[data-theme-mode="dark"] .main-sidebar-header .header-logo .desktop-logo{filter:brightness(1.5)!important}',

      /* Dark mode - Header */
      '[data-theme-mode="dark"] .app-header{background:linear-gradient(135deg,#1a1e2e,#0f111a)!important;border-bottom:1px solid rgba(' + p + ',0.2)!important}',
      '[data-theme-mode="dark"][data-header-styles="gradient"] .app-header{background:linear-gradient(135deg,rgba(' + p + ',0.15),#1a1e2e,#0f111a)!important}',
      '[data-theme-mode="dark"][data-header-styles="dark"] .app-header{background:linear-gradient(135deg,#1a1e2e,#0f111a)!important}',

      /* Dark mode - Content area */
      '[data-theme-mode="dark"] .page-header .page-title{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .breadcrumb-item a{color:' + l + '!important}',
      '[data-theme-mode="dark"] .breadcrumb-item.active{color:rgba(255,255,255,0.6)!important}',
      '[data-theme-mode="dark"] .breadcrumb-item+.breadcrumb-item::before{color:rgba(255,255,255,0.3)!important}',

      /* Dark mode - Cards */
      '[data-theme-mode="dark"] .card{background:#1e2235!important;border-color:rgba(' + p + ',0.12)!important;color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .card .card-header{background:rgba(' + p + ',0.08)!important;border-bottom:1px solid rgba(' + p + ',0.1)!important}',
      '[data-theme-mode="dark"] .card .card-title,[data-theme-mode="dark"] .card h5,[data-theme-mode="dark"] .card h6{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .card .text-muted{color:rgba(255,255,255,0.5)!important}',

      /* Dark mode - Body/Main */
      '[data-theme-mode="dark"] .main-content{background:#0f111a!important}',
      '[data-theme-mode="dark"] body,[data-theme-mode="dark"] .app-content{background:#0f111a!important;color:#cbd5e1!important}',

      /* Dark mode - Tables */
      '[data-theme-mode="dark"] .table{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .table thead th{background:rgba(' + p + ',0.08)!important;color:#e2e8f0!important;border-color:rgba(' + p + ',0.12)!important}',
      '[data-theme-mode="dark"] .table tbody td{border-color:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .table tbody tr:hover{background:rgba(' + p + ',0.08)!important}',

      /* Dark mode - Forms */
      '[data-theme-mode="dark"] .form-control,[data-theme-mode="dark"] .form-select{background:#151827!important;border-color:rgba(' + p + ',0.2)!important;color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .form-control::placeholder{color:rgba(255,255,255,0.3)!important}',

      /* Dark mode - Dashboard specific */
      '[data-theme-mode="dark"] .dash-greeting{background:linear-gradient(135deg,#1a1e2e,' + d + ',' + h + ')!important}',
      '[data-theme-mode="dark"] .dash-top-card .card{background:#1e2235!important;border-color:rgba(' + p + ',0.15)!important}',
      '[data-theme-mode="dark"] .dash-top-card .card h3{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .dash-top-card .card h6{color:rgba(255,255,255,0.6)!important}',
      '[data-theme-mode="dark"] .dash-label-card .card{background:#1e2235!important}',
      '[data-theme-mode="dark"] .dash-label-card .card-header{background:rgba(' + p + ',0.1)!important}',
      '[data-theme-mode="dark"] .date-filter-btn{background:#151827!important;color:rgba(255,255,255,0.7)!important;border-color:rgba(' + p + ',0.2)!important}',
      '[data-theme-mode="dark"] .date-filter-btn:hover{background:rgba(' + p + ',0.15)!important;color:#fff!important}',
      '[data-theme-mode="dark"] .dash-log-item{background:#1e2235!important;border-color:rgba(' + p + ',0.1)!important}',
      '[data-theme-mode="dark"] .dash-log-item:hover{background:rgba(' + p + ',0.12)!important;border-color:rgba(' + p + ',0.25)!important}',

      /* Dark mode - Scrollbar */
      '[data-theme-mode="dark"] ::-webkit-scrollbar-track{background:#0f111a}',
      '[data-theme-mode="dark"] ::-webkit-scrollbar-thumb{background:rgba(' + p + ',0.3)}',

      /* Dark mode - Dropdown */
      '[data-theme-mode="dark"] .dropdown-menu{background:#1e2235!important;border-color:rgba(' + p + ',0.15)!important}',
      '[data-theme-mode="dark"] .dropdown-item{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .dropdown-item:hover{background:rgba(' + p + ',0.1)!important;color:#fff!important}',

      /* Dark mode - Stat cards */
      '[data-theme-mode="dark"] .card[style*="border:1px dashed"]{background:#1e2235!important;border-color:rgba(' + p + ',0.25)!important}',

      /* Dark mode - CRM Message Cards (hardcoded color overrides) */
      '[data-theme-mode="dark"] .card .fw-semibold[style*="color:#1e293b"]{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .card [style*="color:#1e293b"]{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .card [style*="color:#6b7280"]{color:#94a3b8!important}',
      '[data-theme-mode="dark"] .card [style*="color:#9ca3af"]{color:#64748b!important}',
      '[data-theme-mode="dark"] .card [style*="color:#374151"]{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .card [style*="color:#0C4A6E"]{color:#93c5fd!important}',
      '[data-theme-mode="dark"] .card [style*="color:#1a1a2e"]{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .card h6[style*="color:#0C4A6E"]{color:' + l + '!important}',
      '[data-theme-mode="dark"] .card small[style*="color:#60A5FA"]{color:rgba(' + p + ',0.7)!important}',
      '[data-theme-mode="dark"] .card small[style*="color:#FB923C"]{color:#fb923c!important}',
      '[data-theme-mode="dark"] .card [style*="border-bottom:1px solid #f1f5f9"]{border-color:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .card a[style*="border-bottom:1px solid"]{border-color:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .card a:hover{background:rgba(' + p + ',0.08)!important}',

      /* Dark mode - WABA/WA badges in CRM cards */
      '[data-theme-mode="dark"] [style*="background:#F0F9FF"]{background:rgba(14,165,233,0.15)!important}',
      '[data-theme-mode="dark"] [style*="background:#F0FDF4"]{background:rgba(22,163,74,0.15)!important}',
      '[data-theme-mode="dark"] [style*="background:#F1F5F9"]{background:rgba(100,116,139,0.15)!important}',

      /* Dark mode - Broadcast section */
      '[data-theme-mode="dark"] .blast-item{background:#1e2235!important;border-color:rgba(' + p + ',0.12)!important}',
      '[data-theme-mode="dark"] .blast-item .fw-semibold[style*="color:#1e293b"]{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .blast-item [style*="color:#374151"]{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .blast-item [style*="color:#9ca3af"]{color:#64748b!important}',
      '[data-theme-mode="dark"] .blast-progress{background:rgba(255,255,255,0.08)!important}',
      '[data-theme-mode="dark"] .blast-device-item{background:rgba(255,255,255,0.03)!important;border-color:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .blast-device-item [style*="color:#374151"]{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .blast-device-item [style*="color:#9ca3af"]{color:#64748b!important}',
      '[data-theme-mode="dark"] .blast-device-bar{background:rgba(255,255,255,0.08)!important}',
      '[data-theme-mode="dark"] .toggle-device-btn{background:rgba(' + p + ',0.1)!important;color:' + l + '!important;border-color:rgba(' + p + ',0.2)!important}',

      /* Dark mode - Card header gradients override */
      '[data-theme-mode="dark"] .card-header[style*="linear-gradient(135deg,#EFF6FF"]{background:rgba(' + p + ',0.12)!important}',
      '[data-theme-mode="dark"] .card-header[style*="linear-gradient(135deg,#FFF7ED"]{background:linear-gradient(135deg,rgba(249,115,22,0.2),rgba(234,88,12,0.15))!important}',

      /* Dark mode - broadcast status header */
      '[data-theme-mode="dark"] #broadcastStatusSection .card-header[style*="linear-gradient"]{background:linear-gradient(135deg,rgba(' + p + ',0.2),rgba(' + p + ',0.1))!important}',

      /* Dark mode - stat mini cards (Contact/Leads, Category, AI) */
      '[data-theme-mode="dark"] .card[style*="dashed #CBD5E1"]{background:#1e2235!important;border-color:rgba(' + p + ',0.25)!important}',
      '[data-theme-mode="dark"] .card [style*="color:#64748b"]{color:#94a3b8!important}',

      /* Dark mode - text-muted everywhere */  
      '[data-theme-mode="dark"] .text-muted{color:rgba(255,255,255,0.45)!important}',
      '[data-theme-mode="dark"] small.text-muted{color:rgba(255,255,255,0.4)!important}',

      /* Dark mode - general body text */
      '[data-theme-mode="dark"] h1,[data-theme-mode="dark"] h2,[data-theme-mode="dark"] h3,[data-theme-mode="dark"] h4,[data-theme-mode="dark"] h5,[data-theme-mode="dark"] h6{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] p{color:#cbd5e1}',
      '[data-theme-mode="dark"] label{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .fw-semibold,[data-theme-mode="dark"] .fw-bold{color:#e2e8f0}',

            /* Dark mode - APEXCHARTS (legend, tooltip, labels) */
      '[data-theme-mode="dark"] .apexcharts-legend-text{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .apexcharts-text,.apexcharts-text tspan{fill:#94a3b8!important}',
      '[data-theme-mode="dark"] .apexcharts-datalabel-label{fill:#94a3b8!important}',
      '[data-theme-mode="dark"] .apexcharts-datalabel-value{fill:#e2e8f0!important}',
      '[data-theme-mode="dark"] .apexcharts-pie-label{fill:#fff!important}',
      '[data-theme-mode="dark"] .apexcharts-title-text{fill:#e2e8f0!important}',
      '[data-theme-mode="dark"] .apexcharts-subtitle-text{fill:#94a3b8!important}',
      '[data-theme-mode="dark"] .apexcharts-xaxis-label,.apexcharts-yaxis-label{fill:#94a3b8!important}',
      '[data-theme-mode="dark"] .apexcharts-xaxis text tspan,[data-theme-mode="dark"] .apexcharts-yaxis text tspan{fill:#94a3b8!important}',
      '[data-theme-mode="dark"] .apexcharts-tooltip{background:#1e2235!important;border-color:rgba(' + p + ',0.2)!important;color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .apexcharts-tooltip .apexcharts-tooltip-title{background:rgba(' + p + ',0.1)!important;border-color:rgba(' + p + ',0.15)!important;color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .apexcharts-tooltip .apexcharts-tooltip-text{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .apexcharts-gridline{stroke:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .apexcharts-grid-borders line{stroke:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .apexcharts-xaxis-tick{stroke:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .apexcharts-pie .apexcharts-datalabels text{fill:#fff!important}',
      '[data-theme-mode="dark"] .apexcharts-total-label{fill:#94a3b8!important}',
      '[data-theme-mode="dark"] .apexcharts-total-value{fill:#e2e8f0!important}',

      /* Dark mode - Chart selector & filter in dark */
      '[data-theme-mode="dark"] .chart-selector,[data-theme-mode="dark"] #chartViewSelector{background:#151827!important;color:#e2e8f0!important;border-color:rgba(' + p + ',0.25)!important}',
      '[data-theme-mode="dark"] .chart-selector option{background:#151827!important;color:#e2e8f0!important}',

      /* Dark mode - All pages: common black text elements */
      '[data-theme-mode="dark"] .page-header .breadcrumb{color:#94a3b8!important}',
      '[data-theme-mode="dark"] .custom-card .card-body{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .modal-content{background:#1e2235!important;color:#cbd5e1!important;border-color:rgba(' + p + ',0.15)!important}',
      '[data-theme-mode="dark"] .modal-header{border-color:rgba(255,255,255,0.08)!important}',
      '[data-theme-mode="dark"] .modal-footer{border-color:rgba(255,255,255,0.08)!important}',
      '[data-theme-mode="dark"] .modal-title{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .btn-close{filter:invert(1)!important}',
      '[data-theme-mode="dark"] .offcanvas{background:#1e2235!important;color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .offcanvas-header{border-color:rgba(255,255,255,0.08)!important}',

      /* Dark mode - List groups */
      '[data-theme-mode="dark"] .list-group-item{background:#1e2235!important;color:#cbd5e1!important;border-color:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .list-group-item:hover{background:rgba(' + p + ',0.08)!important}',

      /* Dark mode - Alerts & toast */
      '[data-theme-mode="dark"] .alert{border-color:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .toast{background:#1e2235!important;color:#cbd5e1!important;border-color:rgba(255,255,255,0.08)!important}',

      /* Dark mode - Tabs in dark */
      '[data-theme-mode="dark"] .nav-tabs{border-color:rgba(255,255,255,0.08)!important}',
      '[data-theme-mode="dark"] .nav-tabs .nav-link{color:#94a3b8!important}',
      '[data-theme-mode="dark"] .nav-tabs .nav-link.active{color:' + l + '!important;border-bottom-color:' + l + '!important;background:transparent!important}',
      '[data-theme-mode="dark"] .nav-tabs .nav-link:hover{color:#e2e8f0!important;border-color:transparent!important}',

      /* Dark mode - Accordion */
      '[data-theme-mode="dark"] .accordion-item{background:#1e2235!important;color:#cbd5e1!important;border-color:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .accordion-button{background:#1e2235!important;color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .accordion-button:not(.collapsed){background:rgba(' + p + ',0.08)!important;color:' + l + '!important}',

      /* Dark mode - Select2, Choices etc */
      '[data-theme-mode="dark"] .choices__inner,[data-theme-mode="dark"] .select2-container--default .select2-selection{background:#151827!important;border-color:rgba(' + p + ',0.2)!important;color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .choices__list--dropdown{background:#1e2235!important;border-color:rgba(' + p + ',0.15)!important}',
      '[data-theme-mode="dark"] .choices__list--dropdown .choices__item{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .choices__list--dropdown .choices__item--selectable.is-highlighted{background:rgba(' + p + ',0.15)!important}',

      /* Dark mode - Input groups, tags */
      '[data-theme-mode="dark"] .input-group-text{background:#151827!important;color:#94a3b8!important;border-color:rgba(' + p + ',0.2)!important}',
      '[data-theme-mode="dark"] .badge{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .badge.bg-light{background:rgba(255,255,255,0.08)!important;color:#cbd5e1!important}',

      /* Dark mode - CRM specific pages */
      '[data-theme-mode="dark"] .chat-body{background:#0f111a!important}',
      '[data-theme-mode="dark"] .chat-msg{background:#1e2235!important;color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .chat-user-list li{border-color:rgba(255,255,255,0.06)!important}',
      '[data-theme-mode="dark"] .chat-user-list li:hover{background:rgba(' + p + ',0.06)!important}',
      '[data-theme-mode="dark"] .chat-user-list .chat-name{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .chat-user-list .chat-msg-text{color:#94a3b8!important}',

      /* Dark mode - SweetAlert, Toastr */
      '[data-theme-mode="dark"] .swal2-popup{background:#1e2235!important;color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .swal2-title{color:#e2e8f0!important}',
      '[data-theme-mode="dark"] .swal2-html-container{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .toast-message{color:#e2e8f0!important}',

      /* Dark mode - Borders and dividers */
      '[data-theme-mode="dark"] .border{border-color:rgba(255,255,255,0.08)!important}',
      '[data-theme-mode="dark"] .border-bottom{border-color:rgba(255,255,255,0.08)!important}',
      '[data-theme-mode="dark"] .border-top{border-color:rgba(255,255,255,0.08)!important}',
      '[data-theme-mode="dark"] hr{border-color:rgba(255,255,255,0.08)!important}',

      /* Dark mode - Platform cards (top section) */
      '[data-theme-mode="dark"] .dash-top-card .card h6{color:rgba(255,255,255,0.6)!important}',
      '[data-theme-mode="dark"] .dash-top-card .card h3{color:#e2e8f0!important}',

      /* Dark mode - Data tables */
      '[data-theme-mode="dark"] .dataTables_wrapper{color:#cbd5e1!important}',
      '[data-theme-mode="dark"] .dataTables_wrapper .dataTables_length label,.dataTables_wrapper .dataTables_filter label,.dataTables_wrapper .dataTables_info{color:#94a3b8!important}',
      '[data-theme-mode="dark"] .dataTables_wrapper .dataTables_length select,.dataTables_wrapper .dataTables_filter input{background:#151827!important;border-color:rgba(' + p + ',0.2)!important;color:#e2e8f0!important}',

            /* === CARD HOVER === */
      '.dash-top-card .card:hover{box-shadow:0 6px 16px rgba(' + p + ',0.1)!important}'
    ].join('\n');
  }

  function applyTheme(themeId) {
    var t = THEMES[themeId];
    if (!t) return;

    // 1. CSS variables
    var root = document.documentElement;
    root.style.setProperty('--primary-rgb', t.primary);
    root.style.setProperty('--primary', t.hex);
    root.style.setProperty('--primary01', 'rgba(' + t.primary + ',0.1)');
    root.style.setProperty('--primary02', 'rgba(' + t.primary + ',0.2)');
    root.style.setProperty('--primary03', 'rgba(' + t.primary + ',0.3)');
    root.style.setProperty('--primary05', 'rgba(' + t.primary + ',0.5)');
    root.style.setProperty('--primary08', 'rgba(' + t.primary + ',0.8)');
    root.style.setProperty('--primary-bg-subtle', 'rgba(' + t.primary + ',0.1)');
    root.style.setProperty('--primary-border-subtle', 'rgba(' + t.primary + ',0.25)');
    root.style.setProperty('--primary-text-emphasis', t.dark);

    // 2. Dynamic CSS
    var dynStyle = document.getElementById('replai-theme-dynamic');
    if (!dynStyle) {
      dynStyle = document.createElement('style');
      dynStyle.id = 'replai-theme-dynamic';
      document.head.appendChild(dynStyle);
    }
    dynStyle.textContent = buildCSS(t);

    // 3. Update preset buttons
    document.querySelectorAll('.replai-preset-btn').forEach(function(btn){
      var isActive = btn.dataset.theme === themeId;
      btn.classList.toggle('active', isActive);
      btn.style.borderColor = isActive ? t.hex : '#e2e8f0';
      btn.style.background = isActive ? 'rgba(' + t.primary + ',0.08)' : '#fff';
    });

    // 4. Update save button
    var saveBtn = document.getElementById('replai-save-theme');
    if (saveBtn) {
      saveBtn.style.background = 'linear-gradient(135deg,' + t.dark + ',' + t.light + ')';
      saveBtn.style.boxShadow = '0 2px 8px rgba(' + t.primary + ',0.3)';
    }

    // 5. Sync Ynex localStorage
    localStorage.setItem('primaryRGB', t.primary);
  }

  function saveTheme(themeId) {
    localStorage.setItem('replai-theme', themeId);
    applyTheme(themeId);
    var toast = document.getElementById('replai-theme-toast');
    if (toast) {
      toast.style.display = 'flex';
      toast.textContent = '\u2705 Theme "' + THEMES[themeId].name + '" saved!';
      setTimeout(function(){ toast.style.display = 'none'; }, 2500);
    }
  }

  function loadSavedTheme() {
    var saved = localStorage.getItem('replai-theme');
    if (saved && THEMES[saved]) {
      applyTheme(saved);
    } else {
      applyTheme('sky-blue');
      localStorage.setItem('replai-theme', 'sky-blue');
    }
  }

  function buildPresetsUI() {
    var target = document.querySelector('#switcher-profile');
    if (!target) return;

    var saved = localStorage.getItem('replai-theme') || 'sky-blue';

    var html = '<div class="p-3 replai-presets-wrap" style="border-bottom:1px solid #e2e8f0;">';
    html += '<p class="switcher-style-head mb-2" style="font-weight:700;">\uD83C\uDFA8 Quick Themes:</p>';
    html += '<div class="d-flex flex-wrap gap-2 mb-3">';
    for (var id in THEMES) {
      var t = THEMES[id];
      var isActive = id === saved;
      html += '<button class="replai-preset-btn' + (isActive ? ' active' : '') + '" data-theme="' + id + '" ';
      html += 'style="border:2px solid ' + (isActive ? t.hex : '#e2e8f0') + ';';
      html += 'border-radius:12px;padding:8px 14px;cursor:pointer;';
      html += 'background:' + (isActive ? 'rgba(' + t.primary + ',0.08)' : '#fff') + ';';
      html += 'font-size:0.78rem;font-weight:600;color:#1e293b;';
      html += 'transition:all 0.2s ease;display:flex;align-items:center;gap:6px;">';
      html += '<span style="width:18px;height:18px;border-radius:50%;';
      html += 'background:' + t.hex + ';display:inline-block;box-shadow:0 2px 4px rgba(0,0,0,0.15);"></span>';
      html += t.name + '</button>';
    }
    html += '</div>';

    var st = THEMES[saved] || THEMES['sky-blue'];
    html += '<button id="replai-save-theme" style="width:100%;padding:10px;border:none;';
    html += 'border-radius:10px;background:linear-gradient(135deg,' + st.dark + ',' + st.light + ');';
    html += 'color:#fff;font-weight:700;font-size:0.85rem;cursor:pointer;';
    html += 'box-shadow:0 2px 8px rgba(' + st.primary + ',0.3);transition:all 0.2s ease;">';
    html += '\uD83D\uDCBE Save Theme</button>';

    html += '<div id="replai-theme-toast" style="display:none;position:fixed;bottom:20px;right:20px;';
    html += 'background:#1e293b;color:#fff;padding:12px 20px;border-radius:10px;font-size:0.82rem;';
    html += 'font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.2);z-index:99999;';
    html += 'align-items:center;gap:8px;"></div>';
    html += '</div>';

    // Remove old presets if exist
    var old = target.querySelector('.replai-presets-wrap');
    if (old) old.remove();

    target.insertAdjacentHTML('afterbegin', html);

    // Bind events
    document.querySelectorAll('.replai-preset-btn').forEach(function(btn){
      btn.addEventListener('click', function(){
        applyTheme(this.dataset.theme);
      });
    });

    document.getElementById('replai-save-theme').addEventListener('click', function(){
      var active = document.querySelector('.replai-preset-btn.active');
      if (active) saveTheme(active.dataset.theme);
    });
  }

  // Init on DOM ready
  function init() {
    loadSavedTheme();
    buildPresetsUI();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.replaiTheme = { apply: applyTheme, save: saveTheme, themes: THEMES };
})();


// Expose re-apply function for dark/light toggle compatibility
window._reapplyThemePreset = function() {
    var saved = localStorage.getItem('replai-theme') || 'sky-blue';
    if (window.replaiTheme && window.replaiTheme.apply) {
        window.replaiTheme.apply(saved);
    }
};
