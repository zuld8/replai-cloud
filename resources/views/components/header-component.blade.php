 <!-- app-header -->
 <header class="app-header" style="background: linear-gradient(120deg, #1E9BE0 0%, #5B3FB0 60%, #7B2FF7 100%) !important; border-bottom: none !important;">

 <style>
 /* ── Header background — gradient sesuai warna logo Replai.id ── */
 .app-header {
     background: linear-gradient(120deg, #1E9BE0 0%, #5B3FB0 60%, #7B2FF7 100%) !important;
     border-bottom: none !important;
 }
 /* Pastikan teks di header putih di atas bg gelap */
 .app-header .header-link-icon,
 .app-header .user-name,
 .app-header .header-link {
     color: #fff !important;
 }

 @media (max-width: 767px) {
     /* Meter: tampilkan cincin saja, sembunyikan teks label+nilai */
     .cmeter-text { display: none !important; }
     /* Kurangi margin antar cincin di HP */
     .ai-cwrap, .msg-cwrap { margin-left: 3px !important; }
     /* Ikon utilitas tidak berguna di HP → sembunyikan */
     .hide-on-mobile { display: none !important; }
 }
 /* ── Icon buttons kanan — circle style sesuai mockup ──────────── */
 /* Gap merata antar semua header-element kanan */
 .header-content-right {
     gap: 6px;
     align-items: center !important;
     padding-right: 8px;
 }
 /* Setiap header-element juga flex + center vertikal */
 .header-content-right .header-element {
     display: flex !important;
     align-items: center !important;
     height: 100%;
 }
 /* Circle button — lebih opak sesuai mockup */
 .header-content-right .header-element:not(.mainuserProfile) .header-link {
     width: 34px;
     height: 34px;
     border-radius: 50%;
     background: transparent;
     display: flex;
     align-items: center;
     justify-content: center;
     transition: background .18s;
     padding: 0;
     margin: 0 !important;
 }
 .header-content-right .header-element:not(.mainuserProfile) .header-link:hover {
     background: rgba(255,255,255,0.15);
 }
 /* Ukuran ikon semua seragam */
 .header-content-right .header-link-icon {
     font-size: 1.05rem !important;
     line-height: 1 !important;
 }
 /* Sembunyikan caret dropdown dari globe selector */
 .header-content-right .country-selector .header-link::after { display: none !important; }
 </style>



     <!-- Start::main-header-container -->
     <div class="main-header-container container-fluid">

         <!-- Start::header-content-left -->
         <div class="header-content-left">

             <!-- Start::header-element -->
             <div class="header-element">
                 <div class="horizontal-logo">
                     <a href="{{route('admin.index')}}" class="header-logo">
                         <img src="{{asset($internalSetting->logo)}}" alt="logo" class="desktop-logo">
                         <img src="{{asset($internalSetting->icon)}}" alt="logo" class="toggle-logo">
                         <img src="{{asset($internalSetting->white_logo)}}" alt="logo" class="desktop-dark" fetchpriority="high">
                         <img src="{{asset($internalSetting->icon)}}" alt="logo" class="toggle-dark">
                     </a>
                 </div>
             </div>
             <!-- End::header-element -->

             <!-- Start::header-element -->
             <div class="header-element">
                 <!-- Start::header-link -->
                 <a aria-label="anchor" href="javascript:void(0);" class="sidemenu-toggle header-link" data-bs-toggle="sidebar">
                     <span class="open-toggle me-2">
                         <i class="fe fe-align-left header-link-icon border-0"></i>
                     </span>
                 </a>
                 <!-- End::header-link -->
             </div>
             <!-- End::header-element -->




              @php
              /* Helper warna per level (Fase poles meter card) */
              $__lvl = function (\$pct) {
                  if (\$pct >= 90) return ['bar'=>'#E24B4A','text'=>'#A32D2D','tint'=>'#FCEBEB','badge'=>'Mepet'];
                  if (\$pct >= 70) return ['bar'=>'#EF9F27','text'=>'#B45309','tint'=>'#FAEEDA','badge'=>'Hati-hati'];
                  return ['bar'=>'#639922','text'=>'#3B6D11','tint'=>'#EAF3DE','badge'=>'Aman'];
              };
              @endphp
              <style>
              /* ── Meter card — popover TERANG (bukan hitam) ── */
              .meter-card{background:#fff;border:0.5px solid #E4EAF2;border-radius:12px;padding:14px;box-shadow:0 8px 24px rgba(30,42,74,.10),0 2px 6px rgba(30,42,74,.06);width:250px;}
              .meter-head{display:flex;align-items:center;gap:8px;margin-bottom:10px;}
              .meter-ic{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;}
              .meter-title{font-size:14px;font-weight:600;color:#1E2A4A;}
              .meter-badge{margin-left:auto;font-size:11px;border-radius:20px;padding:2px 8px;}
              .meter-desc{font-size:11.5px;color:#6B7A99;line-height:1.6;margin-bottom:6px;}
              .meter-note{font-size:11px;color:#8A96AC;line-height:1.5;margin-bottom:10px;display:flex;gap:5px;align-items:flex-start;}
              .meter-warn{font-size:11px;line-height:1.45;border-radius:8px;padding:8px 10px;margin-bottom:10px;display:flex;gap:6px;align-items:flex-start;}
              .meter-row{display:flex;justify-content:space-between;font-size:12.5px;padding:3px 0;color:#6B7A99;}
              .meter-row span:last-child{color:#1E2A4A;}
              .meter-row-last{border-bottom:0.5px solid #E4EAF2;padding-bottom:8px;margin-bottom:8px;}
              .meter-usage{display:flex;justify-content:space-between;font-size:11px;color:#8A96AC;margin-bottom:4px;}
              .meter-bar{height:6px;background:#F0F3F8;border-radius:20px;overflow:hidden;margin-bottom:11px;}
              .meter-bar>div{height:100%;border-radius:20px;}
              .meter-btn{display:flex;align-items:center;justify-content:center;gap:6px;font-size:12.5px;border-radius:8px;padding:9px;text-decoration:none;}
              .meter-btn-ghost{color:#6B7A99;border:0.5px solid #CBD5E1;}
              .meter-btn-danger{color:#fff;background:#E24B4A;font-weight:500;}
              </style>
              <!-- Start::header-element AI Credit Donut -->
              @php
                  try {
                      $__bid = my_business();
                      $__pkg = \Illuminate\Support\Facades\DB::connection('mysql')
                          ->table('package_transactions')
                          ->where('business_id', $__bid)->where('type','package')
                          ->where('status','success')->orderBy('created_at','desc')
                          ->first(['new_order_ai_response','using_credit_limit']);
                      $__mua = \Illuminate\Support\Facades\DB::connection('mysql')
                          ->table('package_transactions')
                          ->where('business_id', $__bid)->where('type','mua')
                          ->where('status','success')->orderBy('created_at','desc')
                          ->first(['new_order_ai_response','using_credit_limit']);
                      $__pkgL  = $__pkg ? (float)$__pkg->new_order_ai_response : 0;
                      $__pkgU  = $__pkg ? (float)$__pkg->using_credit_limit    : 0;
                      $__muaL  = $__mua ? (float)$__mua->new_order_ai_response : 0;
                      $__muaU  = $__mua ? (float)$__mua->using_credit_limit    : 0;
                      $__aiLimit = $__pkgL + $__muaL;
                      $__aiUsed  = $__pkgU + $__muaU;
                      $__aiPct   = $__aiLimit > 0 ? round($__aiUsed / $__aiLimit * 100, 1) : 0;
                      $__circ    = 56.55;
                      $__filled  = round($__aiPct / 100 * $__circ, 2);
                      $__empty   = round($__circ - $__filled, 2); $__hasAI = true;
                  } catch (\Exception $__e) {
                      $__hasAI = false; $__aiPct = 0; $__aiUsed = 0;
                      $__aiLimit = 0; $__filled = 0; $__empty = 56.55;
                  }
              @endphp
              @if($__hasAI)
              <style>
              .ai-cwrap{position:relative;display:inline-flex;align-items:center;}
              .ai-ctip{visibility:hidden;opacity:0;position:absolute;top:calc(100% + 8px);left:0;
                  width:260px;background:transparent;z-index:9999;
                  transition:opacity .15s,visibility .15s;pointer-events:none;}
              .ai-ctip::before{display:none;}
              .ai-cwrap:hover .ai-ctip{visibility:visible;opacity:1;}
              .ai-tip-row{display:flex;justify-content:space-between;padding:4px 0;
                  border-bottom:1px solid rgba(255,255,255,0.06);}
              .ai-tip-row:last-child{border-bottom:none;}
              </style>
              <div class="header-element ai-cwrap" style="margin-left:6px;cursor:default;">
                  <div style="display:inline-flex;align-items:center;gap:7px;padding:0 4px;">
                      {{-- Cincin peluk ikon AI Credit --}}
                      <div class="cmeter-ring" style="position:relative;width:34px;height:34px;flex-shrink:0;">
                          <svg width="34" height="34" viewBox="0 0 36 36" style="position:absolute;inset:0;">
                              <circle cx="18" cy="18" r="14" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="3"/>
                              <circle cx="18" cy="18" r="14" fill="none"
                                  stroke="{{ $__aiPct > 80 ? '#ef4444' : ($__aiPct > 50 ? '#facc15' : '#a78bfa') }}"
                                  stroke-width="3" stroke-linecap="round"
                                  stroke-dasharray="{{ round($__aiPct / 100 * 87.96, 2) }} {{ round(87.96 - $__aiPct / 100 * 87.96, 2) }}"
                                  transform="rotate(-90 18 18)"/>
                          </svg>
                          <span style="position:absolute;inset:8px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a78bfa);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(124,58,237,0.5);">
                              <i class="bx bxs-magic-wand" style="font-size:11px;color:#fff;line-height:1;"></i>
                          </span>
                      </div>
                      {{-- Teks (disembunyikan di mobile) --}}
                      <div class="cmeter-text" style="line-height:1.2;">
                          <div style="font-size:9px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.6px;display:flex;align-items:center;gap:3px;">
                              AI Credit &middot; {{ $__aiPct }}%
                              <span style="display:inline-flex;align-items:center;justify-content:center;width:12px;height:12px;background:rgba(255,255,255,0.2);border-radius:50%;">
                                  <i class="bx bx-question-mark" style="font-size:8px;color:white;line-height:1;"></i>
                              </span>
                          </div>
                          <div style="font-size:12.5px;font-weight:800;color:#fff;white-space:nowrap;">
                              {{ number_format($__aiUsed) }}<span style="opacity:.75;font-weight:500;">/{{ number_format($__aiLimit) }}</span>
                          </div>
                      </div>
                  </div>
                  {{-- Tooltip AI Credit --}}
                  <div class="ai-ctip">
                      @php \$__cai = \$__lvl(\$__aiPct); @endphp
                      <div class="meter-card">
                          <div class="meter-head">
                              <span class="meter-ic" style="background:#F1ECFE;color:#6D28D9;"><i class="bx bxs-magic-wand"></i></span>
                              <span class="meter-title">AI Credit</span>
                              <span class="meter-badge" style="color:{{ \$__cai['text'] }};background:{{ \$__cai['tint'] }};">{{ \$__cai['badge'] }}</span>
                          </div>
                          <div class="meter-desc">Dipakai untuk balas otomatis AI Chatbot dan AI Training.</div>
                          <div class="meter-row"><span>Terpakai</span><span>{{ number_format(\$__aiUsed, 0, ',', '.') }}</span></div>
                          <div class="meter-row"><span>Total limit</span><span>{{ number_format(\$__aiLimit, 0, ',', '.') }}</span></div>
                          <div class="meter-row meter-row-last"><span>Sisa</span><span style="color:{{ \$__cai['text'] }};font-weight:500;">{{ number_format(max(0,\$__aiLimit-\$__aiUsed), 0, ',', '.') }}</span></div>
                          <div class="meter-usage"><span>Penggunaan</span><span style="color:{{ \$__cai['text'] }};">{{ \$__aiPct }}%</span></div>
                          <div class="meter-bar"><div style="width:{{ min(\$__aiPct,100) }}%;background:{{ \$__cai['bar'] }};"></div></div>
                      </div>
                  </div>
              </div>
              @endif
              <!-- End::header-element AI Credit Donut -->
              <!-- Start::header-element Kredit Pesan Donut -->
              @php
                  try {
                      $__msgBid = my_business();
                      $__msgPkg = \Illuminate\Support\Facades\DB::connection('mysql')
                          ->table('package_transactions')
                          ->where('business_id', $__msgBid)->where('type','package')
                          ->where('status','success')->orderBy('created_at','desc')
                          ->first(['message_limit_option','message_limit','using_message_limit']);
                      $__msgTopup = \Illuminate\Support\Facades\DB::connection('mysql')
                          ->table('package_transactions')
                          ->where('business_id', $__msgBid)->where('type','message_topup')
                          ->where('status','success')->orderBy('created_at','desc')
                          ->first(['message_limit','using_message_limit']);
                      $__msgOption  = $__msgPkg ? ($__msgPkg->message_limit_option ?? 'no') : 'no';
                      $__msgLimit   = ($__msgPkg ? (int)$__msgPkg->message_limit : 0) + ($__msgTopup ? (int)$__msgTopup->message_limit : 0);
                      $__msgUsed    = ($__msgPkg ? (int)$__msgPkg->using_message_limit : 0) + ($__msgTopup ? (int)$__msgTopup->using_message_limit : 0);
                      $__msgPct     = ($__msgOption === 'yes' && $__msgLimit > 0) ? min(100, round($__msgUsed / $__msgLimit * 100, 1)) : 0;
                      $__msgColor   = $__msgPct > 90 ? '#ef4444' : ($__msgPct > 70 ? '#facc15' : '#38bdf8');
                      $__hasMsg     = ($__msgPkg !== null);
                  } catch (\Exception $__me) {
                      $__hasMsg = false; $__msgPct = 0; $__msgUsed = 0;
                      $__msgLimit = 0; $__msgOption = 'no'; $__msgColor = '#38bdf8';
                  }
              @endphp
              @if($__hasMsg)
              <style>
              .msg-cwrap{position:relative;display:inline-flex;align-items:center;}
              .msg-ctip{visibility:hidden;opacity:0;position:absolute;top:calc(100% + 10px);left:50%;
                  transform:translateX(-50%);background:transparent;
                  min-width:260px;z-index:9999;transition:opacity .15s,visibility .15s;pointer-events:none;}
              .msg-ctip::before{display:none;}
              .msg-cwrap:hover .msg-ctip{visibility:visible;opacity:1;}
              .msg-tip-row{display:flex;justify-content:space-between;padding:4px 0;
                  border-bottom:1px solid rgba(255,255,255,0.06);}
              .msg-tip-row:last-child{border-bottom:none;}
              </style>
              <div class="header-element msg-cwrap" style="margin-left:6px;cursor:default;">
                  <div style="display:inline-flex;align-items:center;gap:7px;padding:0 4px;">
                      {{-- Cincin peluk ikon Kredit Pesan --}}
                      <div class="cmeter-ring" style="position:relative;width:34px;height:34px;flex-shrink:0;">
                          <svg width="34" height="34" viewBox="0 0 36 36" style="position:absolute;inset:0;">
                              <circle cx="18" cy="18" r="14" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="3"/>
                              @if($__msgOption === 'yes' && $__msgLimit > 0)
                              <circle cx="18" cy="18" r="14" fill="none"
                                  stroke="{{ $__msgColor }}"
                                  stroke-width="3" stroke-linecap="round"
                                  stroke-dasharray="{{ round($__msgPct / 100 * 87.96, 2) }} {{ round(87.96 - $__msgPct / 100 * 87.96, 2) }}"
                                  transform="rotate(-90 18 18)"/>
                              @else
                              {{-- Unlimited: cincin penuh biru --}}
                              <circle cx="18" cy="18" r="14" fill="none" stroke="#38bdf8" stroke-width="3" stroke-linecap="round"
                                  stroke-dasharray="87.96 0" transform="rotate(-90 18 18)"/>
                              @endif
                          </svg>
                          <span style="position:absolute;inset:8px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#38bdf8);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(14,165,233,0.5);">
                              <i class="bx bxs-paper-plane" style="font-size:11px;color:#fff;line-height:1;"></i>
                          </span>
                      </div>
                      {{-- Teks (disembunyikan di mobile) --}}
                      <div class="cmeter-text" style="line-height:1.2;">
                          <div style="font-size:9px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.6px;display:flex;align-items:center;gap:3px;">
                              Kredit Pesan &infin;
                              <span style="display:inline-flex;align-items:center;justify-content:center;width:12px;height:12px;background:rgba(255,255,255,0.2);border-radius:50%;">
                                  <i class="bx bx-question-mark" style="font-size:8px;color:white;line-height:1;"></i>
                              </span>
                          </div>
                          <div style="font-size:12.5px;font-weight:800;color:#fff;white-space:nowrap;">
                              @if($__msgOption === 'yes' && $__msgLimit > 0)
                                  {{ number_format($__msgUsed) }}<span style="opacity:.75;font-weight:500;">/{{ number_format($__msgLimit) }}</span>
                              @else
                                  <span style="color:#38bdf8;">Tak Terbatas</span>
                              @endif
                          </div>
                      </div>
                  </div>
                  {{-- Tooltip Kredit Pesan --}}
                  <div class="msg-ctip">
                      <div class="meter-card">
                          <div class="meter-head">
                              <span class="meter-ic" style="background:#EAF3FC;color:#185FA5;"><i class="bx bxs-paper-plane"></i></span>
                              <span class="meter-title">Kredit Pesan</span>
                              @if(\$__msgOption === 'yes' && \$__msgLimit > 0)
                                  @php \$__cmsg = \$__lvl(\$__msgPct); @endphp
                                  <span class="meter-badge" style="color:{{ \$__cmsg['text'] }};background:{{ \$__cmsg['tint'] }};">{{ \$__cmsg['badge'] }}</span>
                              @else
                                  <span class="meter-badge" style="color:#185FA5;background:#EAF3FC;">&#8734; Bebas</span>
                              @endif
                          </div>
                          <div class="meter-desc">Dipakai untuk broadcast WhatsApp massal dan follow-up otomatis.</div>
                          <div class="meter-row"><span>Terpakai</span><span>{{ number_format(\$__msgUsed, 0, ',', '.') }}</span></div>
                          @if(\$__msgOption === 'yes' && \$__msgLimit > 0)
                              <div class="meter-row meter-row-last"><span>Sisa</span>
                                  <span style="color:{{ \$__cmsg['text'] }};font-weight:500;">
                                      {{ number_format(max(0,\$__msgLimit-\$__msgUsed), 0, ',', '.') }} dari {{ number_format(\$__msgLimit, 0, ',', '.') }}
                                  </span>
                              </div>
                              <div class="meter-usage"><span>Penggunaan</span><span style="color:{{ \$__cmsg['text'] }};">{{ \$__msgPct }}%</span></div>
                              <div class="meter-bar"><div style="width:{{ min(\$__msgPct,100) }}%;background:{{ \$__cmsg['bar'] }};"></div></div>
                          @else
                              <div class="meter-row meter-row-last"><span>Status</span><span style="color:#185FA5;font-weight:500;">Tidak terbatas &#8734;</span></div>
                              <div style="background:#EAF3FC;border-radius:8px;padding:9px 12px;font-size:12px;color:#185FA5;text-align:center;margin-top:2px;">
                                  Kuota pesan kamu tak terbatas — kirim sepuasnya.
                              </div>
                          @endif
                      </div>
                  </div>
              </div>
              @endif
              <!-- End::header-element Kredit Pesan Donut -->


              <!-- Start::header-element Storage Donut -->
              @php
                  try {
                      $__stBid = my_business();

                      // Used storage — pakai Storage::disk('local') persis seperti StorageBillingController
                      // Cache 5 menit, key sama = shared dengan billing controller
                      $__stCacheKey = "storage_usage_business_{$__stBid}";
                      // FIX perf: header HANYA baca cache — scan folder dilakukan background oleh dashboard:warm
                      // Kalau cache belum ada (bisnis baru), badge tampil 0 sampai warm pertama jalan — aman.
                      $__stUsedMB = \Illuminate\Support\Facades\Cache::get($__stCacheKey, 0);

                      // Total storage limit dari package aktif
                      $__stPkg = \Illuminate\Support\Facades\DB::connection('mysql')
                          ->table('package_transactions')
                          ->where('business_id', $__stBid)
                          ->where('type', 'package')
                          ->where('status', 'success')
                          ->orderBy('created_at', 'desc')
                          ->first(['storage']);
                      $__stTotalMB = $__stPkg ? (float)$__stPkg->storage : 0;

                      // Storage addons
                      $__stAddon = \Illuminate\Support\Facades\DB::connection('mysql')
                          ->table('package_transactions')
                          ->where('business_id', $__stBid)
                          ->where('type', 'storage')
                          ->where('status', 'success')
                          ->orderBy('created_at', 'desc')
                          ->first(['storage']);
                      $__stTotalMB += $__stAddon ? (float)$__stAddon->storage : 0;

                      $__stPct    = $__stTotalMB > 0 ? round($__stUsedMB / $__stTotalMB * 100, 1) : 0;
                      $__stCirc   = 87.96;
                      $__stFilled = round($__stPct / 100 * $__stCirc, 2);
                      $__stEmpty  = round($__stCirc - $__stFilled, 2);
                      $__hasStorage = true;

                  } catch (\Exception $__stE) {
                      $__hasStorage = false; $__stPct = 0;
                      $__stUsedMB = 0; $__stTotalMB = 0;
                      $__stFilled = 0; $__stEmpty = 87.96;
                  }
              @endphp
              @if($__hasStorage)
              <div class="header-element ai-cwrap" style="margin-left:6px;cursor:default;">
                  <div style="display:inline-flex;align-items:center;gap:7px;padding:0 4px;">
                      {{-- Cincin peluk ikon Storage --}}
                      <div class="cmeter-ring" style="position:relative;width:34px;height:34px;flex-shrink:0;">
                          <svg width="34" height="34" viewBox="0 0 36 36" style="position:absolute;inset:0;">
                              <circle cx="18" cy="18" r="14" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="3"/>
                              <circle cx="18" cy="18" r="14" fill="none"
                                  stroke="{{ $__stPct > 90 ? '#ef4444' : ($__stPct > 70 ? '#facc15' : '#f59e0b') }}"
                                  stroke-width="3" stroke-linecap="round"
                                  stroke-dasharray="{{ $__stFilled }} {{ $__stEmpty }}"
                                  transform="rotate(-90 18 18)"/>
                          </svg>
                          <span style="position:absolute;inset:8px;border-radius:50%;background:{{ $__stPct > 90 ? 'linear-gradient(135deg,#dc2626,#ef4444)' : 'linear-gradient(135deg,#d97706,#f59e0b)' }};display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px {{ $__stPct > 90 ? 'rgba(220,38,38,0.5)' : 'rgba(217,119,6,0.5)' }};">
                              <i class="bx bxs-data" style="font-size:11px;color:#fff;line-height:1;"></i>
                          </span>
                      </div>
                      {{-- Teks (disembunyikan di mobile) --}}
                      <div class="cmeter-text" style="line-height:1.2;">
                          <div style="font-size:9px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.6px;display:flex;align-items:center;gap:3px;">
                              Storage &middot; {{ $__stPct }}%
                              @if($__stPct > 90)<i class="bx bxs-error" style="font-size:10px;color:#fecaca;"></i>@endif
                              <span style="display:inline-flex;align-items:center;justify-content:center;width:12px;height:12px;background:rgba(255,255,255,0.2);border-radius:50%;">
                                  <i class="bx bx-question-mark" style="font-size:8px;color:white;line-height:1;"></i>
                              </span>
                          </div>
                          <div style="font-size:12.5px;font-weight:800;color:#fff;white-space:nowrap;">
                              {{ number_format($__stUsedMB, 1) }}<span style="opacity:.75;font-weight:500;">/{{ number_format($__stTotalMB) }} MB</span>
                          </div>
                      </div>
                  </div>
                  <!-- Storage Popover — meter-card terang -->
                  @php \$__c = \$__lvl(\$__stPct); @endphp
                  <div class="ai-ctip" style="left:0;">
                      <div class="meter-card" style="{{ \$__stPct >= 80 ? 'border:1px solid #E24B4A;' : '' }}">
                          <div class="meter-head">
                              <span class="meter-ic" style="background:#FAEEDA;color:#854F0B;"><i class="bx bx-hdd"></i></span>
                              <span class="meter-title">Storage</span>
                              <span class="meter-badge" style="color:{{ \$__c['text'] }};background:{{ \$__c['tint'] }};">{{ \$__c['badge'] }}</span>
                          </div>
                          <div class="meter-desc">Foto, video, dan dokumen dari chat pelanggan dan AI Training.</div>
                          @if (\$__stPct >= 80)
                              <div class="meter-warn" style="background:#FCEBEB;color:#791F1F;">
                                  <i class="bx bx-error-circle" style="color:#A32D2D;flex-shrink:0;margin-top:1px;"></i>
                                  Hampir penuh! Jika penuh, tidak bisa terima file/foto/video.
                              </div>
                          @else
                              <div class="meter-note">
                                  <i class="bx bx-info-circle" style="flex-shrink:0;margin-top:1px;"></i>
                                  Jika penuh, tidak bisa terima file/foto/video.
                              </div>
                          @endif
                          <div class="meter-row"><span>Terpakai</span><span>{{ number_format(\$__stUsedMB, 1, ',', '.') }} MB</span></div>
                          <div class="meter-row meter-row-last"><span>Sisa</span>
                              <span style="color:{{ \$__c['text'] }};font-weight:500;">
                                  {{ number_format(max(\$__stTotalMB - \$__stUsedMB, 0), 1, ',', '.') }} MB dari {{ number_format(\$__stTotalMB, 0, ',', '.') }} MB
                              </span>
                          </div>
                          <div class="meter-usage"><span>Penggunaan</span><span style="color:{{ \$__c['text'] }};">{{ \$__stPct }}%</span></div>
                          <div class="meter-bar"><div style="width:{{ min(\$__stPct,100) }}%;background:{{ \$__c['bar'] }};"></div></div>
                          @if (\$__stPct >= 80)
                              <a href="{{ url('app/master/media-manager') }}" class="meter-btn meter-btn-danger"><i class="bx bx-trash"></i> Hapus media sekarang</a>
                          @else
                              <a href="{{ url('app/master/media-manager') }}" class="meter-btn meter-btn-ghost"><i class="bx bx-images"></i> Kelola media</a>
                          @endif
                      </div>
                  </div>
              </div>
              @endif
              <!-- End::header-element Storage Donut -->


         </div>
         <!-- End::header-content-left -->

         <!-- Start::header-content-right -->
         <div class="header-content-right">

             <div class="header-element country-selector hide-on-mobile">
                 <a aria-label="anchor" href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                     <i class="bx bx-world header-link-icon" style="font-size:1.25rem"></i>
                 </a>
                 <ul class="main-header-dropdown dropdown-menu border-0"> 
                     <li> 
                        <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','id') : route('setlang','id') }}"> 
                            <span class="avatar avatar-xs lh-1 me-2"> 
                                <img src="{{asset('assets/img/flags/indonesia.png')}}" alt="img">
                            </span> {{__('sidebar.indonesia')}} @if(current_lang() == 'id') <i class="bx bx-check-circle ms-2 text-success"></i> @endif 
                        </a> 
                    </li>
                     <li> 
                        <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','en') : route('setlang','en') }}"> 
                            <span class="avatar avatar-xs lh-1 me-2"> 
                                <img src="{{asset('assets/img/flags/english.png')}}" alt="img">
                            </span> {{__('sidebar.english')}} @if(current_lang() == 'en') <i class="bx bx-check-circle ms-2 text-success"></i> @endif 
                        </a> 
                    </li>
                     <li> 
                        <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','hi') : route('setlang','hi') }}"> 
                            <span class="avatar avatar-xs lh-1 me-2"> 
                                <img src="{{asset('assets/img/flags/india.png')}}" alt="img">
                            </span> {{__('sidebar.india')}} @if(current_lang() == 'hi') <i class="bx bx-check-circle ms-2 text-success"></i> @endif 
                        </a> 
                    </li>
                     <li> 
                        <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','pt') : route('setlang','pt') }}"> 
                            <span class="avatar avatar-xs lh-1 me-2"> 
                                <img src="{{asset('assets/img/flags/portugal.png')}}" alt="img">
                            </span> {{__('sidebar.portugal')}} @if(current_lang() == 'pt') <i class="bx bx-check-circle ms-2 text-success"></i> @endif 
                        </a> 
                    </li>
                     <li> 
                        <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','es') : route('setlang','es') }}"> 
                            <span class="avatar avatar-xs lh-1 me-2"> 
                                <img src="{{asset('assets/img/flags/spanyol.png')}}" alt="img">
                            </span> {{__('sidebar.spanish')}} @if(current_lang() == 'es') <i class="bx bx-check-circle ms-2 text-success"></i> @endif 
                        </a> 
                    </li>
                     <li> 
                        <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','de') : route('setlang','de') }}"> 
                            <span class="avatar avatar-xs lh-1 me-2"> 
                                <img src="{{asset('assets/img/flags/de.svg')}}" alt="img">
                            </span> {{__('sidebar.german')}} @if(current_lang() == 'de') <i class="bx bx-check-circle ms-2 text-success"></i> @endif 
                        </a> 
                    </li>
                     <li> 
                        <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','ar') : route('setlang','ar') }}"> 
                            <span class="avatar avatar-xs lh-1 me-2"> 
                                <img src="{{asset('assets/img/flags/arab.png')}}" alt="img">
                            </span> {{__('sidebar.arab')}} @if(current_lang() == 'ar') <i class="bx bx-check-circle ms-2 text-success"></i> @endif 
                        </a> 
                    </li>
                     <li> 
                        <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','ja') : route('setlang','ja') }}"> 
                            <span class="avatar avatar-xs lh-1 me-2"> 
                                <img src="{{asset('assets/img/flags/jp.svg')}}" alt="img">
                            </span> {{__('sidebar.japan')}} @if(current_lang() == 'ja') <i class="bx bx-check-circle ms-2 text-success"></i> @endif 
                        </a> 
                    </li>
                     <li> 
                        <a class="dropdown-item d-flex align-items-center" href="{{ auth()->user()->role == 'admin' ?  route('setlang','nl') : route('setlang','nl') }}"> 
                            <span class="avatar avatar-xs lh-1 me-2"> 
                                <img src="{{asset('assets/img/flags/nl.svg')}}" alt="img">
                            </span> {{__('sidebar.dutch')}} @if(current_lang() == 'nl') <i class="bx bx-check-circle ms-2 text-success"></i> @endif 
                        </a> 
                    </li>
                 </ul>
             </div>


             <!-- Start::header-element -->
             <div class="header-element header-theme-mode hide-on-mobile">
                 <a aria-label="anchor" href="javascript:void(0);" class="header-link layout-setting">
                     <i class="bx bxs-sun header-link-icon dark-layout" style="font-size:1.25rem"></i>
                     <i class="bx bxs-moon header-link-icon light-layout" style="font-size:1.25rem"></i>
                 </a>
             </div>
             <!-- End::header-element -->


             <!-- Start::header-element -->
             <div class="header-element header-fullscreen hide-on-mobile">
                 <!-- Start::header-link -->
                 <a aria-label="anchor" onclick="openFullscreen();" href="javascript:void(0);" class="header-link">
                     <i class="bx bx-expand-alt header-link-icon full-screen-open"></i>
                     <i class="bx bx-collapse-alt header-link-icon full-screen-close d-none"></i>
                 </a>
                 <!-- End::header-link -->
             </div>
             <!-- End::header-element -->

             <!-- Start::header-element -->
             <div class="header-element mainuserProfile">
                 <!-- Start::header-link|dropdown-toggle -->
                 <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                     <div class="d-flex align-items-center" style="gap:7px;">
                         {{-- Avatar: sama dengan circle icons lain (rgba putih di atas bg gelap) --}}
                         <div style="width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                             <i class="bx bx-user" style="color:#fff;font-size:1.05rem;line-height:1;"></i>
                         </div>
                         {{-- Nama user — hanya tampil di xl+ --}}
                         <span class="d-none d-xl-block" style="font-size:0.82rem;font-weight:600;color:#fff;letter-spacing:.01em;white-space:nowrap;">{{auth()->user()->name}}</span>
                     </div>
                 </a>
                 <!-- End::header-link|dropdown-toggle -->
                 <div class="main-header-dropdown dropdown-menu pt-0 border-0 header-profile-dropdown dropdown-menu-end dropdown-menu-arrow" aria-labelledby="mainHeaderProfile">
                     <div class="p-3 menu-header-content text-fixed-white rounded-top text-center">
                         <div class="">
                             <div class="avatar avatar-xl rounded-circle"><img alt="" class="rounded-circle" src="{{asset(auth()->user()->image_data)}}"></div>
                             <p class="text-fixed-white fs-18 fw-semibold mb-0">{{auth()->user()->name}}</p>
                             <span class="fs-13 text-fixed-white">{{auth()->user()->phone}}</span>
                         </div>
                     </div>
                     <div>
                         <hr class="dropdown-divider">
                     </div>

                     <div>

                         @if(auth()->user()->role == 'user')
                         <a class="dropdown-item" href="{{ route('profile') }}"><i class="bx bx-user-circle me-1"></i> {{__('sidebar.profile')}}</a>
                         <a class="dropdown-item" href="{{ route('starter.business.index') }}"><i class="bx bx-toggle-left me-1"></i> {{__('sidebar.starter_app')}}</a>
                         @endif

                         @if(auth()->user()->role == 'admin')
                         <a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bx bx-user-circle me-1"></i> {{__('sidebar.profile')}}</a>
                         <a class="dropdown-item" href="{{ route('admin.index') }}"><i class="bx bx-toggle-right me-1"></i> {{__('sidebar.admin_panel')}}</a>
                         @endif

                         <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                             <i class="bx bx-arrow-to-right me-1"></i> {{__('sidebar.logout')}}
                         </a>
                         <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                             @csrf
                         </form>
                     </div>
                 </div>
             </div>
             <!-- End::header-element -->


             <!-- Start::header-element -->
             <div class="header-element hide-on-mobile">
                 <!-- Start::header-link|switcher-icon -->
                 <a aria-label="anchor" href="javascript:void(0);" class="header-link switcher-icon" data-bs-toggle="offcanvas" data-bs-target="#switcher-canvas">
                     <i class="bx bx-cog header-link-icon"></i>
                 </a>
                 <!-- End::header-link|switcher-icon -->
             </div>
             <!-- End::header-element -->

         </div>
         <!-- End::header-content-right -->

     </div>
     <!-- End::main-header-container -->

 </header>
 <!-- /app-header -->