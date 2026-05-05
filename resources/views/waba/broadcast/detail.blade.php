@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
/* ============================================
   WABA Broadcast Detail - Premium Design
   ============================================ */
.waba-detail-header {
    background: linear-gradient(135deg, #0EA5E9 0%, #38BDF8 60%, #34D399 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.waba-detail-header::after {
    content: '';
    position: absolute;
    right: -30px; top: -30px;
    width: 140px; height: 140px;
    background: rgba(255,255,255,0.07);
    border-radius: 50%;
}
.waba-detail-header::before {
    content: '';
    position: absolute;
    right: 80px; bottom: -40px;
    width: 90px; height: 90px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}
.waba-detail-header h4 { font-weight: 700; font-size: 1.15rem; margin-bottom: 0.25rem; }
.waba-detail-header p  { opacity: 0.85; font-size: 0.82rem; margin-bottom: 0; }

/* Stat cards */
.waba-stat-card {
    border-radius: 14px;
    border: none;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    position: relative;
    overflow: hidden;
    transition: transform 0.18s, box-shadow 0.18s;
}
.waba-stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
.waba-stat-card .stat-label { font-size: 0.73rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.75; }
.waba-stat-card .stat-value { font-size: 2rem; font-weight: 800; line-height: 1.1; margin: 0.3rem 0; }
.waba-stat-card .stat-sub { font-size: 0.78rem; opacity: 0.7; }
.waba-stat-card .stat-icon { font-size: 2.5rem; position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%); opacity: 0.15; }

.stat-total   { background: #1e293b; color: #fff; }
.stat-sent    { background: #0EA5E9; color: #fff; }
.stat-failed  { background: #DC2626; color: #fff; }
.stat-rate    { background: #1D4ED8; color: #fff; }

/* Progress bar overall */
.overall-progress-wrap {
    background: #f1f5f9;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.overall-progress-bar {
    height: 12px;
    border-radius: 8px;
    background: #e5e7eb;
    overflow: hidden;
    margin: 0.75rem 0;
}
.overall-progress-fill {
    height: 100%;
    border-radius: 8px;
    background: linear-gradient(90deg, #0EA5E9, #38BDF8);
    transition: width 1s ease;
}

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
    <h4>📢 {{ $blash->name }}</h4>
    <p>
        Dijadwalkan: {{ $blash->schedule ? \Carbon\Carbon::parse($blash->schedule)->format('d M Y H:i') : '-' }}
        &nbsp;·&nbsp; Dibuat: {{ $blash->created_at ? \Carbon\Carbon::parse($blash->created_at)->format('d M Y H:i') : '-' }}
    </p>
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
        <div class="waba-stat-card stat-sent">
            <div class="stat-label">Antrian</div>
            <div class="stat-value" id="statSent">–</div>
            <div class="stat-sub">Masuk antrian Meta</div>
            <i class="bx bx-time-five stat-icon"></i>
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
            <div class="stat-label">Gagal</div>
            <div class="stat-value" id="statFailed">–</div>
            <div class="stat-sub">Tidak terkirim</div>
            <i class="bx bx-x-circle stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-3">
        <div class="waba-stat-card stat-rate">
            <div class="stat-label">Delivery Rate</div>
            <div class="stat-value" id="statRate">–</div>
            <div class="stat-sub">Persentase delivery</div>
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
        <span><span style="color:#F59E0B;font-weight:600;">■</span> Antrian</span>
        <span><span style="color:#047857;font-weight:600;">■</span> Delivered</span>
        <span><span style="color:#0284c7;font-weight:600;">■</span> Dibaca</span>
        <span><span style="color:#EF4444;font-weight:600;">■</span> Gagal</span>
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
                    const total     = json.total     ?? 0;
                    const sent      = json.sent      ?? 0;
                    const failed    = json.failed    ?? 0;
                    const delivered = json.delivered  ?? 0;
                    const read      = json.read      ?? 0;
                    const deliveryFailed = json.deliveryFailed ?? 0;

                    // Real delivery = delivered + read
                    const realDelivered = delivered + read;
                    const realFailed = failed + deliveryFailed;
                    const deliveryRate = total > 0 ? Math.round(realDelivered / total * 100) : 0;

                    $('#statTotal').text(total.toLocaleString('id-ID'));
                    $('#statSent').text(sent.toLocaleString('id-ID'));
                    $('#statDelivered').text(delivered.toLocaleString('id-ID'));
                    $('#statRead').text(read.toLocaleString('id-ID'));
                    $('#statFailed').text(realFailed.toLocaleString('id-ID'));

                    // Show delivery rate (not just sent rate)
                    const rate = total > 0 ? Math.round(sent / total * 100) : 0;
                    if (realDelivered > 0) {
                        $('#statRate').text(Math.round(realDelivered / total * 100) + '%');
                    } else {
                        $('#statRate').text(rate + '%');
                    }

                    const pct = total > 0 ? Math.min(100, rate) : 0;
                    $('#progressFill').css('width', pct + '%');
                    $('#progressLabel').text(pct + '%');
                    $('#progressLabel').css('color', pct >= 90 ? '#0EA5E9' : pct >= 60 ? '#D97706' : '#DC2626');

                    let sub = sent.toLocaleString('id-ID') + ' masuk antrian dari ' + total.toLocaleString('id-ID') + ' total';
                    if (realDelivered > 0) {
                        sub += ' · ' + realDelivered.toLocaleString('id-ID') + ' delivered';
                    }
                    if (realFailed > 0) {
                        sub += ' · ' + realFailed.toLocaleString('id-ID') + ' gagal';
                    }
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
