@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
/* =============================================
   WABA Broadcast - Main Menu Page
   ============================================= */
.page-hero {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    color: white;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}
.page-hero h2 { font-size: 1.4rem; font-weight: 800; margin: 0; }
.page-hero p  { margin: 0.25rem 0 0; opacity: 0.8; font-size: 0.85rem; }

/* Account Selector */
.waba-account-selector {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 12px;
    padding: 0.6rem 1rem;
    cursor: pointer;
    transition: background 0.2s;
}
.waba-account-selector:hover { background: rgba(255,255,255,0.2); }
.waba-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, #34d399, #38BDF8);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; color: white; flex-shrink: 0;
}
.waba-account-name { font-weight: 700; font-size: 0.88rem; color: white; }
.waba-account-phone { font-size: 0.75rem; color: rgba(255,255,255,0.7); }
.waba-account-selector .chevron { color: rgba(255,255,255,0.7); font-size: 1rem; }

/* Account Dropdown */
.account-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 280px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb;
    z-index: 999;
    overflow: hidden;
    display: none;
}
.account-dropdown.open { display: block; animation: fadeIn 0.15s ease; }
@keyframes fadeIn { from { opacity:0; transform: translateY(-8px); } to { opacity:1; transform: translateY(0); } }
.account-dropdown-header {
    padding: 0.75rem 1rem;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 700;
    font-size: 0.78rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.account-option {
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: background 0.1s;
    border-bottom: 1px solid #f1f5f9;
}
.account-option:last-child { border-bottom: none; }
.account-option:hover { background: #f0fdf4; }
.account-option.active { background: #ecfdf5; }
.account-option .opt-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    background: linear-gradient(135deg, #34d399, #38BDF8);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1rem; flex-shrink: 0;
}
.account-option .opt-name { font-weight: 600; font-size: 0.88rem; color: #1e293b; }
.account-option .opt-phone { font-size: 0.75rem; color: #6b7280; }
.account-option .opt-status {
    margin-left: auto;
    font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
    padding: 2px 8px; border-radius: 99px;
}
.opt-status.active  { background: #dcfce7; color: #15803d; }
.opt-status.inactive{ background: #fee2e2; color: #dc2626; }

/* Stats cards */
.stat-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.25rem; }
@media(max-width:768px){ .stat-cards { grid-template-columns: repeat(2,1fr); } }
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    display: flex; align-items: center; gap: 0.875rem;
    transition: transform 0.15s;
}
.stat-card:hover { transform: translateY(-2px); }
.stat-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
    flex-shrink: 0;
}
.stat-val { font-size: 1.5rem; font-weight: 800; line-height: 1; }
.stat-lbl { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #9ca3af; }

/* Table */
.broadcast-card { border-radius: 16px; border: none; box-shadow: 0 2px 16px rgba(0,0,0,0.06); overflow: hidden; }
.broadcast-card .card-header { background: #f8fafc; border-bottom: 1px solid #e5e7eb; padding: 1rem 1.5rem; }
#broadcastTable thead th {
    background: #f8fafc; color: #374151; font-size: 0.73rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
    border-bottom: 2px solid #e5e7eb; padding: 0.85rem 1rem; white-space: nowrap;
}
#broadcastTable tbody td { padding: 0.85rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
#broadcastTable tbody tr:hover td { background: #fafcff; }
.badge.bg-success-transparent  { background: rgba(16,185,129,0.12) !important; }
.badge.bg-warning-transparent   { background: rgba(245,158,11,0.12) !important; }
.badge.bg-danger-transparent    { background: rgba(239,68,68,0.12) !important; }
.badge.bg-secondary-transparent { background: rgba(100,116,139,0.12) !important; }
.btn-icon.btn-sm { width: 32px; height: 32px; padding: 0; }
.fw-600 { font-weight: 600; }
.fw-700 { font-weight: 700; }
.fw-800 { font-weight: 800; }
.dt-loading { color: #0EA5E9; font-weight: 500; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-leftheader">
        <div class="page-title">Template WhatsApp Business API</div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Template WhatsApp Business API</li>
        </ol>
    </div>
</div>

{{-- Hero Header with account selector (same as broadcast/waba) --}}
<div class="waba-hero mb-4">
    <div class="waba-hero-left">
        <div class="waba-hero-icon"><i class="bx bx-layout"></i></div>
        <div>
            <div class="waba-hero-title">Template WhatsApp Business API</div>
            <div class="waba-hero-sub">Kelola template WABA Anda dari satu tempat</div>
        </div>
    </div>
    <div class="waba-hero-right">
        <div id="accountSelectorWrapper" class="account-selector-wrapper" style="position:relative;">
            <button class="account-selector-btn" onclick="toggleDropdown()">
                <span class="account-selector-icon"><i class="bx bxl-whatsapp"></i></span>
                <div class="account-selector-info">
                    <span id="selName" class="account-selector-name">Pilih Akun</span>
                    <span id="selPhone" class="account-selector-phone"></span>
                </div>
                <i class="bx bx-chevron-down account-selector-chevron"></i>
            </button>
            <div id="accountDropdown" class="account-dropdown">
                <div class="account-dropdown-header">PILIH NOMOR WABA</div>
                @foreach($wabaAccounts as $account)
                <div class="account-option {{ $loop->first ? 'active' : '' }}"
                     data-id="{{ $account->id }}"
                     data-name="{{ $account->name }}"
                     onclick="selectAccount('{{ $account->id }}', '{{ $account->name }}')">
                    <div class="account-option-icon"><i class="bx bxl-whatsapp"></i></div>
                    <div class="account-option-info">
                        <div class="account-option-name">{{ $account->name }}</div>
                        @if($account->phone)
                        <div class="account-option-phone">{{ $account->phone }}</div>
                        @endif
                        <div style="font-size:0.68rem;color:#9ca3af;">ID: {{ Str::limit($account->id, 8, '...') }}</div>
                    </div>
                    <span class="account-option-badge">AKTIF</span>
                </div>
                @endforeach
            </div>
        </div>
        <div id="addBtnWrapper">
            <a href="#" id="addBtn" class="btn btn-light d-none d-sm-inline-block">
                <i class="bx bx-plus-circle me-1"></i>Buat Template Baru
            </a>
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="stat-row mb-4">
    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:rgba(255,255,255,0.15)"><i class="bx bx-layout"></i></div>
        <div>
            <div class="stat-val" id="sumTotal">–</div>
            <div class="stat-lbl">Total Template</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:rgba(255,255,255,0.15)"><i class="bx bx-check-circle"></i></div>
        <div>
            <div class="stat-val" id="sumApproved">–</div>
            <div class="stat-lbl">Disetujui</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:rgba(255,255,255,0.15)"><i class="bx bx-time"></i></div>
        <div>
            <div class="stat-val" id="sumPending">–</div>
            <div class="stat-lbl">Menunggu</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap" style="background:rgba(255,255,255,0.15)"><i class="bx bx-calendar"></i></div>
        <div>
            <div class="stat-val" id="sumRejected">–</div>
            <div class="stat-lbl">Ditolak</div>
        </div>
    </div>
</div>

{{-- Template Table --}}
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card broadcast-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span class="fw-700" style="font-size:0.95rem;color:#1e293b;">
                        <i class="bx bx-layout me-2 text-success"></i>Daftar Template
                    </span>
                    <small class="text-muted ms-2">Terbaru di atas</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success rounded-pill active px-3 fw-600"
                            onclick="setFilter(null,this)">Semua</button>
                    <button class="btn btn-sm btn-light rounded-pill px-3"
                            onclick="setFilter('approved',this)"
                            style="color:#059669">✅ Disetujui</button>
                    <button class="btn btn-sm btn-light rounded-pill px-3"
                            onclick="setFilter('pending',this)"
                            style="color:#9ca3af">⏳ Menunggu</button>
                    <button class="btn btn-sm btn-light rounded-pill px-3"
                            onclick="setFilter('rejected',this)"
                            style="color:#dc2626">❌ Ditolak</button>
                </div>
            </div>
            <div class="card-body p-3">
                <table id="templateTable" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Nama Template</th>
                            <th>Kategori</th>
                            <th>Bahasa</th>
                            <th>Status</th>
                            <th>Kualitas</th>
                            <th style="width:120px">Aksi</th>
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
const ACCOUNTS    = @json($wabaAccounts);
const DEFAULT_META = @json($defaultMeta);

let currentMetaId = localStorage.getItem('template_waba_meta') || (DEFAULT_META ? DEFAULT_META.id : null);
let currentFilter = null;
let dtTable       = null;

function initDataTable() {
    if (dtTable) { dtTable.destroy(); dtTable = null; }
    dtTable = $('#templateTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [[1, 'asc']],
        language: {
            searchPlaceholder: 'Cari nama template...',
            sSearch: '',
            sLengthMenu: 'Tampilkan _MENU_',
            sInfo: '_START_–_END_ dari _TOTAL_ template',
            sInfoEmpty: '0 template',
            sZeroRecords: '<div class="text-center py-4"><i class="bx bx-layout fs-48 text-muted"></i><br><p class="text-muted mt-2">Belum ada template untuk akun ini</p></div>',
            sProcessing: '<div class="dt-loading"><i class="bx bx-loader-alt bx-spin me-2"></i>Memuat data...</div>',
            oPaginate: { sPrevious: '← Prev', sNext: 'Next →' }
        },
        pageLength: 25,
        ajax: {
            url: '{{ route("template.waba.list") }}',
            data: function(d) {
                d.meta_id = currentMetaId;
                if (currentFilter) d.status_filter = currentFilter;
            },
            error: function(xhr) { console.error('Template AJAX error:', xhr.status); }
        },
        columns: [
            {
                data: null, orderable: false, searchable: false,
                render: function(data, type, row, meta) {
                    return '<span class="badge bg-light text-secondary rounded-pill">' + (meta.settings._iDisplayStart + meta.row + 1) + '</span>';
                }
            },
            { data: 'name_col',   name: 't.name',                  orderable: true  },
            { data: 'category',   name: 't.category',               orderable: false },
            { data: 'lang',       name: 't.lang',                   orderable: false },
            { data: 'status_col', name: 't.waba_status_template',   orderable: false },
            { data: 'quality_col',name: 't.quality_score',          orderable: false },
            { data: 'action_col', name: 'action_col',               orderable: false },
        ],
        drawCallback: function(settings) {
            const json = this.api().ajax.json();
            if (json && json.recordsTotal !== undefined) {
                $('#sumTotal').text(parseInt(json.recordsTotal).toLocaleString('id-ID'));
            }
        }
    });
}

function selectAccount(id, name) {
    currentMetaId = id;
    localStorage.setItem('template_waba_meta', id);

    $('#selName').text(name);
    $('#selPhone').text('');

    $('.account-option').removeClass('active');
    $(`.account-option[data-id="${id}"]`).addClass('active');

    updateAddButton(id);
    $('#accountDropdown').removeClass('open');

    if (dtTable) { dtTable.ajax.reload(); }
    loadStats();
}

function updateAddButton(metaId) {
    $('#addBtnWrapper').html(
        `<a href="{{ url('/app/waba/templates/create') }}/${metaId}"
            id="addBtn"
            class="btn btn-light d-none d-sm-inline-block">
            <i class="bx bx-plus-circle me-1"></i>Buat Template Baru
        </a>`
    );
}

function toggleDropdown() {
    $('#accountDropdown').toggleClass('open');
}

$(document).on('click', function(e) {
    if (!$('#accountSelectorWrapper').is(e.target) && $('#accountSelectorWrapper').has(e.target).length === 0) {
        $('#accountDropdown').removeClass('open');
    }
});

function setFilter(f, btn) {
    currentFilter = f;
    $('.btn[onclick*="setFilter"]').removeClass('active btn-success').addClass('btn-light').css('color','');
    $(btn).removeClass('btn-light').addClass('active btn-success').css('color','');
    if (dtTable) dtTable.ajax.reload();
}

function loadStats() {
    if (!currentMetaId) return;
    $.get('{{ route("template.waba.list") }}', { meta_id: currentMetaId, length: 1, start: 0 }, function(d) {
        $('#sumTotal').text(parseInt(d.recordsTotal || 0).toLocaleString('id-ID'));
    });
    $.get('{{ route("template.waba.list") }}', { meta_id: currentMetaId, status_filter: 'approved', length: 1, start: 0 }, function(d) {
        $('#sumApproved').text(parseInt(d.recordsTotal || 0).toLocaleString('id-ID'));
    });
    $.get('{{ route("template.waba.list") }}', { meta_id: currentMetaId, status_filter: 'pending', length: 1, start: 0 }, function(d) {
        $('#sumPending').text(parseInt(d.recordsTotal || 0).toLocaleString('id-ID'));
    });
    $.get('{{ route("template.waba.list") }}', { meta_id: currentMetaId, status_filter: 'rejected', length: 1, start: 0 }, function(d) {
        $('#sumRejected').text(parseInt(d.recordsTotal || 0).toLocaleString('id-ID'));
    });
}

$(document).ready(function() {
    const savedMeta = ACCOUNTS.find(a => a.id === currentMetaId) || ACCOUNTS[0];
    if (savedMeta) {
        $('#selName').text(savedMeta.name);
        currentMetaId = savedMeta.id;
        updateAddButton(savedMeta.id);
    }
    initDataTable();
    loadStats();
});
</script>
@endsection
