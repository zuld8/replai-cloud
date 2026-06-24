@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
/* ============================================
   WABA Broadcast Detail - Premium Design
   ============================================ */
.waba-detail-header {
    background: #fff; border: 0.5px solid #E4EAF2;
    border-radius: 10px; box-shadow: 0 1px 3px rgba(16,42,74,0.06);
    padding: 11px 16px; margin-bottom: 12px;
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

/* Stat cards */
.waba-stat-card {
    background: #fff; border: 0.5px solid #E4EAF2;
    border-radius: 10px; padding: 11px 13px;
    box-shadow: 0 1px 3px rgba(16,42,74,0.06);
    position: relative; overflow: hidden;
    transition: box-shadow 0.15s, transform 0.15s;
}
.waba-stat-card:hover { box-shadow: 0 4px 12px rgba(16,42,74,0.10); transform: translateY(-1px); }
.waba-stat-card .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #64748B; }
.waba-stat-card .stat-value { font-size: 22px; font-weight: 700; line-height: 1.2; margin: 3px 0 1px; color: #1E2A4A; }
.waba-stat-card .stat-sub { font-size: 10px; color: #94A3B8; }
.waba-stat-card .stat-icon { font-size: 22px; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); opacity: 0.12; color: #1E2A4A; }

.stat-total  .stat-icon { color: #1B5FA6; }
.stat-timeout .stat-icon { color: #5B3FB0; }
.stat-failed .stat-icon { color: #DC2626; }
.stat-rate   .stat-icon { color: #16A34A; }

/* Progress bar overall */
.overall-progress-wrap {
    background: #f1f5f9;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.overall-progress-bar {
    height: 8px; border-radius: 5px;
    background: #EEF2F7; overflow: hidden;
    margin: 8px 0; display: flex;
}
.overall-progress-fill {
    height: 100%; border-radius: 0;
    background: #16A34A; transition: width 0.8s ease;
}
.overall-progress-read  { height: 100%; background: #2E8DE1; }
.overall-progress-fail  { height: 100%; background: #DC2626; }
.overall-progress-timeout { height: 100%; background: #9B8EC4; }

/* Table card */
.table-card {
    border-radius: 14px;
    border: none;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    overflow: hidden;
}
.table-card .card-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
}
.table-card .card-header .card-title { font-weight: 700; font-size: 0.95rem; color: #1e293b; }

#resultBlash thead th {
    background: #f8fafc;
    color: #374151;
    font-size: 0.76rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 2px solid #e5e7eb;
    padding: 0.85rem 1rem;
}
#resultBlash tbody td {
    padding: 0.75rem 1rem;
    vertical-align: middle;
    font-size: 0.87rem;
    border-bottom: 1px solid #f1f5f9;
}
#resultBlash tbody tr:hover td { background: #fafafa; }

.badge.bg-success-transparent { background: rgba(16,185,129,0.12) !important; }
.badge.bg-danger-transparent  { background: rgba(239,68,68,0.12)  !important; }
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

{{-- Summary Stat Cards --}}
<div class="row mb-4" id="statCards">
    <div class="col-6 col-lg-2 mb-3">
        <div class="waba-stat-card stat-total">
            <div class="stat-label">Total Penerima</div>
            <div class="stat-value" id="statTotal">–</div>
            <div class="stat-sub">Semua kontak</div>
            <i class="bx bx-group stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-3">
        <div class="waba-stat-card">
            <div class="stat-label">Terkirim</div>
            <div class="stat-value" id="statSent">–</div>
            <div class="stat-sub">Masuk antrian Meta</div>
            <i class="bx bx-check stat-icon" style="color:#64748B"></i>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-3">
        <div class="waba-stat-card" style="background:#047857;color:#fff;">
            <div class="stat-label">Delivered</div>
            <div class="stat-value" id="statDelivered">–</div>
            <div class="stat-sub">Sampai ke HP</div>
            <i class="bx bx-check-double stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-3">
        <div class="waba-stat-card" style="background:#0284c7;color:#fff;">
            <div class="stat-label">Dibaca</div>
            <div class="stat-value" id="statRead">–</div>
            <div class="stat-sub">Pesan dibuka</div>
            <i class="bx bx-show stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-3">
        <div class="waba-stat-card stat-failed">
            <div class="stat-label">Gagal Asli</div>
            <div class="stat-value" id="statFailed">–</div>
            <div class="stat-sub">Error code Meta</div>
            <i class="bx bx-x-circle stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-3">
        <div class="waba-stat-card stat-timeout">
            <div class="stat-label">Nyangkut</div>
            <div class="stat-value" id="statTimeout">–</div>
            <div class="stat-sub">Recovery/timeout</div>
            <i class="bx bx-refresh stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-3">
        <div class="waba-stat-card stat-rate">
            <div class="stat-label">Delivery Rate</div>
            <div class="stat-value" id="statRate">–</div>
            <div class="stat-sub">Pesan sampai HP</div>
            <i class="bx bx-trending-up stat-icon"></i>
        </div>
    </div>
</div>

{{-- Overall Progress --}}
<div class="overall-progress-wrap mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <span class="fw-600" style="font-size:0.88rem;color:#374151;">Progress Pengiriman</span>
        <span id="progressLabel" class="fw-700" style="color:#0EA5E9;font-size:0.88rem;">– %</span>
    </div>
    <div class="overall-progress-bar">
        <div class="overall-progress-fill" id="progressFill" style="width:0%"></div>
    </div>
    <div class="d-flex gap-4 flex-wrap" style="font-size:0.76rem;color:#9ca3af;">
        <span><span style="color:#16A34A;font-weight:600;">■</span> Delivered</span>
        <span><span style="color:#2E8DE1;font-weight:600;">■</span> Dibaca</span>
        <span><span style="color:#DC2626;font-weight:600;">■</span> Gagal</span>
        <span><span style="color:#9B8EC4;font-weight:600;">■</span> Nyangkut</span>
        <span id="progressSub"></span>
    </div>
</div>

{{-- Detail DataTable --}}
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="card-title mb-0">Detail Penerima</div>
        <div class="d-flex gap-2" id="filterBadges">
            <button class="btn btn-sm btn-light rounded-pill active" onclick="filterTable(null, this)">Semua</button>
            <button class="btn btn-sm btn-light rounded-pill" onclick="filterTable('yes', this)" style="color:#0EA5E9;">✅ Antrian</button>
            <button class="btn btn-sm btn-light rounded-pill" onclick="filterTable('no', this)"  style="color:#DC2626;">❌ Gagal</button>
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
                    const deliveryFailed = json.deliveryFailed   ?? 0;  // gagal asli Meta
                    const timeout        = json.deliveryTimeout  ?? 0;  // recovery/nyangkut

                    // Delivery rate = (delivered + read) / total — TIDAK include timeout/gagal
                    const reached      = delivered + read;
                    const deliveryRate = total > 0 ? Math.round(reached / total * 100) : 0;

                    // Segmented bar %
                    const delivPct    = total > 0 ? Math.round(delivered / total * 100) : 0;
                    const readPct     = total > 0 ? Math.round(read      / total * 100) : 0;
                    const failPct     = total > 0 ? Math.round(deliveryFailed / total * 100) : 0;
                    const timeoutPct  = total > 0 ? Math.round(timeout   / total * 100) : 0;

                    $('#statTotal').text(total.toLocaleString('id-ID'));
                    $('#statSent').text(sent.toLocaleString('id-ID'));
                    $('#statDelivered').text(delivered.toLocaleString('id-ID'));
                    $('#statRead').text(read.toLocaleString('id-ID'));
                    $('#statFailed').text(deliveryFailed.toLocaleString('id-ID'));
                    $('#statTimeout').text(timeout.toLocaleString('id-ID'));
                    $('#statRate').text(deliveryRate + '%');

                    // Segmented progress bar
                    $('#progressFill').css('width', delivPct + '%');
                    $('#progressLabel').text(deliveryRate + '%');
                    $('#progressLabel').css('color', deliveryRate >= 70 ? '#16A34A' : deliveryRate >= 40 ? '#D97706' : '#DC2626');

                    let sub = reached.toLocaleString('id-ID') + ' sampai HP dari ' + total.toLocaleString('id-ID') + ' total';
                    if (deliveryFailed > 0) sub += ' · ' + deliveryFailed.toLocaleString('id-ID') + ' gagal asli';
                    if (timeout > 0)        sub += ' · ' + timeout.toLocaleString('id-ID') + ' nyangkut';
                    $('#progressSub').text(sub);
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
