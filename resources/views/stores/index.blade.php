@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">

<style>
    .wa-link {
        color: #03a9f4;
        text-decoration: underline;
    }
</style>
@endsection

@section('button')
<div class="btn-list">
    <span class="d-none d-sm-inline">
        <a href="{{route('stores.export')}}?<?= $params; ?>" target="_blank" class="btn btn-dark">
            <i class="ti ti-download me-1"></i> {{__('general.export_data')}}
        </a>
    </span>
    <span class="d-none d-sm-inline">
        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalImport" onclick="openImportModal(event)" class="btn btn-dark">
            <i class="ti ti-upload me-1"></i> {{__('general.import')}}
        </a>
    </span>
    <a href="{{ route('store.kanban') }}" class="btn btn-dark">
        <i class="bx bx-list-ol"></i>
        {{__('contact.kanban_view')}}
    </a>
    <a href="{{ route('stores.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle"></i>
        {{__('general.add_data')}}
    </a>
 

</div>

@endsection

@section('content')


<!-- List Data -->
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    {{$page}}
                </div>
            </div>
            <div class="card-body table-responsive">
                <input type="hidden" id="paramsData" value="<?= $params; ?>" />

                <!-- Select banner + toolbar -->
                <div id="selectBanner" style="display:none; background:#e8f4fd; border:1px solid #b8dffa; border-radius:8px; padding:10px 16px; margin-bottom:8px; align-items:center; gap:12px; flex-wrap:wrap;">
                    <span style="font-size:13px; color:#333;"><strong id="selectedCount">0</strong> data terpilih di halaman ini.</span>
                    <button id="btnSelectAll" onclick="selectAllRecords()" style="background:none;border:none;color:#007bff;font-size:13px;font-weight:600;cursor:pointer;text-decoration:underline;">Pilih semua <span id="totalCountLabel">0</span> data</button>
                    <span id="allSelectedMsg" style="display:none;color:#1a7a45;font-weight:600;font-size:13px;">✅ Semua <span id="totalCountAll">0</span> data terpilih</span>
                    <button onclick="clearAllSelection()" style="background:none;border:none;color:#dc3545;font-size:12px;cursor:pointer;margin-left:auto;">✕ Batalkan</button>
                </div>
                <div id="bulkActionBar" style="display:none; background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:8px 14px; margin-bottom:8px; gap:8px; align-items:center; flex-wrap:wrap;">
                    <span style="font-size:12px;color:#856404;font-weight:600;">Aksi:</span>
                    <button onclick="deleteSelected()" class="btn btn-sm btn-danger" style="font-size:12px;"><i class="ti ti-trash"></i> Hapus</button>
                    <button onclick="exportSelected()" class="btn btn-sm btn-success" style="font-size:12px;"><i class="ti ti-download"></i> Export</button>
                </div>

                
                <!-- Filter by WA Account -->
                <div class="mb-3 waba-filter-bar">
                    <form method="GET" action="<?= route('stores'); ?>" class="d-flex align-items-center gap-2 flex-wrap">
                        <label class="fw-semibold mb-0 text-muted" style="font-size:13px; white-space:nowrap;">
                            <i class="bx bxl-whatsapp text-success me-1"></i> Filter WA:
                        </label>
                        <select name="meta_account_id" class="form-select form-select-sm"
                            style="max-width:280px;" onchange="this.form.submit()">
                            <option value="">-- Semua WA --</option>
                            @foreach($wabaAccounts as $wa)
                            <option value="{{ $wa['id'] }}" {{ request('meta_account_id') == $wa['id'] ? 'selected' : '' }}>
                                @if($wa['type'] === 'personal')[WA Personal] {{ $wa['name'] }}
                                @else[WABA] {{ $wa['name'] }}
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @if(request('meta_account_id'))
                        <a href="<?= route('stores'); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-x"></i> Reset
                        </a>
                        @endif
                        <input type="hidden" id="currentMetaAccountId"
                            value="<?= request('meta_account_id', ''); ?>">
                    </form>
                </div>

