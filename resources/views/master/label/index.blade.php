@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
<style>
.label-card { border-radius: 16px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden; }
.label-card .card-header { background: linear-gradient(135deg, #f0f9ff, #e0f2fe); border-bottom: 1px solid #bae6fd; padding: 1.2rem 1.5rem; }
.label-card .card-header h5 { font-weight: 700; color: #0c4a6e; margin: 0; font-size: 1.05rem; }
.label-card .card-header .subtitle { color: #64748b; font-size: 0.82rem; margin-top: 2px; }
.label-row { transition: all 0.2s ease; border-bottom: 1px solid #f1f5f9; }
.label-row:hover { background: #f8fafc; transform: translateX(2px); }
.label-row td { vertical-align: middle; padding: 0.85rem 1rem !important; }
.label-color-dot { width: 14px; height: 14px; border-radius: 50%; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.15); }
.label-name { font-weight: 600; color: #1e293b; font-size: 0.92rem; }
.label-type-badge { font-size: 0.7rem; padding: 3px 10px; border-radius: 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.contact-count { display: inline-flex; align-items: center; gap: 4px; background: linear-gradient(135deg, #dbeafe, #eff6ff); color: #1d4ed8; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; }
.contact-count.has-data { background: linear-gradient(135deg, #dcfce7, #f0fdf4); color: #15803d; }
.contact-count i { font-size: 1rem; }
.position-badge { background: linear-gradient(135deg, #e0e7ff, #eef2ff); color: #4338ca; font-weight: 700; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.82rem; }
.action-btn { width: 34px; height: 34px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; border: 1px solid; font-size: 14px; }
.action-btn.edit { color: #f59e0b; border-color: #fef3c7; background: #fffbeb; }
.action-btn.edit:hover { background: #f59e0b; color: #fff; }
.action-btn.delete { color: #ef4444; border-color: #fee2e2; background: #fef2f2; }
.action-btn.delete:hover { background: #ef4444; color: #fff; }
.summary-cards { display: flex; gap: 12px; margin-bottom: 16px; }
.summary-card { flex: 1; padding: 14px 18px; border-radius: 12px; text-align: center; }
.summary-card .number { font-size: 1.5rem; font-weight: 800; }
.summary-card .text { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }
</style>
@endsection

@section('button')
<div class="btn-list">
    <a href="{{ route('label.create') }}" class="btn btn-primary" style="border-radius:10px;font-weight:600;padding:8px 20px;">
        <i class="bx bx-plus-circle me-1"></i>
        {{ __('master.label.add_new_label') }}
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>

        {{-- Summary Cards --}}
        <div class="summary-cards">
            <div class="summary-card" style="background:linear-gradient(135deg,#dbeafe,#eff6ff);">
                <div class="number" style="color:#1d4ed8;">{{ $labels->count() }}</div>
                <div class="text" style="color:#3b82f6;">Total Label</div>
            </div>
            <div class="summary-card" style="background:linear-gradient(135deg,#dcfce7,#f0fdf4);">
                <div class="number" style="color:#15803d;">{{ $labels->sum('chat_count') }}</div>
                <div class="text" style="color:#22c55e;">Total Kontak</div>
            </div>
            <div class="summary-card" style="background:linear-gradient(135deg,#fef3c7,#fffbeb);">
                <div class="number" style="color:#b45309;">{{ $labels->where('chat_count', '>', 0)->count() }}</div>
                <div class="text" style="color:#f59e0b;">Label Aktif</div>
            </div>
        </div>

        <div class="card label-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5><i class="bx bx-purchase-tag me-2"></i>Daftar Label</h5>
                    <div class="subtitle">Kelola label untuk mengorganisir chat dan kontak</div>
                </div>
            </div>
            <div class="card-body p-0">
                <table id="labelTable" class="table mb-0" style="width:100%">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="width:60px;padding-left:1.2rem;font-weight:700;color:#64748b;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;">No.</th>
                            <th style="font-weight:700;color:#64748b;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;">Label</th>
                            <th style="width:100px;font-weight:700;color:#64748b;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;">Tipe</th>
                            <th style="width:100px;text-align:center;font-weight:700;color:#64748b;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;">Urutan</th>
                            <th style="width:140px;text-align:center;font-weight:700;color:#64748b;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;">Kontak</th>
                            <th style="width:120px;text-align:center;font-weight:700;color:#64748b;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($labels as $label)
                        <tr class="label-row">
                            <td style="padding-left:1.2rem;">
                                <span style="color:#94a3b8;font-weight:600;">{{ $no++ }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="label-color-dot" style="background-color: {{ $label->color ?? '#0EA5E9' }};"></span>
                                    <div>
                                        <div class="label-name">{{ $label->name }}</div>
                                        <small style="color:#94a3b8;font-size:0.75rem;">{{ $label->color ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(strtolower($label->type ?? '') === 'crm')
                                    <span class="label-type-badge" style="background:#dbeafe;color:#1d4ed8;">CRM</span>
                                @elseif(strtolower($label->type ?? '') === 'keyword')
                                    <span class="label-type-badge" style="background:#fef3c7;color:#b45309;">Keyword</span>
                                @else
                                    <span class="label-type-badge" style="background:#f1f5f9;color:#64748b;">{{ $label->type ?? '-' }}</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span class="position-badge">{{ $label->position ?? '-' }}</span>
                            </td>
                            <td style="text-align:center;">
                                @if(($label->chat_count ?? 0) > 0)
                                <span class="contact-count has-data"
                                      onclick="showLabelContacts('{{ $label->id }}', '{{ addslashes($label->name) }}', '{{ $label->color ?? '#0EA5E9' }}')"
                                      style="cursor:pointer;" title="Lihat daftar kontak">
                                    <i class="bx bx-group"></i>
                                    {{ number_format($label->chat_count ?? 0) }}
                                </span>
                                @else
                                <span class="contact-count">
                                    <i class="bx bx-user"></i> 0
                                </span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="{{ route('label.download', $label->id) }}" class="action-btn" title="Download Kontak" style="background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;" download>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('label.update', $label->id) }}" class="action-btn edit" title="Edit">
                                        <i class="bx bx-pencil"></i>
                                    </a>
                                    <a href="{{ route('label.delete', $label->id) }}" class="action-btn delete deletebutton" title="Hapus">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Contact List Modal --}}
<div id="contactModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <div class="d-flex align-items-center gap-3">
                    <span id="contactModalDot" style="width:14px;height:14px;border-radius:50%;display:inline-block;background:#0ea5e9;"></span>
                    <div>
                        <h5 class="modal-title mb-0" id="contactModalLabel">Kontak Label</h5>
                        <small id="contactModalCount" class="text-muted"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="contactModalBody" style="min-height:200px;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2 text-muted">Memuat data...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8fafc;">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a id="contactModalDownload" href="#" class="btn btn-sm btn-primary">
                    <i class="bx bx-download me-1"></i> Download CSV
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showLabelContacts(labelId, labelName, labelColor) {
    // Reset modal
    document.getElementById('contactModalLabel').textContent = labelName;
    document.getElementById('contactModalCount').textContent = '';
    document.getElementById('contactModalDot').style.background = labelColor;
    document.getElementById('contactModalDownload').href = '/app/master/labels/download/' + labelId;
    document.getElementById('contactModalBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2 text-muted">Memuat data...</div>
        </div>`;

    // Show modal - compatible with Bootstrap 4 and 5
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var modal = new bootstrap.Modal(document.getElementById('contactModal'));
        modal.show();
    } else if (typeof $ !== 'undefined') {
        $('#contactModal').modal('show');
    }

    // Fetch contacts
    fetch('/app/master/labels/contacts/' + labelId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('contactModalCount').textContent = data.total + ' kontak';

        if (!data.contacts || data.contacts.length === 0) {
            document.getElementById('contactModalBody').innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bx bx-user-x" style="font-size:2.5rem;"></i>
                    <div class="mt-2">Belum ada kontak</div>
                </div>`;
            return;
        }

        // Build table
        let rows = data.contacts.map((c, i) => `
            <tr>
                <td style="color:#94a3b8;font-weight:600;width:45px;padding-left:1rem;">${i+1}</td>
                <td>
                    <div style="font-weight:600;color:#1e293b;">${escHtml(c.name)}</div>
                </td>
                <td>
                    <span style="font-family:monospace;color:#0ea5e9;font-size:0.92rem;">${escHtml(c.phone)}</span>
                </td>
                <td>
                    <span style="font-size:0.75rem;padding:2px 8px;border-radius:20px;background:#f0fdf4;color:#15803d;font-weight:600;">${escHtml(c.channel)}</span>
                </td>
                <td style="color:#94a3b8;font-size:0.83rem;">${escHtml(c.created_at)}</td>
            </tr>`).join('');

        document.getElementById('contactModalBody').innerHTML = `
            <div style="padding:0.75rem 1rem 0.25rem;border-bottom:1px solid #f1f5f9;">
                <input type="text" id="contactSearch" placeholder="Cari nama / nomor..."
                    class="form-control form-control-sm" style="max-width:300px;"
                    oninput="filterContacts(this.value)">
            </div>
            <div style="overflow-x:auto;">
                <table class="table table-hover mb-0" id="contactListTable">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="width:45px;padding-left:1rem;color:#64748b;font-size:0.77rem;font-weight:700;text-transform:uppercase;">No</th>
                            <th style="color:#64748b;font-size:0.77rem;font-weight:700;text-transform:uppercase;">Nama</th>
                            <th style="color:#64748b;font-size:0.77rem;font-weight:700;text-transform:uppercase;">Nomor</th>
                            <th style="color:#64748b;font-size:0.77rem;font-weight:700;text-transform:uppercase;">Channel</th>
                            <th style="color:#64748b;font-size:0.77rem;font-weight:700;text-transform:uppercase;">Tgl Masuk</th>
                        </tr>
                    </thead>
                    <tbody id="contactListBody">${rows}</tbody>
                </table>
            </div>`;
    })
    .catch(err => {
        document.getElementById('contactModalBody').innerHTML = `
            <div class="text-center py-5 text-danger">
                <i class="bx bx-error" style="font-size:2rem;"></i>
                <div class="mt-2">Gagal memuat data kontak</div>
            </div>`;
    });
}

function escHtml(str) {
    if (!str) return '-';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function filterContacts(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#contactListBody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>

@endsection

