 <!-- app-header -->
 <header class="app-header" style="background: linear-gradient(120deg, #1a2755 0%, #2d1d6b 55%, #5B3FB0 100%) !important; border-bottom: none !important;">

 <style>
 /* ── Header background — gradient gelap navy ke ungu sesuai mockup ── */
 .app-header {
     background: linear-gradient(120deg, #1a2755 0%, #2d1d6b 55%, #5B3FB0 100%) !important;
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
     gap: 4px;
 }
 /* Circle button — rgba putih (bg header gelap) */
 .header-content-right .header-element:not(.mainuserProfile) .header-link {
     width: 34px;
     height: 34px;
     border-radius: 50%;
     background: rgba(255,255,255,0.15);
     display: flex;
     align-items: center;
     justify-content: center;
     transition: background .18s;
     padding: 0;
     margin: 0 !important;
 }
 .header-content-right .header-element:not(.mainuserProfile) .header-link:hover {
     background: rgba(255,255,255,0.27);
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
              .ai-ctip{
                  visibility:hidden;opacity:0;
                  position:absolute;top:calc(100% + 8px);left:0;
                  width:240px;background:#1a1a2e;color:#e2e8f0;
                  border-radius:12px;padding:14px;font-size:12px;line-height:1.65;
                  box-shadow:0 12px 32px rgba(0,0,0,0.35);z-index:9999;
                  transition:opacity 0s,visibility 0s;pointer-events:none;
                  border:1px solid rgba(255,255,255,0.08);}
              .ai-ctip::before{content:'';position:absolute;top:-7px;left:16px;
                  border:7px solid transparent;border-bottom-color:#1a1a2e;border-top:none;}
              .ai-cwrap:hover .ai-ctip{visibility:visible;opacity:1;}
              .ai-tip-row{display:flex;justify-content:space-between;padding:4px 0;
                  border-bottom:1px solid rgba(255,255,255,0.06);}
              .ai-tip-row:last-child{border-bottom:none;}
              </style>
              <div class="header-element ai-cwrap" style="margin-left:6px;cursor:default;">
                  <div style="display:inline-flex;align-items:center;gap:7px;padding:0 4px;">
                      {{-- Cincin peluk ikon AI Credit --}}
                      <div class="cmeter-ring" style="position:relative;width:38px;height:38px;flex-shrink:0;">
                          <svg width="38" height="38" viewBox="0 0 36 36" style="position:absolute;inset:0;">
                              <circle cx="18" cy="18" r="14" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="3"/>
                              <circle cx="18" cy="18" r="14" fill="none"
                                  stroke="{{ $__aiPct > 80 ? '#ef4444' : ($__aiPct > 50 ? '#facc15' : '#a78bfa') }}"
                                  stroke-width="3" stroke-linecap="round"
                                  stroke-dasharray="{{ round($__aiPct / 100 * 87.96, 2) }} {{ round(87.96 - $__aiPct / 100 * 87.96, 2) }}"
                                  transform="rotate(-90 18 18)"/>
                          </svg>
                          <span style="position:absolute;inset:5px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a78bfa);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(124,58,237,0.5);">
                              <i class="bx bx-bot" style="font-size:14px;color:#fff;line-height:1;"></i>
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
                  {{-- Tooltip --}}
                  <div class="ai-ctip">
                      <div style="font-weight:700;font-size:12.5px;color:#a78bfa;margin-bottom:8px;">🤖 AI Credit</div>
                      <div style="color:#94a3b8;font-size:11px;margin-bottom:8px;">Digunakan untuk:</div>
                      <div style="display:flex;flex-direction:column;gap:4px;margin-bottom:10px;">
                          <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;">
                              <span style="color:#34d399;">●</span>
                              Balas otomatis <b style="color:#f0fdf4;">AI Chatbot</b>
                          </div>
                          <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;">
                              <span style="color:#34d399;">●</span>
                              <b style="color:#f0fdf4;">AI Training</b> & fine-tuning
                          </div>
                      </div>
                      <div class="ai-tip-row">
                          <span style="color:#64748b;">Terpakai</span>
                          <span style="font-weight:600;">{{ number_format($__aiUsed) }}</span>
                      </div>
                      <div class="ai-tip-row">
                          <span style="color:#64748b;">Total Limit</span>
                          <span style="font-weight:600;">{{ number_format($__aiLimit) }}</span>
                      </div>
                      <div class="ai-tip-row">
                          <span style="color:#64748b;">Sisa</span>
                          <span style="font-weight:700;color:#34d399;">{{ number_format(max(0,$__aiLimit-$__aiUsed)) }}</span>
                      </div>
                      <div style="margin-top:8px;background:rgba(52,211,153,0.08);border-radius:6px;padding:6px 8px;">
                          <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                              <span style="font-size:10px;color:#64748b;">Penggunaan</span>
                              <span style="font-size:10px;font-weight:700;color:{{ $__aiPct > 80 ? '#f87171' : '#34d399' }};">{{ $__aiPct }}%</span>
                          </div>
                          <div style="background:rgba(255,255,255,0.08);border-radius:4px;height:4px;overflow:hidden;">
                              <div style="background:{{ $__aiPct > 80 ? '#f87171' : '#34d399' }};width:{{ $__aiPct }}%;height:100%;border-radius:4px;"></div>
                          </div>
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
              .msg-ctip{
                  visibility:hidden;opacity:0;position:absolute;top:calc(100% + 10px);left:50%;
                  transform:translateX(-50%);background:#1a1a2e;border:1px solid rgba(56,189,248,0.2);
                  border-radius:10px;padding:14px 16px;min-width:200px;z-index:9999;
                  box-shadow:0 8px 30px rgba(0,0,0,0.5);transition:opacity 0.2s ease,visibility 0.2s ease;
                  color:#e2e8f0;font-size:12px;}
              .msg-ctip::before{content:'';position:absolute;top:-7px;left:50%;transform:translateX(-50%);
                  border:7px solid transparent;border-bottom-color:#1a1a2e;border-top:none;}
              .msg-cwrap:hover .msg-ctip{visibility:visible;opacity:1;}
              .msg-tip-row{display:flex;justify-content:space-between;padding:4px 0;
                  border-bottom:1px solid rgba(255,255,255,0.06);}
              .msg-tip-row:last-child{border-bottom:none;}
              </style>
              <div class="header-element msg-cwrap" style="margin-left:6px;cursor:default;">
                  <div style="display:inline-flex;align-items:center;gap:7px;padding:0 4px;">
                      {{-- Cincin peluk ikon Kredit Pesan --}}
                      <div class="cmeter-ring" style="position:relative;width:38px;height:38px;flex-shrink:0;">
                          <svg width="38" height="38" viewBox="0 0 36 36" style="position:absolute;inset:0;">
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
                          <span style="position:absolute;inset:5px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#38bdf8);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(14,165,233,0.5);">
                              <i class="bx bxs-paper-plane" style="font-size:13px;color:#fff;line-height:1;"></i>
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
                  {{-- Tooltip --}}
                  <div class="msg-ctip">
                      <div style="font-weight:700;font-size:12.5px;color:#38bdf8;margin-bottom:8px;">💬 Kredit Pesan</div>
                      <div style="color:#94a3b8;font-size:11px;margin-bottom:8px;">Digunakan untuk:</div>
                      <div style="display:flex;flex-direction:column;gap:4px;margin-bottom:10px;">
                          <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;">
                              <span style="color:#38bdf8;">●</span>
                              Broadcast <b style="color:#f0fdf4;">WhatsApp</b> massal
                          </div>
                          <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;">
                              <span style="color:#38bdf8;">●</span>
                              Follow-up <b style="color:#f0fdf4;">pesan otomatis</b>
                          </div>
                      </div>
                      <div class="msg-tip-row">
                          <span style="color:#64748b;">Terpakai</span>
                          <span style="font-weight:600;">{{ number_format($__msgUsed) }}</span>
                      </div>
                      @if($__msgOption === 'yes' && $__msgLimit > 0)
                      <div class="msg-tip-row">
                          <span style="color:#64748b;">Total Limit</span>
                          <span style="font-weight:600;">{{ number_format($__msgLimit) }}</span>
                      </div>
                      <div class="msg-tip-row">
                          <span style="color:#64748b;">Sisa</span>
                          <span style="font-weight:700;color:#38bdf8;">{{ number_format(max(0,$__msgLimit-$__msgUsed)) }}</span>
                      </div>
                      <div style="margin-top:8px;background:rgba(56,189,248,0.08);border-radius:6px;padding:6px 8px;">
                          <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                              <span style="font-size:10px;color:#64748b;">Penggunaan</span>
                              <span style="font-size:10px;font-weight:700;color:{{ $__msgColor }};">{{ $__msgPct }}%</span>
                          </div>
                          <div style="background:rgba(255,255,255,0.08);border-radius:4px;height:4px;overflow:hidden;">
                              <div style="background:{{ $__msgColor }};width:{{ $__msgPct }}%;height:100%;border-radius:4px;"></div>
                          </div>
                      </div>
                      @else
                      <div class="msg-tip-row">
                          <span style="color:#64748b;">Status</span>
                          <span style="font-weight:700;color:#38bdf8;">Tidak Terbatas ∞</span>
                      </div>
                      @endif
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
                      if (\Illuminate\Support\Facades\Cache::has($__stCacheKey)) {
                          // Gunakan cache yang sudah ada (set oleh billing controller)
                          $__stUsedMB = \Illuminate\Support\Facades\Cache::get($__stCacheKey, 0);
                      } else {
                          // Hitung sendiri dengan Storage::disk persis seperti controller
                          $__stUsedMB = \Illuminate\Support\Facades\Cache::remember(
                              $__stCacheKey, 60,
                              function() use ($__stBid) {
                                  $totalSize = 0;
                                  $path = "uploads/folders/{$__stBid}";
                                  if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
                                      $files = \Illuminate\Support\Facades\Storage::disk('local')->allFiles($path);
                                      foreach ($files as $file) {
                                          $totalSize += \Illuminate\Support\Facades\Storage::disk('local')->size($file);
                                      }
                                  }
                                  return round($totalSize / 1024 / 1024, 2);
                              }
                          );
                      }

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
                      <div class="cmeter-ring" style="position:relative;width:38px;height:38px;flex-shrink:0;">
                          <svg width="38" height="38" viewBox="0 0 36 36" style="position:absolute;inset:0;">
                              <circle cx="18" cy="18" r="14" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="3"/>
                              <circle cx="18" cy="18" r="14" fill="none"
                                  stroke="{{ $__stPct > 90 ? '#ef4444' : ($__stPct > 70 ? '#facc15' : '#f59e0b') }}"
                                  stroke-width="3" stroke-linecap="round"
                                  stroke-dasharray="{{ $__stFilled }} {{ $__stEmpty }}"
                                  transform="rotate(-90 18 18)"/>
                          </svg>
                          <span style="position:absolute;inset:5px;border-radius:50%;background:{{ $__stPct > 90 ? 'linear-gradient(135deg,#dc2626,#ef4444)' : 'linear-gradient(135deg,#d97706,#f59e0b)' }};display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px {{ $__stPct > 90 ? 'rgba(220,38,38,0.5)' : 'rgba(217,119,6,0.5)' }};">
                              <i class="bx bxs-hdd" style="font-size:13px;color:#fff;line-height:1;"></i>
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
                  <!-- Storage Tooltip -->
                  <div class="ai-ctip" style="left:0;">
                      <div style="font-weight:700;font-size:12.5px;color:#38bdf8;margin-bottom:8px;">💾 Storage</div>
                      <div style="color:#94a3b8;font-size:11px;margin-bottom:6px;">Penyimpanan file & media bisnis:</div>
                      <div style="display:flex;flex-direction:column;gap:4px;margin-bottom:10px;">
                          <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;">
                              <span style="color:#38bdf8;">●</span>
                              <span>Foto & video dari <b style="color:#f0fdf4;">chat pelanggan</b></span>
                          </div>
                          <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;">
                              <span style="color:#38bdf8;">●</span>
                              <span>Dokumen <b style="color:#f0fdf4;">AI Training</b></span>
                          </div>
                          <div style="display:flex;align-items:center;gap:6px;font-size:11.5px;">
                              <span style="color:#f87171;">⚠</span>
                              <span style="color:#fca5a5;">Jika penuh, tidak bisa terima <b>file/foto/video</b></span>
                          </div>
                      </div>
                      <div class="ai-tip-row">
                          <span style="color:#64748b;">Terpakai</span>
                          <span style="font-weight:600;">{{ number_format($__stUsedMB, 2) }} MB</span>
                      </div>
                      <div class="ai-tip-row">
                          <span style="color:#64748b;">Total Limit</span>
                          <span style="font-weight:600;">{{ number_format($__stTotalMB) }} MB</span>
                      </div>
                      <div class="ai-tip-row">
                          <span style="color:#64748b;">Sisa</span>
                          <span style="font-weight:700;color:#38bdf8;">{{ number_format(max(0, $__stTotalMB - $__stUsedMB), 2) }} MB</span>
                      </div>
                      <div style="margin-top:8px;background:rgba(56,189,248,0.08);border-radius:6px;padding:6px 8px;">
                          <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                              <span style="font-size:10px;color:#64748b;">Penggunaan</span>
                              <span style="font-size:10px;font-weight:700;color:{{ $__stPct > 80 ? '#f87171' : '#38bdf8' }};">{{ $__stPct }}%</span>
                          </div>
                          <div style="background:rgba(255,255,255,0.08);border-radius:4px;height:4px;overflow:hidden;">
                              <div style="background:{{ $__stPct > 80 ? '#ef4444' : '#38bdf8' }};width:{{ $__stPct }}%;height:100%;border-radius:4px;"></div>
                          </div>
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
                     <i class="bx bx-expand-alt header-link-icon full-screen-open" style="font-size:1.25rem"></i>
                     <i class="bx bx-collapse-alt header-link-icon full-screen-close d-none" style="font-size:1.25rem"></i>
                 </a>
                 <!-- End::header-link -->
             </div>
             <!-- End::header-element -->

             <!-- Start::header-element -->
             <div class="header-element mainuserProfile">
                 <!-- Start::header-link|dropdown-toggle -->
                 <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                     <div class="d-flex align-items-center" style="gap:8px;">
                         {{-- Avatar: gradient circle + icon (konsisten dengan meter kiri) --}}
                         <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#2E8DE1,#5B3FB0);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 6px rgba(46,141,225,.35);">
                             <i class="bx bx-user" style="color:#fff;font-size:1.1rem;line-height:1;"></i>
                         </div>
                         {{-- Nama user — hanya tampil di xl+ --}}
                         <span class="d-none d-xl-block fw-600" style="font-size:0.82rem;color:#fff;letter-spacing:.01em;white-space:nowrap;">{{auth()->user()->name}}</span>
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