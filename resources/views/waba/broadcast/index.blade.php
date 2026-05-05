@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
/* =============================================
   WABA Broadcast List - Premium Design
   ============================================= */

/* Summary bar */
.waba-summary-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}
.waba-sum-card {
    flex: 1;
    min-width: 140px;
    background: #fff;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    display: flex;
    align-items: center;
    gap: 0.875rem;
    transition: transform 0.15s;
}
.waba-sum-card:hover { transform: translateY(-2px); }
.waba-sum-card .sum-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}
.waba-sum-card .sum-value { font-size: 1.5rem; font-weight: 800; line-height: 1; }
.waba-sum-card .sum-label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; opacity: 0.6; }

.sum-total  .sum-icon { background: rgba(30,41,59,0.1);   color: #1e293b; }
.sum-waba   .sum-icon { background: rgba(5,150,105,0.12);  color: #0EA5E9; }
.sum-today  .sum-icon { background: rgba(29,78,216,0.1);   color: #1D4ED8; }
.sum-fail   .sum-icon { background: rgba(220,38,38,0.1);   color: #DC2626; }

/* Table card */
.broadcast-card {
    border-radius: 16px;
    border: none;
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    overflow: hidden;
}
.broadcast-card .card-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
}

/* DataTable overrides */
#broadcastTable thead th {
    background: #f8fafc;
    color: #374151;
    font-size: 0.73rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 2px solid #e5e7eb;
    padding: 0.85rem 1rem;
    white-space: nowrap;
}
#broadcastTable tbody td {
    padding: 0.85rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
#broadcastTable tbody tr:hover td { background: #fafcff; }

/* Status badges */
.badge.bg-success-transparent { background: rgba(16,185,129,0.12) !important; }
.badge.bg-warning-transparent  { background: rgba(245,158,11,0.12)  !important; }
.badge.bg-danger-transparent   { background: rgba(239,68,68,0.12)   !important; }
.badge.bg-secondary-transparent{ background: rgba(100,116,139,0.12) !important; }

/* Action buttons */
.btn-icon.btn-sm { width: 32px; height: 32px; padding: 0; }

/* Search bar */
.dataTables_filter input {
    border-radius: 8px !important;
    border: 1.5px solid #e5e7eb !important;
    padding: 0.4rem 0.8rem !important;
    font-size: 0.85rem !important;
}
.dataTables_length select {
    border-radius: 8px !important;
    border: 1.5px solid #e5e7eb !important;
}

/* Loading overlay */
.dt-loading {
    color: #0EA5E9;
    font-weight: 500;
}
</style>
@endsection

@section('button')
<div class="btn-list">
    <a href="{{route('waba.broadcast.create',$meta->id)}}"
       class="btn btn-primary d-none d-sm-inline-block">
        <i class="bx bx-plus-circle me-1"></i>
        {{__('general.add_data')}}
    </a>
    <a href="{{route('waba.broadcast.create',$meta->id)}}"
       class="btn btn-info d-sm-none btn-icon" aria-label="{{__('general.add_data')}}">
        <i class="bx bx-plus-circle"></i>
    </a>
</div>
@endsection

@section('content')

{{-- Summary Stats Bar --}}
<div class="waba-summary-bar" id="summaryBar">
    <div class="waba-sum-card sum-total">
        <div class="sum-icon"><i class="bx bx-broadcast"></i></div>
        <div>
            <div class="sum-value" id="sumTotal">–</div>
            <div class="sum-label">Total Broadcast</div>
        </div>
    </div>
    <div class="waba-sum-card sum-waba">
        <div class="sum-icon"><i class="bx bx-check-circle"></i></div>
        <div>
            <div class="sum-value" id="sumDone">–</div>
            <div class="sum-label">Selesai</div>
        </div>
    </div>
    <div class="waba-sum-card sum-today">
        <div class="sum-icon"><i class="bx bx-loader-alt"></i></div>
        <div>
            <div class="sum-value" id="sumPending">–</div>
            <div class="sum-label">Menunggu</div>
        </div>
    </div>
    <div class="waba-sum-card sum-fail">
        <div class="sum-icon"><i class="bx bx-time-five"></i></div>
        <div>
            <div class="sum-value" id="sumRecent">–</div>
            <div class="sum-label">Bulan Ini</div>
        </div>
    </div>
</div>

{{-- Main Table Card --}}
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card broadcast-card">
            <div class="row g-0">
                <x-waba-sidebar-update-component idwaba="{{$meta->id}}"></x-waba-sidebar-update-component>
                <div class="col-12 col-md-10 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-700" style="font-size:0.95rem;color:#1e293b;">
                                <i class="bx bx-broadcast me-2 text-success"></i>Riwayat Broadcast
                            </span>
                            <small class="text-muted ms-2">Terbaru di atas</small>
                        </div>
                        <div class="d-flex gap-2" id="filterBtns">
                            <button class="btn btn-sm btn-light rounded-pill active px-3"
                                    onclick="setFilter(null,this)">Semua</button>
                            <button class="btn btn-sm btn-light rounded-pill px-3"
                                    onclick="setFilter('done',this)"
                                    style="color:#0EA5E9">✅ Selesai</button>
                            <button class="btn btn-sm btn-light rounded-pill px-3"
                                    onclick="setFilter('pending',this)"
                                    style="color:#9ca3af">⏳ Menunggu</button>
                            <button class="btn btn-sm btn-light rounded-pill px-3"
                                    onclick="setFilter('partial_success',this)"
                                    style="color:#d97706">⚠️ Sebagian</button>
                            <button class="btn btn-sm btn-light rounded-pill px-3"
                                    onclick="setFilter('failed',this)"
                                    style="color:#dc2626">❌ Gagal</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-3">
                            <table id="broadcastTable" class="table table-bordered text-nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5%">#</th>
                                        <th>Judul Broadcast</th>
                                        <th>Jadwal</th>
                                        <th>Kategori Kontak</th>
                                        <th>Template</th>
                                        <th>Status Pengiriman</th>
                                        <th style="width:120px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
const META_ID = '{{ $meta->id }}';
let currentFilter = null;
let rowNumber = 0;

const dtTable = $('#broadcastTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    order: [[2, 'desc']], // schedule desc = terbaru pertama
    language: {
        searchPlaceholder: 'Cari judul, template...',
        sSearch: '',
        sLengthMenu: 'Tampilkan _MENU_',
        sInfo: 'Menampilkan _START_–_END_ dari _TOTAL_ broadcast',
        sInfoEmpty: 'Tidak ada broadcast',
        sZeroRecords: 'Tidak ada broadcast ditemukan',
        sProcessing: '<div class="dt-loading"><i class="bx bx-loader-alt bx-spin me-2"></i>Memuat data...</div>',
        oPaginate: { sPrevious: '← Prev', sNext: 'Next →' }
    },
    pageLength: 10,
    ajax: {
        url: `/app/waba/broadcast/list-data/${META_ID}`,
        data: function(d) {
            if (currentFilter) d.status_filter = currentFilter;
        }
    },
    columns: [
        {
            data: null,
            orderable: false,
            searchable: false,
            render: function(data, type, row, meta) {
                return '<span class="badge bg-light text-secondary rounded-pill">' +
                       (meta.settings._iDisplayStart + meta.row + 1) + '</span>';
            }
        },
        { data: 'title_col',    name: 'title_col',    orderable: true  },
        { data: 'schedule_col', name: 'schedule_col', orderable: true  },
        { data: 'category_name',name: 'category_name',orderable: false },
        { data: 'template_name',name: 'template_name',orderable: false },
        { data: 'status_col',   name: 'status_col',   orderable: false },
        { data: 'action_col',   name: 'action_col',   orderable: false },
    ],
    drawCallback: function(settings) {
        const json = this.api().ajax.json();
        if (json && json.recordsTotal !== undefined) {
            const total = json.recordsTotal;
            $('#sumTotal').text(total.toLocaleString('id-ID'));
        }
    }
});

function setFilter(f, btn) {
    currentFilter = f;
    $('.btn[onclick*="setFilter"]').removeClass('active');
    $(btn).addClass('active');
    dtTable.ajax.reload();
}

// Load summary stats separately
$.get(`/app/waba/broadcast/list-data/${META_ID}?stats_only=1&length=1&start=0`, function(data) {
    if (data.recordsTotal) $('#sumTotal').text(parseInt(data.recordsTotal).toLocaleString('id-ID'));
});

// Quick stat: bulan ini
$.get(`/app/waba/broadcast/list-data/${META_ID}?month_filter=1&length=1&start=0`, function(data) {
    if (data.recordsTotal !== undefined) $('#sumRecent').text(parseInt(data.recordsTotal).toLocaleString('id-ID'));
});
</script>
@endsection
