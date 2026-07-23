@extends('layouts.admin')

@section('content')

{{-- ═══════════════════════════════════════════════════════
     SEKSI 1 — RINGKASAN KPI
═══════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center mb-2 mt-1">
    <span class="text-uppercase fs-11 fw-semibold text-muted me-2 ls-1">Ringkasan</span>
    <div style="flex:1;height:1px;background:rgba(255,255,255,.08)"></div>
</div>
<div class="row g-3 mb-4">

    {{-- Merchant --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card custom-card">
            <div class="card-body p-3">
                <p class="text-muted mb-1 fs-12">Merchant</p>
                <h4 class="mb-0 fw-bold">{{ number_format($summary['merchants']) }}</h4>
            </div>
        </div>
    </div>

    {{-- Bisnis --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card custom-card">
            <div class="card-body p-3">
                <p class="text-muted mb-1 fs-12">Bisnis</p>
                <h4 class="mb-0 fw-bold">{{ number_format($summary['business']) }}</h4>
            </div>
        </div>
    </div>

    {{-- Langganan Aktif (highlighted) --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card custom-card" style="border:1px solid #2E8DE1;background:rgba(46,141,225,.08)">
            <div class="card-body p-3">
                <p class="mb-1 fs-12" style="color:#2E8DE1">Langganan Aktif</p>
                <h4 class="mb-0 fw-bold" style="color:#2E8DE1">{{ number_format($sub['aktif']) }}</h4>
            </div>
        </div>
    </div>

    {{-- Konversi --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card custom-card">
            <div class="card-body p-3">
                <p class="text-muted mb-1 fs-12">Konversi</p>
                <h4 class="mb-0 fw-bold">{{ $sub['konversi'] }}%</h4>
            </div>
        </div>
    </div>

    {{-- Topup --}}
    <div class="col-6 col-md-4 col-lg">
        <div class="card custom-card">
            <div class="card-body p-3">
                <p class="text-muted mb-1 fs-12">Topup</p>
                <h4 class="mb-0 fw-bold">Rp{{ $summary['topup'] >= 1000000 ? number_format($summary['topup']/1000000,1).'jt' : number_format($summary['topup']) }}</h4>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════
     SEKSI 2 — MRR CHART + PERLU TINDAKAN
═══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- MRR Chart --}}
    <div class="col-lg-7 col-12">
        <div class="card custom-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted mb-1 fs-12">Pendapatan (MRR)</p>
                        <h4 class="mb-0 fw-bold">
                            Rp{{ $mrrThisMonth >= 1000000 ? number_format($mrrThisMonth/1000000,2).'jt' : number_format($mrrThisMonth) }}
                            @if($mrrGrowth > 0)
                                <span class="fs-12 ms-1" style="color:#16A34A">▲{{ $mrrGrowth }}%</span>
                            @elseif($mrrGrowth < 0)
                                <span class="fs-12 ms-1" style="color:#C0392B">▼{{ abs($mrrGrowth) }}%</span>
                            @endif
                        </h4>
                    </div>
                    <span class="badge" style="background:rgba(46,141,225,.15);color:#2E8DE1;font-size:11px">12 bln</span>
                </div>
                <div id="mrrChart"></div>
            </div>
        </div>
    </div>

    {{-- Perlu Tindakan --}}
    <div class="col-lg-5 col-12">
        <div class="card custom-card h-100">
            <div class="card-body p-3">
                <p class="fw-semibold mb-3 fs-13">🔔 Perlu Tindakan</p>

                {{-- Berbayar mulai sepi (churn asli: aktif & berbayar tapi sepi 3 hari) --}}
                @if($churn > 0)
                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded" style="background:rgba(192,57,43,.08)">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="color:#C0392B">⚠️</span>
                        <span class="fs-12" style="color:#C0392B">{{ $churn }} berbayar mulai sepi</span>
                    </div>
                    <a href="/administrator/business" class="fs-12 fw-semibold" style="color:#C0392B">Follow-up →</a>
                </div>
                @endif

                {{-- Segera habis (< 7 hari) --}}
                @php $semuaHabis = $mustFollow->count(); @endphp
                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded" style="background:rgba(224,145,47,.08)">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="color:#E0912F">🕐</span>
                        <span class="fs-12">{{ $semuaHabis }} segera habis (7 hari)</span>
                    </div>
                    <a href="/administrator/business" class="fs-12 fw-semibold" style="color:#E0912F">Ingatkan →</a>
                </div>

                {{-- Tanpa paket --}}
                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded" style="background:rgba(100,116,139,.08)">
                    <div class="d-flex align-items-center">
                        <span class="me-2">👤</span>
                        <span class="fs-12">{{ number_format($sub['tanpa_paket']) }} tanpa paket</span>
                    </div>
                    <a href="/administrator/business" class="fs-12 fw-semibold text-primary">Hubungi →</a>
                </div>

                {{-- Belum bayar --}}
                @if($notPayment->count() > 0)
                <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background:rgba(91,63,176,.08)">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="color:#5B3FB0">💳</span>
                        <span class="fs-12">{{ $notPayment->count() }} pending pembayaran</span>
                    </div>
                    <a href="/administrator/transaction" class="fs-12 fw-semibold" style="color:#5B3FB0">Cek →</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     SEKSI 3 — PENGGUNAAN AI (lazy-loaded via AJAX)
═══════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center mb-2">
    <span class="text-uppercase fs-11 fw-semibold text-muted me-2 ls-1">✦ Penggunaan AI</span>
    <div style="flex:1;height:1px;background:rgba(255,255,255,.08)"></div>
</div>
<div class="row g-3 mb-4">

    {{-- AI Stats + Chart (skeleton → diisi JS) --}}
    <div class="col-lg-7 col-12">
        <div class="card custom-card h-100">
            <div class="card-body p-3">
                <div class="row g-2 mb-3" id="ai-stats-row">
                    <div class="col-6 col-sm-3"><p class="text-muted mb-1 fs-11">AI Credit Terpakai</p>
                        <h5 class="mb-0 fw-bold" style="color:#6a5ad0" id="ai-credit"><span class="sk-pulse">...</span></h5></div>
                    <div class="col-6 col-sm-3"><p class="text-muted mb-1 fs-11">Balasan AI</p>
                        <h5 class="mb-0 fw-bold" id="ai-replies"><span class="sk-pulse">...</span></h5></div>
                    <div class="col-6 col-sm-3"><p class="text-muted mb-1 fs-11">Otomatisasi</p>
                        <h5 class="mb-0 fw-bold" style="color:#16A34A" id="ai-automation"><span class="sk-pulse">...</span></h5></div>
                    <div class="col-6 col-sm-3"><p class="text-muted mb-1 fs-11">AI Training</p>
                        <h5 class="mb-0 fw-bold" id="ai-training"><span class="sk-pulse">...</span></h5></div>
                </div>
                <p class="text-muted fs-11 mb-1">Tren AI credit · bulan ini</p>
                <div id="responseAiChart"></div>
            </div>
        </div>
    </div>

    {{-- AI Top 5 (skeleton → diisi JS) --}}
    <div class="col-lg-5 col-12">
        <div class="card custom-card h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="fw-semibold mb-0 fs-13">Konsumsi AI Terbesar</p>
                    <span class="text-muted fs-11" id="ai-top-total">loading...</span>
                </div>
                <div id="ai-top-list">
                    <div class="sk-bar mb-3"></div><div class="sk-bar mb-3"></div><div class="sk-bar"></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════
     SEKSI 4 — CHANNEL TERDAFTAR
═══════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center mb-2">
    <span class="text-uppercase fs-11 fw-semibold text-muted me-2 ls-1">Channel Terdaftar</span>
    <div style="flex:1;height:1px;background:rgba(255,255,255,.08)"></div>
</div>
<div class="row g-3 mb-4">
    @php
    $channelList = [
        ['icon'=>'bxl-whatsapp', 'label'=>'WA Business', 'val'=>$channels['waba'],      'color'=>'#25D366'],
        ['icon'=>'bxl-whatsapp', 'label'=>'WA Personal', 'val'=>$channels['wa_pers'],   'color'=>'#25D366', 'opacity'=>'.7'],
        ['icon'=>'bxl-instagram','label'=>'Instagram',   'val'=>$channels['instagram'],  'color'=>'#C13584'],
        ['icon'=>'bxl-messenger','label'=>'Messenger',   'val'=>$channels['messenger'],  'color'=>'#0078FF'],
        ['icon'=>'bxl-telegram', 'label'=>'Telegram',    'val'=>$channels['telegram'],   'color'=>'#0088CC'],
        ['icon'=>'bx-chat',      'label'=>'Live Chat',   'val'=>$channels['livechat'],   'color'=>'#E0912F'],
    ];
    @endphp
    @foreach($channelList as $ch)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card custom-card text-center">
            <div class="card-body p-3">
                <i class="bx {{ $ch['icon'] }} fs-28 mb-1" style="color:{{ $ch['color'] }};opacity:{{ $ch['opacity'] ?? '1' }}"></i>
                <h5 class="mb-0 fw-bold">{{ number_format($ch['val']) }}</h5>
                <p class="text-muted mb-0 fs-11">{{ $ch['label'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════
     SEKSI 5 — KESEHATAN LANGGANAN + AKTIVITAS
═══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Kesehatan Langganan --}}
    <div class="col-lg-7 col-12">
        <div class="card custom-card h-100">
            <div class="card-body p-3">
                <p class="fw-semibold mb-3 fs-13">Kesehatan Langganan</p>
                @php
                    $total   = max(1, $sub['total']);
                    $pAktif  = round($sub['aktif'] / $total * 100);
                    $pTanpa  = round($sub['tanpa_paket'] / $total * 100);
                    $mustCnt = $mustFollow->count();
                    $pMust   = round($mustCnt / $total * 100);
                @endphp
                <div class="progress mb-3" style="height:12px">
                    <div class="progress-bar" style="width:{{ $pAktif }}%;background:#16A34A" title="Aktif: {{ $sub['aktif'] }}"></div>
                    <div class="progress-bar" style="width:{{ $pMust }}%;background:#E0912F" title="Segera habis: {{ $mustCnt }}"></div>
                    <div class="progress-bar" style="width:{{ $pTanpa }}%;background:#64748B" title="Tanpa paket: {{ $sub['tanpa_paket'] }}"></div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <span class="fs-12"><span class="badge me-1" style="background:#16A34A">●</span>Aktif · <strong>{{ $sub['aktif'] }}</strong></span>
                    </div>
                    <div class="col-6">
                        <span class="fs-12"><span class="badge me-1" style="background:#E0912F">●</span>Segera habis · <strong>{{ $mustCnt }}</strong></span>
                    </div>
                    <div class="col-6">
                        <span class="fs-12"><span class="badge me-1" style="background:#64748B">●</span>Tanpa paket · <strong>{{ $sub['tanpa_paket'] }}</strong></span>
                    </div>
                    <div class="col-6">
                        <span class="fs-12">Total Bisnis · <strong>{{ $sub['total'] }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Aktivitas --}}
    <div class="col-lg-5 col-12">
        <div class="card custom-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="fw-semibold mb-0 fs-13">Aktivitas</p>
                    <div class="d-flex gap-1">
                        <a href="?range=7"  class="btn btn-sm {{ $range == 7  ? 'btn-primary' : 'btn-outline-secondary' }}" style="font-size:11px;padding:2px 8px">7h</a>
                        <a href="?range=30" class="btn btn-sm {{ $range == 30 ? 'btn-primary' : 'btn-outline-secondary' }}" style="font-size:11px;padding:2px 8px">30h</a>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <p class="text-muted mb-1 fs-11">Blast WA</p>
                        <h6 class="fw-bold mb-0">{{ $activity['blast_w'] >= 1000 ? number_format($activity['blast_w']/1000,1).'k' : number_format($activity['blast_w']) }}</h6>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1 fs-11">Blast Email</p>
                        <h6 class="fw-bold mb-0">{{ $activity['blast_e'] >= 1000 ? number_format($activity['blast_e']/1000,1).'k' : number_format($activity['blast_e']) }}</h6>
                    </div>
                    <div class="col-4">
                        <p class="text-muted mb-1 fs-11">Scrap Maps</p>
                        <h6 class="fw-bold mb-0">{{ number_format($activity['scrap_maps']) }}</h6>
                    </div>
                    <div class="col-4">
                        <p class="text-muted mb-1 fs-11">Scrap Grup</p>
                        <h6 class="fw-bold mb-0">{{ number_format($activity['scrap_group']) }}</h6>
                    </div>
                    <div class="col-4">
                        <p class="text-muted mb-1 fs-11">Scrap Kontak</p>
                        <h6 class="fw-bold mb-0">{{ number_format($activity['scrap_kontak']) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════
     SEKSI 6 — BISNIS AKTIF TERKINI
═══════════════════════════════════════════════════════ --}}
<div class="card custom-card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between py-2">
        <div>
            <span class="fw-semibold fs-13">Bisnis Aktif Terkini</span>
            <span class="text-muted fs-11 ms-2" id="active-biz-count">· <span class="sk-pulse">...</span> aktif (7h)</span>
        </div>
        <a href="/administrator/business" class="fs-12 text-primary">Lihat semua →</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="fs-12 fw-semibold text-muted ps-3">Bisnis</th>
                        <th class="fs-12 fw-semibold text-muted">Aktivitas Terakhir</th>
                        <th class="fs-12 fw-semibold text-muted text-end pe-3">Chat 7h</th>
                    </tr>
                </thead>
                <tbody id="active-biz-tbody">
                    <tr><td colspan="3" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        <span class="text-muted fs-12">Memuat data...</span>
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     SEKSI 7 — PAKET SEGERA HABIS + BELUM BERLANGGANAN
═══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6 col-12">
        <div class="card custom-card h-100">
            <div class="card-header py-2">
                <div class="card-title mb-0 fs-13">{{ __('dashboard.expiring_packages') }}</div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @forelse($mustFollow as $follow)
                    <li class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fs-13 mb-0">{{ $follow->business->name ?? '' }}</h6>
                                <span class="fs-11 text-muted">{{ $follow->business->merchant->name ?? '' }}</span>
                            </div>
                            <div class="text-end">
                                <a class="d-block fs-12 text-primary">{{ $follow->last_expire_date }}</a>
                                <span class="fs-11 text-muted">{{ $follow->package->name ?? '' }}</span>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-muted text-center py-3 fs-12">Tidak ada paket yang segera habis</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-12">
        <div class="card custom-card h-100">
            <div class="card-header py-2">
                <div class="card-title mb-0 fs-13">{{ __('dashboard.business_not_subscribed') }}</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="fs-12 text-muted ps-3">Bisnis</th>
                                <th class="fs-12 text-muted">Merchant</th>
                                <th class="fs-12 text-muted">Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($merchantNotPackage as $not)
                            <tr>
                                <td class="ps-3 fs-12">{{ $not->name }}</td>
                                <td class="fs-12">{{ $not->merchant->name ?? '' }}</td>
                                <td class="fs-12 text-muted">{{ $not->created_at->format('d M') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3 fs-12">Semua bisnis sudah berlangganan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.js') }}"></script>
<style>
.sk-pulse { display:inline-block; color:transparent; background:linear-gradient(90deg,rgba(255,255,255,.08) 25%,rgba(255,255,255,.18) 50%,rgba(255,255,255,.08) 75%);
  background-size:200% 100%; animation:sk-shimmer 1.4s infinite; border-radius:4px; min-width:40px; }
.sk-bar { height:18px; border-radius:4px; background:linear-gradient(90deg,rgba(255,255,255,.06) 25%,rgba(255,255,255,.14) 50%,rgba(255,255,255,.06) 75%);
  background-size:200% 100%; animation:sk-shimmer 1.4s infinite; }
@keyframes sk-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
</style>
<script>
// ── MRR Chart (12 bulan) ──
(function () {
    const mrrData = @json($mrr);
    if (!mrrData || !mrrData.length) return;
    const labels = mrrData.map(d => d.ym);
    const values = mrrData.map(d => parseFloat(d.total) || 0);

    new ApexCharts(document.querySelector("#mrrChart"), {
        chart:  { type: 'area', height: 130, toolbar: { show: false }, sparkline: { enabled: true } },
        series: [{ name: 'MRR', data: values }],
        stroke: { width: 2, curve: 'straight' },  // straight: apa adanya, bukan bell-curve simetris
        markers: { size: 3 },
        colors: ['#2E8DE1'],
        fill:   { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
        dataLabels: { enabled: false },
        tooltip: {
            y: { formatter: v => 'Rp' + (v >= 1000000 ? (v/1000000).toFixed(2) + 'jt' : v.toLocaleString('id-ID')) }
        },
        xaxis: { categories: labels, labels: { show: false } },
        yaxis: { labels: { show: false } },
        grid:  { show: false },
    }).render();
})();

// ── AI Credit Chart (existing endpoint, reuse) ──
(function () {
    const el = document.querySelector("#responseAiChart");
    if (!el) return;

    fetch("/administrator/dashboard/response-ai")
        .then(r => r.json())
        .then(data => {
            const now = new Date();
            const days = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
            const fullDates = Array.from({ length: days }, (_, i) => {
                const d = i + 1;
                return `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            });
            const map = data.reduce((a, i) => { a[i.date] = i.count; return a; }, {});
            const series = fullDates.map(d => map[d] || 0);

            new ApexCharts(el, {
                chart:  { height: 120, type: 'area', toolbar: { show: false }, sparkline: { enabled: false } },
                series: [{ name: 'AI Credit', data: series }],
                stroke: { width: 2, curve: 'smooth' },
                colors: ['#7367F0'],
                fill:   { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.05 } },
                dataLabels: { enabled: false },
                xaxis: { categories: fullDates, labels: { show: false } },
                yaxis: { labels: { formatter: v => v.toLocaleString() } },
                grid:  { borderColor: 'rgba(255,255,255,.05)' },
            }).render();
        })
        .catch(e => console.error('AI chart error', e));
})();

// ── LAZY LOAD: AI Stats + Bisnis Aktif ──
const palette = ['#2E8DE1','#16A34A','#5B3FB0','#E0912F','#C0392B'];
function bizColor(name) {
    let h = 0; for (let c of name) h = (h * 31 + c.charCodeAt(0)) & 0xffffffff;
    return palette[Math.abs(h) % palette.length];
}
function fmtNum(n) { return n >= 1000000 ? (n/1000000).toFixed(1)+'jt' : n >= 1000 ? (n/1000).toFixed(1)+'k' : n.toLocaleString('id-ID'); }
function diffHuman(dt) {
    const s = (Date.now() - new Date(dt)) / 1000;
    if (s < 60)    return 'baru saja';
    if (s < 3600)  return Math.floor(s/60)   + ' mnt lalu';
    if (s < 86400) return Math.floor(s/3600) + ' jam lalu';
    return Math.floor(s/86400) + ' hari lalu';
}

// Fetch AI Stats (ai_replies, automation, training, credit, top 5)
fetch('/administrator/dashboard/widgets/ai-stats')
    .then(r => r.json())
    .then(d => {
        const ai = d.ai || {};
        const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        setEl('ai-credit',     (d.credit || 0).toLocaleString('id-ID'));
        setEl('ai-replies',    (ai.ai_replies  || 0).toLocaleString('id-ID'));
        setEl('ai-automation', (ai.automation  || 0) + '%');
        setEl('ai-training',   (ai.training    || 0).toLocaleString('id-ID'));
        // Top list
        const top   = d.top || [];
        const total = top.reduce((a, i) => a + i.count, 0);
        setEl('ai-top-total', 'total ' + total.toLocaleString('id-ID'));
        const topEl = document.getElementById('ai-top-list');
        if (topEl) {
            topEl.innerHTML = top.length === 0
                ? '<p class="text-muted fs-12 text-center py-3">Belum ada data bulan ini</p>'
                : top.map(t => `
                    <div class="mb-3">
                      <div class="d-flex justify-content-between mb-1">
                        <span class="fs-12">${t.name}</span>
                        <span class="fs-12 text-muted">${t.pct}%</span>
                      </div>
                      <div class="progress" style="height:6px;background:rgba(106,90,208,.15)">
                        <div class="progress-bar" style="width:${t.pct}%;background:#6a5ad0"></div>
                      </div>
                    </div>`).join('');
        }
    })
    .catch(e => console.error('AI stats error', e));

// Fetch Bisnis Aktif (top 10, 7 hari)
fetch('/administrator/dashboard/widgets/active-biz')
    .then(r => r.json())
    .then(d => {
        const rows  = d.active || [];
        const cnt = document.getElementById('active-biz-count');
        if (cnt) cnt.innerHTML = '· ' + rows.length + ' aktif (7h)';
        const tbody = document.getElementById('active-biz-tbody');
        if (!tbody) return;
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Belum ada data aktivitas 7 hari</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map(r => {
            const name  = r.name || 'N/A';
            const init  = name.trim().split(/\s+/).map(w => w[0]).slice(0, 2).join('').toUpperCase();
            const color = bizColor(name);
            const diff  = diffHuman(r.last);
            const daysAgo = (Date.now() - new Date(r.last)) / 86400000;
            const cls   = daysAgo >= 3 ? 'text-warning' : 'text-success';
            const warn  = daysAgo >= 3 ? ' ⚠️' : '';
            return `<tr>
              <td class="ps-3"><div class="d-flex align-items-center">
                <div class="rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold text-white fs-11"
                     style="width:30px;height:30px;min-width:30px;background:${color}">${init}</div>
                <span class="fs-13">${name}</span></div></td>
              <td class="fs-12 ${cls}">${diff}${warn}</td>
              <td class="text-end pe-3 fs-13 fw-semibold">${fmtNum(r.chat_7d)}</td>
            </tr>`;
        }).join('');
    })
    .catch(e => console.error('Active biz error', e));

</script>
@endsection
