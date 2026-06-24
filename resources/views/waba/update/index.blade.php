@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
<style>
/* ── Info Row Layout ── */
.info-row {
    display: flex; align-items: flex-start;
    padding: 6px 0; border-bottom: 0.5px solid #F1F5F9;
    font-size: 13px; gap: 8px;
}
.info-row:last-child { border-bottom: none; }
.info-row .lbl { width: 160px; flex-shrink: 0; color: #64748B; padding-top: 1px; }
.info-row .val { color: #1E2A4A; font-weight: 500; flex: 1; }
.info-row .val.empty { color: #94A3B8; font-weight: 400; font-style: italic; }

/* ── Status Badges ── */
.badge-status  { font-size: 11px; padding: 2px 9px; border-radius: 6px; font-weight: 600; display: inline-block; }
.badge-green   { background: #DCFCE7; color: #166534; }
.badge-limited { background: #FEF3C7; color: #854F0B; }
.badge-red     { background: #FEECEC; color: #B91C1C; }
.badge-muted   { background: #F1F5F9; color: #64748B; }

/* ── Section Header with Icon Chip ── */
.section-header {
    display: flex; align-items: center; gap: 8px;
    font-size: 0.88rem; font-weight: 700; color: #1E2A4A;
    margin-bottom: 0.6rem;
}
.icon-chip {
    width: 28px; height: 28px; border-radius: 7px;
    background: #EAF3FC; color: #2E8DE1;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 0.95rem; flex-shrink: 0;
}
.section-divider { border: none; border-top: 0.5px solid #E4EAF2; margin: 1rem 0; }

/* ── Credential toggle link ── */
.cred-toggle-link { font-size: 12px; color: #2E8DE1; cursor: pointer; margin-left: auto; }
.cred-toggle-link:hover { color: #1B5FA6; }
</style>
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('waba')}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="ti ti-arrow-left fs-16 me-1"></i>{{__('master.device.back_to_device_list')}}
    </a>
    <a href="{{route('waba')}}" class="btn btn-primary d-sm-none btn-icon" aria-label="{{__('master.device.back_to_device_list')}}">
        <i class="ti ti-arrow-left fs-16"></i>
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <x-validation-component></x-validation-component>
    <div class="col-xl-12">
        <div class="card">
            <div class="row g-0">
                <x-waba-sidebar-update-component idwaba="{{$meta->id}}"></x-waba-sidebar-update-component>
                <div class="col-12 col-md-10 d-flex flex-column">
                    <div class="card-body" style="min-height:400px;">

                        @php
                            $qCache       = $detail['waba_quality_cache'] ?? null;
                            $quality      = strtoupper($qCache['quality_rating'] ?? 'UNKNOWN');
                            $tier         = $qCache['messaging_limit_tier'] ?? null;
                            $displayPhone = $qCache['display_phone_number'] ?? ($waba_phone ?? '-');
                            $canSend      = $detail['healt_status']['data']['health_status']['can_send_message'] ?? '-';
                            $lastUpdated  = $detail['waba_quality_updated_at'] ?? null;
                            $tierMap = [
                                'TIER_50'        => '50 pesan/hari',
                                'TIER_250'       => '250 pesan/hari',
                                'TIER_1K'        => '1.000 pesan/hari',
                                'TIER_10K'       => '10.000 pesan/hari',
                                'TIER_100K'      => '100.000 pesan/hari',
                                'TIER_UNLIMITED' => 'Tidak Terbatas',
                            ];
                            $tierLabel = $tierMap[$tier] ?? ($tier ?? '-');
                            $qualityLabel = match($quality) {
                                'HIGH', 'GREEN'  => 'Green / High',
                                'MEDIUM','YELLOW'=> 'Medium',
                                'LOW',  'RED'    => 'Low',
                                'UNKNOWN_RATING','UNKNOWN' => 'Unknown',
                                default          => ucfirst(strtolower($quality)),
                            };
                            $qualityBadge = match($quality) {
                                'HIGH','GREEN'   => 'badge-green',
                                'MEDIUM','YELLOW'=> 'badge-limited',
                                'LOW','RED'      => 'badge-red',
                                default          => 'badge-muted',
                            };
                            $canSendBadge = match(strtoupper($canSend)) {
                                'AVAILABLE','CONNECTED' => 'badge-green',
                                'LIMITED'               => 'badge-limited',
                                default                 => (strlen($canSend) > 1 ? 'badge-muted' : 'badge-muted'),
                            };
                            $canSendLabel = match(strtoupper($canSend)) {
                                'AVAILABLE' => 'Tersedia',
                                'LIMITED'   => 'Limited',
                                default     => ($canSend ?: '— belum diisi'),
                            };
                        @endphp

                        {{-- Section: Informasi Umum --}}
                        <div class="section-header">
                            <span class="icon-chip"><i class="ti ti-info-circle"></i></span>
                            Informasi Umum
                        </div>

                        <div class="info-row">
                            <span class="lbl">Nama Akun</span>
                            @php $v = $detail['healt_status']['data']['name'] ?? ''; @endphp
                            <span class="val {{ $v ? '' : 'empty' }}">{{ $v ?: '— belum diisi' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="lbl">Tentang</span>
                            @php $v = $detail['business_detail']['about'] ?? ''; @endphp
                            <span class="val {{ $v ? '' : 'empty' }}">{{ $v ?: '— belum diisi' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="lbl">Email</span>
                            @php $v = $detail['business_detail']['email'] ?? ''; @endphp
                            <span class="val {{ $v ? '' : 'empty' }}">{{ $v ?: '— belum diisi' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="lbl">Deskripsi</span>
                            @php $v = $detail['business_detail']['description'] ?? ''; @endphp
                            <span class="val {{ $v ? '' : 'empty' }}">{{ $v ?: '— belum diisi' }}</span>
                        </div>

                        <hr class="section-divider">

                        {{-- Section: Nomor & Status --}}
                        <div class="section-header">
                            <span class="icon-chip"><i class="ti ti-phone"></i></span>
                            Nomor & Status
                        </div>

                        <div class="info-row">
                            <span class="lbl">Nomor WhatsApp</span>
                            <span class="val" data-q="phone">{{ $displayPhone }}</span>
                        </div>
                        <div class="info-row">
                            <span class="lbl">Bisa Mengirim Pesan</span>
                            <span class="badge-status {{ $canSendBadge }}" data-q="cansend">{{ $canSendLabel }}</span>
                        </div>
                        <div class="info-row">
                            <span class="lbl">Status Nomor</span>
                            <span class="badge-status {{ $canSendBadge }}" data-q="status">{{ $canSendLabel }}</span>
                        </div>
                        <div class="info-row">
                            <span class="lbl">Kualitas Nomor</span>
                            <span class="badge-status {{ $qualityBadge }}" data-q="quality">{{ $qualityLabel }}</span>
                        </div>
                        <div class="info-row">
                            <span class="lbl">Limit Broadcast</span>
                            <span class="val" data-q="tier">{{ $tierLabel }}</span>
                        </div>
                        @if($lastUpdated)
                        <div class="info-row">
                            <span class="lbl">Terakhir Diperbarui</span>
                            <span class="val empty" style="font-style:normal;">{{ \Carbon\Carbon::parse($lastUpdated)->format('d M Y, H:i') }}</span>
                        </div>
                        @endif

                        <hr class="section-divider">

                        {{-- Section: Kredensial Akun --}}
                        <div class="section-header">
                            <span class="icon-chip"><i class="ti ti-key"></i></span>
                            Kredensial Akun
                            <span class="cred-toggle-link ms-auto" onclick="toggleCredentials()">
                                <i class="ti ti-eye" id="credToggleIcon"></i> Tampilkan untuk update
                            </span>
                        </div>
                        <div id="credentialForm" style="display:none;">
                            <p class="text-muted mb-3" style="font-size:12px; background:#fff8e1; padding:8px 12px; border-radius:6px;">
                                <i class="ti ti-alert-triangle me-1 text-warning"></i>
                                Hati-hati! Jangan bagikan ke siapapun. Isi hanya jika ingin memperbarui.
                            </p>
                            <div class="row g-3">
                                <div class="col-lg-6 col-sm-12">
                                    <label class="form-label">Facebook App Secret</label>
                                    <input class="form-control" name="app_secret" form="credForm" type="password"
                                           placeholder="Kosongkan jika tidak ingin mengubah" autocomplete="new-password">
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <label class="form-label">Access Token</label>
                                    <input class="form-control" name="access_token" form="credForm" type="password"
                                           placeholder="Kosongkan jika tidak ingin mengubah" autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div id="credSaveFooter" style="display:none;">
                        <form id="credForm" action="<?= route('waba.general.update', $meta->id); ?>" enctype="multipart/form-data" method="POST">
                            @csrf
                        </form>
                        <div class="card-footer bg-transparent mt-auto d-flex justify-content-end">
                            <div class="btn-list p-3">
                                <button type="submit" form="credForm" class="btn btn-primary">
                                    <i class="ti ti-device-floppy fs-16 me-1"></i>{{__('general.save_change')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/libs/select2/select2.js')}}"></script>
<script>
// Async fetch quality data (non-blocking)
document.addEventListener('DOMContentLoaded', function() {
    fetchWabaQuality();
});

const TIER_MAP = {
    'TIER_50':        '50 pesan/hari',
    'TIER_250':       '250 pesan/hari',
    'TIER_1K':        '1.000 pesan/hari',
    'TIER_10K':       '10.000 pesan/hari',
    'TIER_100K':      '100.000 pesan/hari',
    'TIER_UNLIMITED': 'Tidak Terbatas'
};

function fetchWabaQuality() {
    const metaId = '{{ $meta->id }}';
    fetch('/app/waba/update/' + metaId + '/quality', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.ok ? r.json() : null)
    .then(json => {
        if (!json || !json.data) return;
        const d = json.data;
        const q = (d.quality_rating || '').toUpperCase();
        const qColor = q === 'GREEN' ? '#1a7a45' : q === 'YELLOW' ? '#b07c00' : q === 'RED' ? '#c0392b' : '';
        const qLabel = q === 'GREEN' ? 'Green' : q === 'YELLOW' ? 'Medium' : q === 'RED' ? 'Low' : (q || '-');

        const set = (attr, val, color) => {
            const el = document.querySelector('[data-q="' + attr + '"]');
            if (el) { el.textContent = val; if (color !== undefined) el.style.color = color; }
        };
        set('phone',   d.display_phone_number || '-');
        set('quality', qLabel, qColor);
        set('tier',    TIER_MAP[d.messaging_limit_tier] || d.messaging_limit_tier || '-');
        if (json.updated_at) set('updated', json.updated_at);
    })
    .catch(() => {});
}


function toggleCredentials() {
    const section = document.getElementById('credentialForm');
    const footer  = document.getElementById('credSaveFooter');
    const icon    = document.getElementById('credToggleIcon');
    const show = section.style.display === 'none';
    section.style.display = show ? 'block' : 'none';
    footer.style.display  = show ? 'block' : 'none';
    icon.className = show ? 'ti ti-eye-off' : 'ti ti-eye';
}
</script>
@endsection
