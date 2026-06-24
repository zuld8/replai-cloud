@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
/* ── Broadcast WABA — Brand-aligned compact style ───────────── */

/* 1. Hero: putih ringan + icon chip hijau */
.page-hero {
    background: #fff;
    border: 0.5px solid #E4EAF2;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(16,42,74,0.06);
    padding: 11px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}
.bc-hero-left { display:flex; align-items:center; gap:10px; }
.bc-hero-icon {
    width:34px; height:34px; border-radius:9px;
    background:#DCFCE7; color:#15803D;
    display:flex; align-items:center; justify-content:center;
    font-size:17px; flex-shrink:0;
}
.bc-hero-title { font-size:14px; font-weight:600; color:#1E2A4A; margin:0; line-height:1.3; }
.bc-hero-sub   { font-size:11px; color:#64748B; margin:1px 0 0; }

/* 2. Account Selector — compact on white bg */
.waba-account-selector {
    display: flex; align-items: center; gap: 8px;
    background: #F2F8FE; border: 0.5px solid #E4EAF2;
    border-radius: 8px; padding: 6px 11px;
    cursor: pointer; transition: background 0.15s;
    position: relative;
}
.waba-account-selector:hover { background: #E6F1FB; }
.waba-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: #25D366; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; flex-shrink: 0;
}
.waba-account-name  { font-size: 12px; font-weight: 600; color: #1E2A4A; }
.waba-account-phone { font-size: 11px; color: #64748B; }
.waba-account-selector .chevron { color: #64748B; font-size: 14px; }

/* 3. Account Dropdown */
.account-dropdown {
    position: absolute; top: calc(100% + 6px); right: 0;
    min-width: 260px; background: #fff;
    border-radius: 10px; box-shadow: 0 6px 24px rgba(16,42,74,0.12);
    border: 0.5px solid #E4EAF2; z-index: 999;
    overflow: hidden; display: none;
}
.account-dropdown.open { display: block; animation: bcFadeIn 0.14s ease; }
@keyframes bcFadeIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }
.account-dropdown-header {
    padding: 7px 12px; background: #F5F8FC;
    border-bottom: 0.5px solid #E4EAF2;
    font-size: 10px; font-weight: 700; color: #64748B;
    text-transform: uppercase; letter-spacing: .04em;
}
.account-option {
    padding: 9px 12px; display: flex; align-items: center; gap: 10px;
    cursor: pointer; transition: background 0.1s;
    border-bottom: 0.5px solid #F1F5F9;
}
.account-option:last-child { border-bottom: none; }
.account-option:hover { background: #EAF3FC; }
.account-option.active { background: #F0FDF4; }
.account-option .opt-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: #25D366; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.account-option .opt-name  { font-weight: 600; font-size: 12px; color: #1E2A4A; }
.account-option .opt-phone { font-size: 11px; color: #64748B; }
.account-option .opt-status {
    margin-left: auto; font-size: 10px; font-weight: 600;
    text-transform: uppercase; padding: 2px 8px; border-radius: 6px;
}
.opt-status.active   { background: #DCFCE7; color: #166534; }
.opt-status.inactive { background: #FEECEC; color: #DC2626; }

/* 4. Stat cards — brand chip icon + compact */
.stat-cards {
    display: grid; grid-template-columns: repeat(4,1fr);
    gap: 9px; margin-bottom: 12px;
}
@media(max-width:768px){ .stat-cards { grid-template-columns: repeat(2,1fr); } }
.stat-card {
    background: #fff; border: 0.5px solid #E4EAF2;
    border-radius: 10px; padding: 11px 13px;
    box-shadow: 0 1px 3px rgba(16,42,74,0.06);
    transition: box-shadow 0.18s ease, transform 0.15s ease;
    display: flex; align-items: center; gap: 11px;
}
.stat-card:hover { box-shadow: 0 4px 12px rgba(16,42,74,0.10); transform: translateY(-1px); }
.stat-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.stat-val { font-size: 20px; font-weight: 700; color: #1E2A4A; line-height: 1; }
.stat-lbl { font-size: 10px; font-weight: 600; text-transform: uppercase;
            letter-spacing: .04em; color: #64748B; margin-top: 2px; }

/* 5. Table card */
.broadcast-card {
    border: 0.5px solid #E4EAF2 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(16,42,74,0.06) !important;
    overflow: hidden;
}
.broadcast-card .card-header {
    background: #F5F8FC !important;
    border-bottom: 0.5px solid #E4EAF2 !important;
    padding: 9px 14px !important;
}
#broadcastTable thead th {
    background: #F5F8FC; color: #64748B;
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .04em;
    border-bottom: 0.5px solid #E4EAF2;
    padding: 8px 10px; white-space: nowrap;
}
#broadcastTable tbody td {
    padding: 8px 10px; vertical-align: middle;
    border-bottom: 0.5px solid #F1F5F9; font-size: 13px;
}
#broadcastTable tbody tr:hover td { background: #F8FBFF; }

/* 6. Filter tab buttons */
.bc-filter-btn {
    font-size: 11px; padding: 4px 13px; border-radius: 7px;
    border: 0.5px solid #E4EAF2; background: #F1F5F9;
    color: #475569; cursor: pointer; transition: all 0.15s;
    font-weight: 500;
}
.bc-filter-btn:hover { background: #EAF3FC; color: #2E8DE1; border-color: #2E8DE1; }
.bc-filter-btn.active { background: #2E8DE1; color: #fff; border-color: #2E8DE1; }

/* 7. Action chips */
.bc-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 7px;
    border: 0.5px solid #E4EAF2; font-size: 13px;
    cursor: pointer; transition: all 0.15s; text-decoration: none;
}
.bc-action-edit   { background: #F2F8FE; color: #1B5FA6; }
.bc-action-edit:hover   { background: #EAF3FC; color: #1B5FA6; }
.bc-action-view   { background: #F0FDF4; color: #166534; }
.bc-action-view:hover   { background: #DCFCE7; color: #166534; }
.bc-action-delete { background: #FFF5F5; color: #DC2626; }
.bc-action-delete:hover { background: #FEECEC; color: #DC2626; }

/* misc */
.fw-600 { font-weight: 600; }
.fw-700 { font-weight: 700; }
.dt-loading { color: #2E8DE1; font-weight: 500; }
.badge.bg-success-transparent  { background: #DCFCE7 !important; color: #166534 !important; }
.badge.bg-warning-transparent  { background: #FEF3C7 !important; color: #854F0B !important; }
.badge.bg-danger-transparent   { background: #FEECEC !important; color: #DC2626 !important; }
.badge.bg-secondary-transparent{ background: #F1F5F9 !important; color: #475569 !important; }
</style>
</style>
@endsection

@section('button')
<div id="addBtnWrapper" class="btn-list">
    {{-- will be updated by JS based on selected meta --}}
</div>
@endsection

@section('content')

{{-- Page Hero — putih ringan, WA aksen chip hijau --}}
<div class="page-hero">
    <div class="bc-hero-left">
        <div class="bc-hero-icon"><i class="bx bxl-whatsapp"></i></div>
        <div>
            <div class="bc-hero-title">Broadcast WhatsApp Business API</div>
            <div class="bc-hero-sub">Kelola kampanye broadcast WABA Anda dari satu tempat</div>
        </div>
    </div>
    {{-- WABA Account Selector --}}
    <div style="position:relative;" id="accountSelectorWrapper">
        <div class="waba-account-selector" onclick="toggleDropdown()">
            <div class="waba-avatar"><i class="bx bxl-whatsapp"></i></div>
            <div>
                <div class="waba-account-name" id="selName">Pilih Akun WABA</div>
                <div class="waba-account-phone" id="selPhone">–</div>
            </div>
            <i class="bx bx-chevron-down chevron"></i>
        </div>
        <div class="account-dropdown" id="accountDropdown">
            <div class="account-dropdown-header">Pilih Nomor WABA</div>
            @forelse($wabaAccounts as $acc)
            <div class="account-option {{ $loop->first ? 'active' : '' }}"
                 onclick="selectAccount('{{ $acc->id }}','{{ addslashes($acc->name) }}')"
                 data-id="{{ $acc->id }}">
                <div class="opt-avatar"><i class="bx bxl-whatsapp"></i></div>
                <div>
                    <div class="opt-name">{{ $acc->name }}</div>
                    <div class="opt-phone">ID: {{ Str::limit($acc->id, 8) }}</div>
                </div>
                <span class="opt-status active">Aktif</span>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bx bx-info-circle fs-24"></i><br>
                Belum ada akun WABA terdaftar
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Stats Cards — 4 kolom, icon chip brand --}}
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon" style="background:#EAF3FC;color:#1B5FA6">
            <i class="bx bx-broadcast"></i></div>
        <div>
            <div class="stat-val" id="sumTotal">–</div>
            <div class="stat-lbl">Total Broadcast</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#DCFCE7;color:#166534">
            <i class="bx bx-check-circle"></i></div>
        <div>
            <div class="stat-val" id="sumDone">–</div>
            <div class="stat-lbl">Selesai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEF3C7;color:#854F0B">
            <i class="bx bxs-hourglass"></i></div>
        <div>
            <div class="stat-val" id="sumPending">–</div>
            <div class="stat-lbl">Menunggu</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#F1ECFE;color:#5B3FB0">
            <i class="bx bx-calendar"></i></div>
        <div>
            <div class="stat-val" id="sumMonth">–</div>
            <div class="stat-lbl">Bulan Ini</div>
        </div>
    </div>
</div>

{{-- Broadcast Table --}}
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card broadcast-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span class="fw-700" style="font-size:0.95rem;color:#1e293b;">
                        <i class="bx bx-list-ul me-2 text-success"></i>Riwayat Broadcast
                    </span>
                    <small class="text-muted ms-2">Terbaru di atas</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="bc-filter-btn active"
                            onclick="setFilter(null,this)">Semua</button>
                    <button class="bc-filter-btn"
                            onclick="setFilter('done',this)">✓ Selesai</button>
                    <button class="bc-filter-btn"
                            onclick="setFilter('pending',this)">⏳ Menunggu</button>
                </div>
            </div>
            <div class="card-body p-3">
                <table id="broadcastTable" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Nama Kampanye</th>
                            <th>Jadwal</th>
                            <th>Kategori</th>
                            <th>Template</th>
                            <th>Status Pengiriman</th>
                            <th style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
// Default accounts from PHP
const ACCOUNTS = @json($wabaAccounts);
const DEFAULT_META = @json($defaultMeta);

let currentMetaId   = localStorage.getItem('waba_selected_meta') || (DEFAULT_META ? DEFAULT_META.id : null);
let currentFilter   = null;
let dtTable         = null;

// Init DataTable
function initDataTable() {
    if (dtTable) { dtTable.destroy(); }
    dtTable = $('#broadcastTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [[2, 'desc']],
        language: {
            searchPlaceholder: 'Cari nama kampanye...',
            sSearch: '',
            sLengthMenu: 'Tampilkan _MENU_',
            sInfo: '_START_–_END_ dari _TOTAL_ broadcast',
            sInfoEmpty: '0 broadcast',
            sZeroRecords: '<div class="text-center py-4"><i class="bx bx-broadcast fs-48 text-muted"></i><br><p class="text-muted mt-2">Belum ada broadcast untuk akun ini</p></div>',
            sProcessing: '<div class="dt-loading"><i class="bx bx-loader-alt bx-spin me-2"></i>Memuat data...</div>',
            oPaginate: { sPrevious: '← Prev', sNext: 'Next →' }
        },
        pageLength: 10,
        ajax: {
            url: '{{ route("broadcast.waba.list") }}',
            data: function(d) {
                d.meta_id = currentMetaId;
                if (currentFilter) d.status_filter = currentFilter;
            },
            error: function(xhr) {
                console.error('DataTable AJAX error:', xhr.status, xhr.responseText);
            }
        },
        columns: [
            {
                data: null, orderable: false, searchable: false,
                render: function(data, type, row, meta) {
                    return '<span class="badge bg-light text-secondary rounded-pill">'
                         + (meta.settings._iDisplayStart + meta.row + 1) + '</span>';
                }
            },
            { data: 'title_col',     name: 'title_col',     orderable: true  },
            { data: 'schedule_col',  name: 'schedule_col',  orderable: true  },
            { data: 'category_name', name: 'category_name', orderable: false },
            { data: 'template_name', name: 'template_name', orderable: false },
            { data: 'stats_col',     name: 'stats_col',     orderable: false },
            { data: 'action_col',    name: 'action_col',    orderable: false },
        ],
        drawCallback: function(settings) {
            const json = this.api().ajax.json();
            if (json && json.recordsTotal !== undefined) {
                $('#sumTotal').text(parseInt(json.recordsTotal).toLocaleString('id-ID'));
            }
        }
    });
}

// Select account
function selectAccount(id, name) {
    currentMetaId = id;
    localStorage.setItem('waba_selected_meta', id);

    // Update header selector display
    $('#selName').text(name);
    $('#selPhone').text('');

    // Update active state in dropdown
    $('.account-option').removeClass('active');
    $(`.account-option[data-id="${id}"]`).addClass('active');

    // Update "Add Data" button
    updateAddButton(id);

    // Close dropdown & reload table
    $('#accountDropdown').removeClass('open');
    if (dtTable) {
        dtTable.ajax.reload();
    }

    // Reload stats
    loadStats();
}

function updateAddButton(metaId) {
    // Find the account name for URL
    fetch(`{{ url('/app/waba/broadcast/create') }}/${metaId}`, { method: 'HEAD' })
        .catch(() => {});
    $('#addBtnWrapper').html(
        `<a href="{{ url('/app/waba/broadcast/create') }}/${metaId}"
            class="btn btn-primary d-none d-sm-inline-block">
            <i class="bx bx-plus-circle me-1"></i>Buat Broadcast Baru
        </a>`
    );
}

function toggleDropdown() {
    $('#accountDropdown').toggleClass('open');
}

// Close dropdown on outside click
$(document).on('click', function(e) {
    if (!$('#accountSelectorWrapper').is(e.target) && $('#accountSelectorWrapper').has(e.target).length === 0) {
        $('#accountDropdown').removeClass('open');
    }
});

function setFilter(f, btn) {
    currentFilter = f;
    $('.bc-filter-btn').removeClass('active');
    $(btn).addClass('active');
    if (dtTable) dtTable.ajax.reload();
}

function loadStats() {
    if (!currentMetaId) return;

    // Done count (has recipients)
    $.get('{{ route("broadcast.waba.list") }}', { meta_id: currentMetaId, status_filter: 'done', length: 1, start: 0 }, function(d) {
        $('#sumDone').text(parseInt(d.recordsTotal || 0).toLocaleString('id-ID'));
    });
    // Pending count (no recipients yet)
    $.get('{{ route("broadcast.waba.list") }}', { meta_id: currentMetaId, status_filter: 'pending', length: 1, start: 0 }, function(d) {
        $('#sumPending').text(parseInt(d.recordsTotal || 0).toLocaleString('id-ID'));
    });
    // This month
    $.get('{{ route("broadcast.waba.list") }}', { meta_id: currentMetaId, month_filter: 1, length: 1, start: 0 }, function(d) {
        $('#sumMonth').text(parseInt(d.recordsTotal || 0).toLocaleString('id-ID'));
    });
}

// Init on page load
$(document).ready(function() {
    // Restore or use default meta
    const savedMeta = ACCOUNTS.find(a => a.id === currentMetaId) || ACCOUNTS[0];

    if (savedMeta) {
        $('#selName').text(savedMeta.name);
        $('#selPhone').text('');
        currentMetaId = savedMeta.id;
        updateAddButton(savedMeta.id);
    }

    initDataTable();
    loadStats();
});
</script>
@endsection
