@extends('layouts.app')

@section('button')
<div class="btn-list">
    <a href="{{ route('flow.create') }}" class="btn btn-primary">
        <i class="bx bx-plus-circle fs-16 me-1"></i> Tambah Flow
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <x-validation-component></x-validation-component>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Filter Tabs --}}
        <ul class="nav nav-tabs mb-3" id="flowTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-filter="all">Semua ({{ $flows->count() }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-filter="payment">💳 Payment Flow</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-filter="form">📋 Form Flow</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-filter="order">🛒 Order Flow</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-filter="booking">📅 Booking Flow</a>
            </li>
        </ul>

        @if($flows->isEmpty())
        <div class="card custom-card">
            <div class="card-body text-center py-5">
                <i class="bx bx-transfer-alt fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Belum ada Flow</h5>
                <p class="text-muted mb-4">Buat Flow pertamamu untuk mengotomasi balasan WhatsApp</p>
                <a href="{{ route('flow.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Buat Flow Pertama
                </a>
            </div>
        </div>
        @else
        <div class="row g-3" id="flowCardGrid">
            @foreach($flows as $flow)
            @php
                $badgeClass = match($flow->flow_type) {
                    'payment' => 'bg-success',
                    'form'    => 'bg-warning text-dark',
                    'order'   => 'bg-primary',
                    'booking' => 'bg-info',
                    default   => 'bg-secondary',
                };
                $typeIcon = match($flow->flow_type) {
                    'payment' => '💳',
                    'form'    => '📋',
                    'order'   => '🛒',
                    'booking' => '📅',
                    default   => '⚡',
                };
                $typeLabel = match($flow->flow_type) {
                    'payment' => 'Payment Flow',
                    'form'    => 'Form Flow',
                    'order'   => 'Order Flow',
                    'booking' => 'Booking Flow',
                    default   => 'Flow',
                };
            @endphp
            <div class="col-md-4 flow-card" data-type="{{ $flow->flow_type }}">
                <div class="card custom-card h-100 {{ $flow->status === 'inactive' ? 'opacity-75' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge {{ $badgeClass }} fs-12">{{ $typeIcon }} {{ $typeLabel }}</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input flow-toggle" type="checkbox"
                                    data-id="{{ $flow->id }}"
                                    {{ $flow->status === 'active' ? 'checked' : '' }}>
                            </div>
                        </div>
                        <h6 class="fw-semibold mt-2 mb-1">{{ $flow->name }}</h6>
                        <div class="mb-2">
                            @foreach(explode(',', $flow->keyword) as $kw)
                                <span class="badge bg-light text-dark border me-1 mb-1">{{ trim($kw) }}</span>
                            @endforeach
                        </div>
                        <small class="text-muted">
                            <i class="bx bx-calendar me-1"></i>{{ $flow->created_at?->format('d M Y') }}
                        </small>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 d-flex gap-2">
                        <a href="{{ route('flow.update', $flow->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bx bx-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('flow.delete', $flow->id) }}" method="POST"
                              onsubmit="return confirm('Hapus flow ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// Filter tabs
document.querySelectorAll('#flowTabs .nav-link').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#flowTabs .nav-link').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const filter = this.dataset.filter;
        document.querySelectorAll('.flow-card').forEach(card => {
            card.style.display = (filter === 'all' || card.dataset.type === filter) ? '' : 'none';
        });
    });
});

// Toggle status
document.querySelectorAll('.flow-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const id = this.dataset.id;
        fetch(`/app/auto-reply/flow/toggle/${id}`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'}
        }).then(r => r.json()).then(d => {
            console.log('Status:', d.status);
        });
    });
});
</script>
@endsection