<table id="storeData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:40px; text-align:center;">
                                <input type="checkbox" id="checkAll" title="Pilih semua di halaman ini"
                                    style="width:16px; height:16px; cursor:pointer; accent-color:#00ceec;">
                            </th>
                            <th>{{__('general.name')}}</th>
                            <th>{{__('general.telp')}}</th>
                            <th>{{__('sidebar.category')}}</th>
                            <th>{{__('contact.handle_by')}}</th>
                            <th>{{__('contact.label')}}</th>
                            <th>{{__('contact.source')}}</th>
                            <th>{{__('general.action')}}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import — Modern Drag & Drop -->
<div class="modal fade" id="modalImport" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <form action="<?= route('stores.import'); ?>" enctype="multipart/form-data" method="post"
            class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            @csrf
            <input type="hidden" name="meta_account_id" id="importMetaAccountId" value="">

            <!-- Header -->
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#25d366,#128c7e);display:flex;align-items:center;justify-content:center;">
                        <i class="bx bxl-whatsapp text-white fs-18"></i>
                    </div>
                    <h6 class="modal-title fw-bold mb-0" style="font-size:15px;">Import Kontak</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- WA Account Badge -->
            <div class="px-4 pt-2 pb-0">
                <div id="importWaInfo" style="display:none; background:linear-gradient(135deg,#e8f5e9,#f0fdf4); border:1px solid #86efac; border-radius:10px; padding:8px 14px; font-size:13px;">
                    <i class="bx bxl-whatsapp text-success me-1"></i>
                    Import ke: <strong id="importWaName" class="text-success">-</strong>
                </div>
                <div id="importWaWarning" style="display:none; background:linear-gradient(135deg,#fff7ed,#fffbeb); border:1px solid #fcd34d; border-radius:10px; padding:10px 14px; font-size:13px;">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:22px;">⚠️</span>
                        <div>
                            <div class="fw-semibold" style="color:#92400e;">Pilih Akun WhatsApp Dulu!</div>
                            <div style="color:#b45309; font-size:12px;">Gunakan filter WA di halaman kontak sebelum mengimpor data.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-4 py-3">
                <!-- Drag & Drop Zone -->
                <div id="dropZone"
                    style="border: 2px dashed #cbd5e1; border-radius: 14px; padding: 32px 20px;
                           text-align: center; cursor: pointer; transition: all 0.25s ease;
                           background: #f8fafc; position: relative;"
                    onclick="openFileDialog()"
                    ondragover="handleDragOver(event)"
                    ondragleave="handleDragLeave(event)"
                    ondrop="handleDrop(event)">

                    <!-- Default state -->
                    <div id="dropZoneDefault">
                        <div style="width:56px;height:56px;border-radius:14px;background:#e0f2fe;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
                            <i class="bx bx-cloud-upload" style="font-size:28px;color:#0284c7;"></i>
                        </div>
                        <div class="fw-semibold" style="font-size:14px;color:#334155;">Drag & drop file di sini</div>
                        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">atau <span style="color:#0284c7;text-decoration:underline;">klik untuk pilih file</span></div>
                        <div style="margin-top:10px;display:inline-block;background:#f1f5f9;border-radius:6px;padding:3px 10px;font-size:11px;color:#64748b;">
                            .xlsx, .xls, .csv
                        </div>
                    </div>

                    <!-- File selected state -->
                    <div id="dropZoneSelected" style="display:none;">
                        <div style="width:56px;height:56px;border-radius:14px;background:#dcfce7;margin:0 auto 10px;display:flex;align-items:center;justify-content:center;">
                            <i class="bx bx-file" style="font-size:28px;color:#16a34a;"></i>
                        </div>
                        <div class="fw-semibold" id="selectedFileName" style="font-size:14px;color:#166534;word-break:break-all;"></div>
                        <div id="selectedFileSize" style="font-size:12px;color:#4ade80;margin-top:3px;"></div>
                        <div style="margin-top:10px;">
                            <span style="font-size:11px;color:#94a3b8;">Klik untuk ganti file</span>
                        </div>
                    </div>

                    <!-- Real file input (hidden) -->
                    <input type="file" name="file" id="importFileInput"
                        accept=".xlsx,.xls,.csv"
                        style="display:none;"
                        onchange="handleFileSelect(this)"
                        required>
                </div>

                <!-- Tips -->
                <div style="margin-top:12px;font-size:11px;color:#94a3b8;text-align:center;">
                    💡 Gunakan template sample untuk format yang benar
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 pt-0 px-4 pb-4 d-flex justify-content-between align-items-center">
                <a href="{{asset('assets/xlsx/import_sample.xlsx')}}"
                    class="btn btn-sm" download
                    style="background:#f1f5f9;color:#475569;border-radius:8px;font-size:12px;border:none;padding:7px 14px;">
                    <i class="bx bx-download me-1"></i>Template Sample
                </a>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal"
                        style="border-radius:8px;font-size:13px;padding:7px 16px;">Batal</button>
                    <button type="submit" id="importSubmitBtn"
                        class="btn btn-sm"
                        style="border-radius:8px;font-size:13px;padding:7px 18px;
                               background:linear-gradient(135deg,#25d366,#128c7e);color:white;border:none;
                               transition: all 0.2s;">
                        <i class="bx bx-import me-1"></i>Import Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
#dropZone:hover    { border-color: #0284c7; background: #f0f9ff; }
#dropZone.dragging { border-color: #0284c7; background: #e0f2fe; transform: scale(1.01); }
#dropZone.has-file { border-color: #16a34a; border-style: solid; background: #f0fdf4; }
#importSubmitBtn:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,211,102,0.4); }
#importSubmitBtn:disabled { opacity: 0.45; cursor: not-allowed; }
</style>

@php
$categories = \App\Models\Master\Category::select('id', 'name')->orderBy('name')->get();
@endphp


  
@endsection

@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>

<script>
    var store_table; // declared globally so event handlers outside .ready() can access it
    $(document).ready(function() {
        store_table = $('#storeData').DataTable({
            responsive: false,
            language: {
                searchPlaceholder: '{{__("contact.search")}}',
                sSearch: '',
            },
            pageLength: 25,
            processing: true,
            serverSide: true,
            aaSorting: [
                [1, 'asc']
            ],
            ajax: {
                url: '/app/stores',
                type: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                data: function(d) {
                    d = datatable_pasarsafe_callback(d);
                    // Forward ALL URL params to AJAX (category, name, district, status, etc.)
                    // So clicking "1 Merchant" on category page filters correctly
                    var urlParams = new URLSearchParams(window.location.search);
                    urlParams.forEach(function(val, key) {
                        if (key !== 'meta_account_id' && key !== 'draw') {
                            d[key] = val;
                        }
                    });
                    // meta_account_id handled via WA filter dropdown
                    d.meta_account_id = $("#currentMetaAccountId").val();
                }
            },
            columnDefs: [
                {
                    targets: [0],
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                },
                {
                    targets: [2],
                    orderable: false,
                    searchable: true,
                }
            ],
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="row-check" value="${row.id}"
                            style="width:16px; height:16px; cursor:pointer; accent-color:#00ceec;"
                            onclick="updateSelection()">`;
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone',
                    render: function(data, type, row) {
                        if (!data) return '';
                        const phone = data.replace(/\D/g, '');
                        return `<a href="https://wa.me/${phone}" class="wa-link" target="_blank">${data}</a>`;
                    }
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'handled',
                    name: 'handled'
                },
                {
                    data: 'lables',
                    name: 'lables'
                },
                {
                    data: 'sumber',
                    name: 'sumber'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    });

    // ===================================
    // SELECT ALL LOGIC (no-lag approach)
    // ===================================
    let selectedIds    = new Set();
    let selectAllMode  = false;  // true = all records in DB
    let totalRecords   = 0;

    // When DataTable redraws, restore checkboxes
    store_table.on('draw', function() {
        totalRecords = store_table.page.info().recordsTotal;
        $('#totalCountLabel').text(totalRecords.toLocaleString('id-ID'));
        $('#totalCountAll').text(totalRecords.toLocaleString('id-ID'));

        // Restore checked state for current page rows
        store_table.rows({page: 'current'}).every(function() {
            const id = this.data()?.id;
            if (id && selectedIds.has(id)) {
                $(this.node()).find('.row-check').prop('checked', true);
            }
        });

        updateHeaderCheckbox();
        updateBannerAndToolbar();

        // Show active URL filter badges on first draw
        showActiveFilterBadges();
    });

    function showActiveFilterBadges() {
        var urlParams = new URLSearchParams(window.location.search);
        var labels = {
            'category': 'Kategori',
            'district': 'Kecamatan',
            'status'  : 'Status',
            'name'    : 'Nama'
        };
        var badges = [];
        urlParams.forEach(function(val, key) {
            if (labels[key] && val) {
                badges.push('<span class="badge bg-info me-1">' + labels[key] + ': ' + val.substring(0,8) + '...</span>');
            }
        });
        if (badges.length > 0) {
            $('#activeFilterBadges').show();
            $('#filterBadgeList').html(badges.join(''));
        }
    }

    // Check-all checkbox in header
    $('#checkAll').on('change', function() {
        const checked = $(this).prop('checked');
        store_table.rows({page: 'current'}).every(function() {
            const id = this.data()?.id;
            if (!id) return;
            if (checked) selectedIds.add(id);
            else selectedIds.delete(id);
            $(this.node()).find('.row-check').prop('checked', checked);
        });
        if (!checked) selectAllMode = false;
        updateBannerAndToolbar();
    });

    function updateSelection() {
        // A row checkbox was clicked manually
        selectAllMode = false;
        store_table.rows({page: 'current'}).every(function() {
            const id = this.data()?.id;
            if (!id) return;
            const checked = $(this.node()).find('.row-check').prop('checked');
            if (checked) selectedIds.add(id);
            else selectedIds.delete(id);
        });
        updateHeaderCheckbox();
        updateBannerAndToolbar();
    }

    function updateHeaderCheckbox() {
        const pageIds = [];
        store_table.rows({page: 'current'}).every(function() {
            const id = this.data()?.id;
            if (id) pageIds.push(id);
        });
        const allChecked = pageIds.length > 0 && pageIds.every(id => selectedIds.has(id));
        $('#checkAll').prop('checked', allChecked);
        $('#checkAll').prop('indeterminate', !allChecked && pageIds.some(id => selectedIds.has(id)));
    }

    function updateBannerAndToolbar() {
        const count = selectAllMode ? totalRecords : selectedIds.size;
        $('#selectedCount').text(count.toLocaleString('id-ID'));

        if (count > 0) {
            $('#selectBanner').css('display', 'flex');
            $('#bulkActionBar').css('display', 'flex');
        } else {
            $('#selectBanner').hide();
            $('#bulkActionBar').hide();
        }

        // Show "select all X records" only if current page all checked and not yet in select-all mode
        const pageSize = store_table.rows({page: 'current'}).count();
        const allPageChecked = pageSize > 0 && store_table.rows({page: 'current'}).every(function() {
            return selectedIds.has(this.data()?.id);
        });
        if (allPageChecked && !selectAllMode && totalRecords > selectedIds.size) {
            $('#btnSelectAll').show();
            $('#allSelectedMsg').hide();
        } else if (selectAllMode) {
            $('#btnSelectAll').hide();
            $('#allSelectedMsg').show();
        } else {
            $('#btnSelectAll').hide();
            $('#allSelectedMsg').hide();
        }
    }

    function selectAllRecords() {
        selectAllMode = true;
        // Also select current page IDs for display
        store_table.rows({page: 'current'}).every(function() {
            const id = this.data()?.id;
            if (id) selectedIds.add(id);
        });
        updateBannerAndToolbar();
    }

    function clearAllSelection() {
        selectedIds.clear();
        selectAllMode = false;
        $('#checkAll').prop('checked', false).prop('indeterminate', false);
        $('.row-check').prop('checked', false);
        $('#selectBanner').hide();
        $('#bulkActionBar').hide();
    }

    function deleteSelected() {
        const count = selectAllMode ? totalRecords : selectedIds.size;
        if (count === 0) { alert('Pilih minimal 1 data'); return; }

        if (!confirm(`⚠️ Hapus ${count.toLocaleString('id-ID')} data kontak? Tindakan ini tidak bisa dibatalkan.`)) return;

        const payload = selectAllMode
            ? { select_all: true }
            : { ids: Array.from(selectedIds) };

        $.ajax({
            url: '{{ route("stores.deleteMultiple") }}',
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    clearAllSelection();
                    store_table.ajax.reload(null, false);
                    toastr?.success(res.message) || alert(res.message);
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Gagal menghapus'));
            }
        });
    }

    function exportSelected() {
        const count = selectAllMode ? totalRecords : selectedIds.size;
        if (count === 0) { alert('Pilih minimal 1 data'); return; }

        const params = new URLSearchParams($('#paramsData').val());
        if (selectAllMode) {
            params.set('select_all', '1');
        } else {
            Array.from(selectedIds).forEach(id => params.append('ids[]', id));
        }
        window.open('{{ route("stores.export") }}?' + params.toString(), '_blank');
    }

    // ── Import Modal — Drag & Drop + Validation ─────────────
    function openImportModal(e) {
        const metaId = document.getElementById('currentMetaAccountId')?.value || '';
        const waSelect = document.querySelector('select[name="meta_account_id"]');
        const waName = waSelect ? waSelect.options[waSelect.selectedIndex]?.text : '';

        document.getElementById('importMetaAccountId').value = metaId;

        const infoEl = document.getElementById('importWaInfo');
        const warnEl = document.getElementById('importWaWarning');
        const submitBtn = document.getElementById('importSubmitBtn');
        const dropZone = document.getElementById('dropZone');

        if (metaId) {
            if (infoEl) { infoEl.style.display = 'block'; }
            if (warnEl) { warnEl.style.display = 'none'; }
            document.getElementById('importWaName').textContent = waName;
            if (dropZone) { dropZone.style.opacity = '1'; dropZone.style.pointerEvents = 'auto'; }
            if (submitBtn) submitBtn.disabled = false;
        } else {
            if (infoEl) { infoEl.style.display = 'none'; }
            if (warnEl) { warnEl.style.display = 'block'; }
            if (dropZone) { dropZone.style.opacity = '0.4'; dropZone.style.pointerEvents = 'none'; }
            if (submitBtn) submitBtn.disabled = true;
            // Highlight WA filter
            const filterBar = document.querySelector('.waba-filter-bar select');
            if (filterBar) {
                filterBar.style.outline = '2px solid #ef4444';
                filterBar.style.borderRadius = '6px';
                filterBar.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => { filterBar.style.outline = ''; }, 2500);
            }
        }
        // Reset file input
        resetDropZone();
    }

    function resetDropZone() {
        const input = document.getElementById('importFileInput');
        if (input) input.value = '';
        const dz = document.getElementById('dropZone');
        if (dz) { dz.classList.remove('has-file', 'dragging'); }
        document.getElementById('dropZoneDefault').style.display = 'block';
        document.getElementById('dropZoneSelected').style.display = 'none';
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            showFileSelected(file);
        }
    }

    function showFileSelected(file) {
        const dz = document.getElementById('dropZone');
        dz.classList.add('has-file');
        document.getElementById('dropZoneDefault').style.display = 'none';
        document.getElementById('dropZoneSelected').style.display = 'block';
        document.getElementById('selectedFileName').textContent = file.name;
        const kb = (file.size / 1024).toFixed(1);
        const mb = (file.size / 1024 / 1024).toFixed(2);
        document.getElementById('selectedFileSize').textContent = file.size > 1024*1024 ? mb + ' MB' : kb + ' KB';
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.classList.add('dragging');
    }

    function handleDragLeave(e) {
        e.currentTarget.classList.remove('dragging');
    }

    function handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('dragging');
        const metaId = document.getElementById('importMetaAccountId')?.value || '';
        if (!metaId) return; // locked
        const file = e.dataTransfer.files[0];
        if (!file) return;
        const allowed = ['xlsx','xls','csv'];
        const ext = file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) {
            alert('Format file harus .xlsx, .xls, atau .csv');
            return;
        }
        // Inject file into input
        const input = document.getElementById('importFileInput');
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        showFileSelected(file);
    }

</script>

<script>
/* ══ Import Modal — Global drag & drop functions ══════════════ */

function openImportModal(e) {
    const metaId = (document.getElementById('currentMetaAccountId') || {}).value || '';
    const waSelect = document.querySelector('select[name="meta_account_id"]');
    const waName = waSelect ? waSelect.options[waSelect.selectedIndex].text : '';

    const importIdEl = document.getElementById('importMetaAccountId');
    if (importIdEl) importIdEl.value = metaId;

    const infoEl    = document.getElementById('importWaInfo');
    const warnEl    = document.getElementById('importWaWarning');
    const submitBtn = document.getElementById('importSubmitBtn');
    const dropZone  = document.getElementById('dropZone');

    if (metaId) {
        if (infoEl)    { infoEl.style.display = 'block'; }
        if (warnEl)    { warnEl.style.display = 'none'; }
        const nameEl = document.getElementById('importWaName');
        if (nameEl)    nameEl.textContent = waName;
        if (dropZone)  { dropZone.style.opacity = '1'; dropZone.style.pointerEvents = 'auto'; }
        if (submitBtn) submitBtn.disabled = false;
    } else {
        if (infoEl)    { infoEl.style.display = 'none'; }
        if (warnEl)    { warnEl.style.display = 'block'; }
        if (dropZone)  { dropZone.style.opacity = '0.4'; dropZone.style.pointerEvents = 'none'; }
        if (submitBtn) submitBtn.disabled = true;
        const filterBar = document.querySelector('.waba-filter-bar select');
        if (filterBar) {
            filterBar.style.outline = '2px solid #ef4444';
            filterBar.style.borderRadius = '6px';
            filterBar.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(function() { filterBar.style.outline = ''; }, 2500);
        }
    }
    resetDropZone();
}

function resetDropZone() {
    const input = document.getElementById('importFileInput');
    if (input) input.value = '';
    const dz = document.getElementById('dropZone');
    if (dz) { dz.classList.remove('has-file', 'dragging'); }
    const defaultEl   = document.getElementById('dropZoneDefault');
    const selectedEl  = document.getElementById('dropZoneSelected');
    if (defaultEl)  defaultEl.style.display = 'block';
    if (selectedEl) selectedEl.style.display = 'none';
}

function handleFileSelect(input) {
    if (input.files && input.files[0]) {
        showFileSelected(input.files[0]);
    }
}

function showFileSelected(file) {
    const dz = document.getElementById('dropZone');
    if (dz) dz.classList.add('has-file');
    const defaultEl  = document.getElementById('dropZoneDefault');
    const selectedEl = document.getElementById('dropZoneSelected');
    const nameEl     = document.getElementById('selectedFileName');
    const sizeEl     = document.getElementById('selectedFileSize');
    if (defaultEl)  defaultEl.style.display  = 'none';
    if (selectedEl) selectedEl.style.display = 'block';
    if (nameEl)     nameEl.textContent = file.name;
    if (sizeEl) {
        const kb = (file.size / 1024).toFixed(1);
        const mb = (file.size / 1048576).toFixed(2);
        sizeEl.textContent = file.size > 1048576 ? mb + ' MB' : kb + ' KB';
    }
}

function handleDragOver(e) {
    e.preventDefault();
    const metaId = (document.getElementById('importMetaAccountId') || {}).value || '';
    if (!metaId) return;
    e.currentTarget.classList.add('dragging');
}

function handleDragLeave(e) {
    e.currentTarget.classList.remove('dragging');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragging');
    const metaId = (document.getElementById('importMetaAccountId') || {}).value || '';
    if (!metaId) return;
    const file = e.dataTransfer.files[0];
    if (!file) return;
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['xlsx','xls','csv'].includes(ext)) {
        alert('Format file harus .xlsx, .xls, atau .csv');
        return;
    }
    try {
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('importFileInput').files = dt.files;
    } catch(err) { /* Safari fallback - no preview */ }
    showFileSelected(file);
}

function openFileDialog() {
    const metaId = (document.getElementById('importMetaAccountId') || {}).value || '';
    if (!metaId) return;
    document.getElementById('importFileInput').click();
}
</script>

@endsection