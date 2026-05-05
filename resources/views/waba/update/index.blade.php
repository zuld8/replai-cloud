@extends('layouts.app')

@section('styles')
<link href="{{asset('assets/libs/select2/select2.css')}}" rel="stylesheet">
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
                    <div class="card-body">
                        <h2 class="mb-4">{{__('page.waba.general')}}</h2>

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
                                'HIGH'           => 'High',
                                'MEDIUM'         => 'Medium',
                                'LOW'            => 'Low',
                                'UNKNOWN_RATING' => '-',
                                default          => ucfirst(strtolower($quality)),
                            };
                            $qualityColor = match($quality) {
                                'HIGH'   => 'color:#1a7a45;',
                                'MEDIUM' => 'color:#b07c00;',
                                'LOW'    => 'color:#c0392b;',
                                default  => '',
                            };
                            $statusColor = ($canSend === 'AVAILABLE') ? 'color:#1a7a45;' : '';
                        @endphp

                        {{-- Semua info dalam satu list tanpa duplikasi --}}
                        <div class="mb-2 d-flex justify-content-between">
                            Nama Akun :
                            <strong>{{ isset($detail['healt_status']['data']['name']) ? $detail['healt_status']['data']['name'] : '' }}</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            Tentang :
                            <strong>{{ isset($detail['business_detail']['about']) ? $detail['business_detail']['about'] : '' }}</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            Email :
                            <strong>{{ isset($detail['business_detail']['email']) ? $detail['business_detail']['email'] : '' }}</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            Bisa Mengirim Pesan :
                            <strong>{{ isset($detail['healt_status']['data']['health_status']['can_send_message']) ? $detail['healt_status']['data']['health_status']['can_send_message'] : '' }}</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            Deskripsi :
                            <strong>{{ isset($detail['business_detail']['description']) ? $detail['business_detail']['description'] : '' }}</strong>
                        </div>

                        <hr class="my-3">

                        {{-- Status & Kualitas Nomor --}}
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span>Nomor WhatsApp :</span>
                            <strong data-q="phone">{{ $displayPhone }}</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span>Status Nomor :</span>
                            <strong style="{{ $statusColor }}">{{ $canSend }}</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span>Kualitas Nomor :</span>
                            <strong data-q="quality" style="{{ $qualityColor }}">{{ $qualityLabel }}</strong>
                        </div>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span>Limit Broadcast :</span>
                            <strong data-q="tier">{{ $tierLabel }}</strong>
                        </div>
                        @if($lastUpdated)
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span>Terakhir Diperbarui :</span>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($lastUpdated)->format('d M Y, H:i') }}</small>
                        </div>
                        @endif

                        <hr class="my-3">

                        {{-- Kredensial Akun --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Kredensial Akun</span>
                            <span onclick="toggleCredentials()" style="cursor:pointer; color:#00ceec; font-size:12px;">
                                <i class="ti ti-eye" id="credToggleIcon"></i> Tampilkan untuk update
                            </span>
                        </div>
                        <div id="credentialForm" style="display:none;">
                            <p class="text-muted" style="font-size:12px; background:#fff8e1; padding:8px 12px; border-radius:6px;">
                                <i class="ti ti-alert-triangle me-1 text-warning"></i>
                                Hati-hati! Jangan bagikan ke siapapun. Isi hanya jika ingin memperbarui.
                            </p>
                            <div class="row g-3 mt-1">
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
