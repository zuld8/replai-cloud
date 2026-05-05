@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
/* =============================================
   WABA Broadcast - Main Menu Page
   ============================================= */
.page-hero {
    background: linear-gradient(135deg, #022c22 0%, #064e3b 40%, #065f46 75%, #047857 100%);
    border-radius: 18px;
    padding: 1.6rem 2rem;
    color: white !important;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    position: relative;
    overflow: visible;
    box-shadow: 0 6px 24px rgba(4, 120, 87, 0.3), 0 2px 8px rgba(0,0,0,0.15);
}
/* Decorative subtle radial glow — CSS only, zero perf cost */
.page-hero::before {
    content: '';
    position: absolute;
    top: -60%;
    right: -10%;
    width: 380px;
    height: 380px;
    background: radial-gradient(circle, rgba(52,211,153,0.15) 0%, transparent 65%);
    border-radius: 50%;
    pointer-events: none;
}
/* Subtle grid pattern overlay — pure CSS */
.page-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 32px 32px;
    border-radius: 18px;
    pointer-events: none;
}
.page-hero > * { position: relative; z-index: 1; }
.page-hero h2 {
    font-size: 1.45rem;
    font-weight: 800;
    margin: 0;
    color: #ffffff !important;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
    letter-spacing: -0.01em;
}
.page-hero h2 i { color: #6ee7b7 !important; margin-right: 0.5rem; }
.page-hero p  {
    margin: 0.3rem 0 0;
    opacity: 0.82;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.85) !important;
}

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
    background: #ffffff;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.04);
    border: 1px solid rgba(226,232,240,0.8);
    display: flex; align-items: center; gap: 0.875rem;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.stat-icon {
    width: 46px; height: 46px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.stat-val {
    font-size: 1.6rem; font-weight: 800; line-height: 1;
    background: linear-gradient(135deg, #1e293b, #374151);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.stat-lbl { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-top: 3px; }

/* Table */
.broadcast-card {
    border-radius: 18px; border: 1px solid rgba(226,232,240,0.6);
    box-shadow: 0 2px 12px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.03);
    overflow: hidden;
}
.broadcast-card .card-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
}
#broadcastTable thead th {
    background: #f8fafc; color: #475569; font-size: 0.72rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
    border-bottom: 2px solid #e2e8f0; padding: 0.9rem 1rem; white-space: nowrap;
}
#broadcastTable tbody td { padding: 0.85rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
#broadcastTable tbody tr:hover td { background: linear-gradient(90deg, #f0fdf4, #fafcff); }
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

@section('button')
<div id="addBtnWrapper" class="btn-list">
    {{-- will be updated by JS based on selected meta --}}
</div>
@endsection

@section('content')

{{-- Page Hero --}}
<div class="page-hero">
    <div>
        <h2><i class="bx bx-broadcast me-2"></i>Broadcast WhatsApp Business API</h2>
        <p>Kelola kampanye broadcast WABA Anda dari satu tempat</p>
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

{{-- Stats Cards --}}
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(30,41,59,0.08);color:#1e293b">
            <i class="bx bx-broadcast"></i></div>
        <div>
            <div class="stat-val" id="sumTotal">–</div>
            <div class="stat-lbl">Total Broadcast</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(5,150,105,0.1);color:#0EA5E9">
            <i class="bx bx-check-circle"></i></div>
        <div>
            <div class="stat-val" id="sumDone">–</div>
            <div class="stat-lbl">Selesai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#d97706">
            <i class="bx bxs-hourglass"></i></div>
        <div>
            <div class="stat-val" id="sumPending">–</div>
            <div class="stat-lbl">Menunggu</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(79,70,229,0.1);color:#4f46e5">
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
                    <button class="btn btn-sm btn-success rounded-pill active px-3 fw-600"
                            onclick="setFilter(null,this)">Semua</button>
                    <button class="btn btn-sm btn-light rounded-pill px-3"
                            onclick="setFilter('done',this)"
                            style="color:#0EA5E9">✅ Selesai</button>
                    <button class="btn btn-sm btn-light rounded-pill px-3"
                            onclick="setFilter('pending',this)"
                            style="color:#9ca3af">⏳ Menunggu</button>
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
    $('.btn[onclick*="setFilter"]').removeClass('active btn-success').addClass('btn-light').css('color','');
    $(btn).removeClass('btn-light').addClass('active btn-success').css('color','');
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
