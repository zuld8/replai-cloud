@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
/* ══════════════════════════════════════════
   Detail Broadcast — Donut Summary Card
   ══════════════════════════════════════════ */

/* Header */
.waba-detail-header {
    background: #fff; border: 0.5px solid #E4EAF2;
    border-radius: 10px; box-shadow: 0 1px 3px rgba(16,42,74,0.06);
    padding: 11px 16px; margin-bottom: 10px;
    display: flex; align-items: center; gap: 12px;
}
.waba-detail-icon {
    width: 36px; height: 36px; border-radius: 9px;
    background: #EAF3FC; color: #1B5FA6;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.waba-detail-header h4 { font-size: 13px; font-weight: 600; color: #1E2A4A; margin: 0; }
.waba-detail-header p  { font-size: 11px; color: #64748B; margin: 2px 0 0; }

/* Donut summary card */
.bc-donut-card {
    background: #fff; border: 0.5px solid #E4EAF2;
    border-radius: 10px; box-shadow: 0 1px 3px rgba(16,42,74,0.06);
    padding: 14px 18px; margin-bottom: 10px;
    display: flex; align-items: center; gap: 20px;
}
.bc-donut-wrap { flex-shrink: 0; width: 160px; }
.bc-conic {
    width: 160px; height: 160px; border-radius: 50%;
    position: relative; transition: background 0.5s ease;
}
.bc-conic-center {
    position: absolute; inset: 22px; background: #fff;
    border-radius: 50%; display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 1px;
}
.bc-rate-val { font-size: 26px; font-weight: 700; color: #16A34A; line-height: 1; }
.bc-rate-val.rate-mid { color: #D97706; }
.bc-rate-val.rate-low { color: #DC2626; }
.bc-rate-lbl { font-size: 10px; color: #64748B; }
.bc-donut-legend { flex: 1; }
.bc-legend-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0; column-gap: 20px;
}
.bc-legend-row {
    display: flex; align-items: baseline;
    justify-content: space-between;
    padding: 5px 0; border-bottom: 0.5px solid #F1F5F9;
    font-size: 12px; color: #64748B;
}
.bc-legend-row:last-child { border-bottom: none; }
.bc-legend-row .lbl { display: flex; align-items: center; gap: 5px; }
.bc-legend-row .num { font-size: 15px; font-weight: 600; color: #1E2A4A; }
.bc-legend-row.is-total .lbl { font-weight: 600; color: #1E2A4A; }
.bc-legend-row.is-total .num { font-size: 16px; color: #1B5FA6; }
.bc-legend-sub {
    font-size: 10px; color: #94A3B8; margin-top: 8px; padding-top: 8px;
    border-top: 0.5px solid #F1F5F9;
}
.bc-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

@media (max-width: 640px) {
    .bc-donut-card { flex-direction: column; align-items: flex-start; }
    .bc-donut-wrap { width: 100%; }
    .bc-legend-grid { grid-template-columns: 1fr 1fr; }
}

/* Table card */
.table-card {
    border-radius: 10px; border: 0.5px solid #E4EAF2 !important;
    box-shadow: 0 1px 3px rgba(16,42,74,0.06); overflow: hidden;
}
.table-card .card-header {
    background: #F5F8FC !important; border-bottom: 0.5px solid #E4EAF2 !important;
    padding: 9px 14px !important;
}

#resultBlash thead th {
    background: #F5F8FC; color: #374151;
    font-size: 10.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.04em;
    border-bottom: 1px solid #E4EAF2; padding: 8px 12px;
}
#resultBlash tbody td {
    padding: 8px 12px; vertical-align: middle;
    font-size: 13px; border-bottom: 1px solid #F1F5F9;
}
#resultBlash tbody tr:hover td { background: #FAFCFF; }
</style>
@endsection

@section('button')
<div class="btn-list">
    <span class="d-none d-sm-inline">
        <a href="{{route('blash.export',$blash->id)}}" target="_blank" class="btn btn-dark">
            <i class="ti ti-download me-1"></i> {{__('general.export_data')}}
        </a>
    </span>
    <a href="{{route('waba.broadcast',$meta->id)}}" class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-chevron-left"></i> {{__('blash.back_to')}}
    </a>
    <a href="{{route('waba.broadcast',$meta->id)}}" class="btn btn-info d-sm-none btn-icon">
        <i class="bx bx-chevron-left"></i>
    </a>
    <button type="button" id="refresh_button" class="d-none"></button>
    <input type="hidden" value="<?= $blash->id; ?>" id="idBlash">
    <input type="hidden" value="<?= $meta->id; ?>" id="idMeta">
</div>
@endsection

@section('content')

{{-- Broadcast Info Header --}}
<div class="waba-detail-header">
    <div class="waba-detail-icon"><i class="bx bx-broadcast"></i></div>
    <div>
        <h4>{{ $blash->name }}</h4>
        <p>
            Dijadwal: {{ $blash->schedule ? \Carbon\Carbon::parse($blash->schedule)->format('d M Y H:i') : '-' }}
            &nbsp;·&nbsp; Dibuat: {{ $blash->created_at ? \Carbon\Carbon::parse($blash->created_at)->format('d M Y H:i') : '-' }}
        </p>
    </div>
</div>

{{-- ═══ Donat Ringkasan (CSS conic-gradient, no JS library) ═══ --}}
<div class="bc-donut-card" id="statCards">
    {{-- Donat CSS --}}
    <div class="bc-donut-wrap">
        <div class="bc-conic" id="donutRing" style="background:conic-gradient(#E2E8F0 0% 100%);">
            <div class="bc-conic-center">
                <span class="bc-rate-val" id="donutRate">–</span>
                <span class="bc-rate-lbl">sampai HP</span>
            </div>
        </div>
    </div>
    {{-- Legend angka --}}
    <div class="bc-donut-legend">
        <div class="bc-legend-grid">
            <div class="bc-legend-row">
                <span class="lbl"><span class="bc-dot" style="background:#16A34A"></span>Sampai ke HP</span>
                <span class="num" id="statDelivered">–</span>
            </div>
            <div class="bc-legend-row">
                <span class="lbl"><span class="bc-dot" style="background:#2E8DE1"></span>Dibaca</span>
                <span class="num" id="statRead">–</span>
            </div>
            <div class="bc-legend-row">
                <span class="lbl"><span class="bc-dot" style="background:#DC2626"></span>Gagal</span>
                <span class="num" id="statFailed">–</span>
            </div>
            <div class="bc-legend-row">
                <span class="lbl"><span class="bc-dot" style="background:#9B8EC4"></span>Tertunda</span>
                <span class="num" id="statTimeout">–</span>
            </div>
            <div class="bc-legend-row">
                <span class="lbl"><span class="bc-dot" style="background:#E2E8F0;border:1px solid #CBD5E1"></span>Menunggu</span>
                <span class="num" id="statPending">–</span>
            </div>
            <div class="bc-legend-row is-total">
                <span class="lbl">Total</span>
                <span class="num" id="statTotal">–</span>
            </div>
        </div>
        <div class="bc-legend-sub" id="legendSub">–</div>
    </div>
</div>





{{-- Detail DataTable --}}
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span style="font-size:13px;font-weight:600;color:#1E2A4A;">Detail Penerima</span>
        <div class="d-flex gap-2" id="filterBadges">
            <button class="bc-filter-btn active" onclick="filterTable(null, this)">Semua</button>
            <button class="bc-filter-btn" onclick="filterTable('delivered', this)">✓✓ Sampai ke HP</button>
            <button class="bc-filter-btn" onclick="filterTable('read', this)">✓✓ Dibaca</button>
            <button class="bc-filter-btn" onclick="filterTable('failed_real', this)" style="color:#B91C1C">✕ Gagal</button>
            <button class="bc-filter-btn" onclick="filterTable('timeout', this)" style="color:#5B3FB0">⟳ Tertunda</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="p-3">
            <table id="resultBlash" class="table table-bordered text-nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>No. WhatsApp</th>
                        <th>Status</th>
                        <th>Log / Respon</th>
                        <th>Waktu Kirim</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
    const blashId = $("#idBlash").val();
    const metaId  = $("#idMeta").val();
    let currentFilter = null;
    let dtTable;

    $(document).ready(function() {
        dtTable = $('#resultBlash').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: 'Cari nama, nomor...',
                sSearch: '',
                sLengthMenu: 'Tampilkan _MENU_',
                sInfo: 'Menampilkan _START_–_END_ dari _TOTAL_',
                sInfoEmpty: 'Tidak ada data',
                sZeroRecords: 'Tidak ada data ditemukan',
                oPaginate: {
                    sPrevious: '← Prev',
                    sNext: 'Next →'
                }
            },
            pageLength: 25,
            processing: true,
            serverSide: true,
            aaSorting: [[4, 'desc']],
            ajax: {
                url: `/app/waba/broadcast/detail-data/${metaId}/${blashId}`,
                data: function(d) {
                    if (currentFilter !== null) d.status_filter = currentFilter;
                    return d;
                }
            },
            columns: [
                { data: 'store',              name: 'store',              orderable: false },
                { data: 'phone',              name: 'phone',              orderable: false },
                { data: 'sending_status_col', name: 'sending_status_col', orderable: false },
                { data: 'log',                name: 'log',                orderable: false, searchable: false },
                { data: 'date',               name: 'date',               orderable: false },
            ],
            drawCallback: function(settings) {
                const json = this.api().ajax.json();
                if (json) {
                    const total          = json.total            ?? 0;
                    const sent           = json.sent             ?? 0;
                    const delivered      = json.delivered        ?? 0;
                    const read           = json.read             ?? 0;
                    const deliveryFailed = json.deliveryFailed   ?? 0;
                    const timeout        = json.deliveryTimeout  ?? 0;
                    const pending        = Math.max(0, total - delivered - read - deliveryFailed - timeout);
                    const reached        = delivered + read;
                    const deliveryRate   = total > 0 ? Math.round(reached / total * 100) : 0;

                    // Legend angka
                    $('#statTotal').text(total.toLocaleString('id-ID'));
                    $('#statDelivered').text(delivered.toLocaleString('id-ID'));
                    $('#statRead').text(read.toLocaleString('id-ID'));
                    $('#statFailed').text(deliveryFailed.toLocaleString('id-ID'));
                    $('#statTimeout').text(timeout.toLocaleString('id-ID'));
                    $('#statPending').text(pending.toLocaleString('id-ID'));
                    $('#legendSub').text(sent.toLocaleString('id-ID') + ' terkirim ke Meta · ' + reached.toLocaleString('id-ID') + ' sampai HP');

                    // ── Conic-gradient donat ────────────────────────────────
                    const rateColor = deliveryRate >= 70 ? '#16A34A' : deliveryRate >= 40 ? '#D97706' : '#DC2626';
                    const rateEl2 = document.getElementById('donutRate');
                    if (rateEl2) {
                        rateEl2.textContent = deliveryRate + '%';
                        rateEl2.className = 'bc-rate-val' + (deliveryRate >= 70 ? '' : deliveryRate >= 40 ? ' rate-mid' : ' rate-low');
                    }
                    // Segmen kumulatif (%)
                    const pD  = total > 0 ? delivered      / total * 100 : 0;
                    const pR  = total > 0 ? read            / total * 100 : 0;
                    const pF  = total > 0 ? deliveryFailed  / total * 100 : 0;
                    const pT  = total > 0 ? timeout         / total * 100 : 0;
                    const c1 = pD;           // delivered end
                    const c2 = c1 + pR;      // read end
                    const c3 = c2 + pF;      // failed end
                    const c4 = c3 + pT;      // timeout end
                    const grad = total > 0
                        ? `conic-gradient(#16A34A 0% ${c1}%,#2E8DE1 ${c1}% ${c2}%,#DC2626 ${c2}% ${c3}%,#9B8EC4 ${c3}% ${c4}%,#E2E8F0 ${c4}% 100%)`
                        : 'conic-gradient(#E2E8F0 0% 100%)';
                    const ring = document.getElementById('donutRing');
                    if (ring) ring.style.background = grad;
                }
            }
        });

        // Stats loaded from DataTables response (no extra request needed)

        $("#refresh_button").on("click", function() {
            dtTable.ajax.reload();
            loadStats();
        });
    });



    function filterTable(status, btn) {
        currentFilter = status;
        $('.btn[onclick*="filterTable"]').removeClass('active');
        $(btn).addClass('active');
        dtTable.ajax.reload();
    }
</script>
@endsection
