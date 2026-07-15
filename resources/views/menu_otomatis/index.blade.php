@extends('layouts.app')

@section('button')
<div class="btn-list">
    <a href="{{ route('menu-otomatis.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle fs-16 me-1"></i> Buat Menu Otomatis
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        @if($flows->isEmpty())
        <div class="card custom-card">
            <div class="card-body text-center py-5">
                <i class="bx bx-sitemap fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Belum ada Menu Otomatis</h5>
                <p class="text-muted mb-4">Buat menu interaktif pertamamu untuk mengotomasi balasan WhatsApp</p>
                <a href="{{ route('menu-otomatis.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Buat Pertama
                </a>
            </div>
        </div>
        @else
        <div class="card custom-card">
            <div class="card-header">
                <h5 class="card-title">Daftar Menu Otomatis</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Menu</th>
                                <th>Pemicu</th>
                                <th>Langkah</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($flows as $flow)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $flow->name }}</div>
                                @if($flow->trigger_type === 'keyword' && $flow->trigger_keywords)
                                <small class="text-muted">{{ implode(', ', $flow->trigger_keywords) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($flow->trigger_type === 'default')
                                    <span class="badge bg-warning text-dark">Pesan apa saja</span>
                                @elseif($flow->trigger_type === 'keyword')
                                    @if(($flow->keyword_match ?? 'exact') === 'contains')
                                    <span class="badge bg-info">Mengandung kata</span>
                                    @else
                                    <span class="badge bg-primary">Sama persis</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">{{ $flow->trigger_type }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $flow->nodes_count }} langkah</span>
                            </td>
                            <td>
                                <button class="btn btn-sm {{ $flow->status === 'active' ? 'btn-success' : 'btn-secondary' }}"
                                    onclick="toggleFlow('{{ $flow->id }}', this)"
                                    data-status="{{ $flow->status }}">
                                    {{ $flow->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td><small>{{ $flow->created_at->format('d M Y') }}</small></td>
                            <td class="text-end">
                                <a href="{{ route('menu-otomatis.edit', $flow->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bx bx-edit"></i> Edit
                                </a>
                                <button class="btn btn-sm btn-outline-secondary me-1" onclick="duplicateFlow('{{ $flow->id }}')">
                                    <i class="bx bx-copy"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteFlow('{{ $flow->id }}', this)">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const _token = '{{ csrf_token() }}';

async function toggleFlow(id, btn) {
    const res = await fetch(`/app/auto-reply/menu-otomatis/${id}/toggle`, {
        method: 'POST', headers: {'X-CSRF-TOKEN': _token, 'Accept': 'application/json'}
    });
    const d = await res.json();
    if (d.status === 'ok') {
        btn.dataset.status = d.new_status;
        btn.className = 'btn btn-sm ' + (d.new_status === 'active' ? 'btn-success' : 'btn-secondary');
        btn.textContent = d.new_status === 'active' ? 'Aktif' : 'Nonaktif';
    }
}

async function deleteFlow(id, btn) {
    if (!confirm('Hapus menu otomatis ini? Semua langkah dan sesi aktif akan dihapus.')) return;
    const res = await fetch(`/app/auto-reply/menu-otomatis/${id}`, {
        method: 'DELETE', headers: {'X-CSRF-TOKEN': _token, 'Accept': 'application/json'}
    });
    if ((await res.json()).status === 'ok') btn.closest('tr').remove();
}

async function duplicateFlow(id) {
    const res = await fetch(`/app/auto-reply/menu-otomatis/${id}/duplicate`, {
        method: 'POST', headers: {'X-CSRF-TOKEN': _token, 'Accept': 'application/json'}
    });
    const d = await res.json();
    if (d.status === 'ok') { location.href = `/app/auto-reply/menu-otomatis/${d.id}/edit`; }
}
</script>
@endpush
