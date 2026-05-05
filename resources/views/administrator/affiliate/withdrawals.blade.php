@extends('layouts.admin')
@section('content')
<div class="container-xl">
<div class="page-header mb-3">
    <h2 class="page-title">Affiliate Withdrawals</h2>
</div>

@if(session('flash'))<div class="alert alert-success">{{ session('flash') }}</div>@endif
@if(session('gagal'))<div class="alert alert-danger">{{ session('gagal') }}</div>@endif

<div class="card">
<div class="table-responsive">
<table class="table table-vcenter">
    <thead><tr>
        <th>Tanggal</th><th>Affiliator</th><th>Bank</th>
        <th>No. Rekening</th><th>Atas Nama</th><th>Jumlah</th><th>Status</th><th>Aksi</th>
    </tr></thead>
    <tbody>
    @forelse($withdrawals as $wd)
    <tr>
        <td style="font-size:0.8rem;">{{ $wd->created_at->format("d M Y H:i") }}</td>
        <td>{{ $wd->affiliate->user->name ?? "-" }}</td>
        <td>{{ $wd->bank_name }}</td>
        <td><code>{{ $wd->bank_account }}</code></td>
        <td>{{ $wd->bank_holder }}</td>
        <td class="fw-bold">Rp {{ number_format($wd->amount) }}</td>
        <td>
            @if($wd->status=="pending")<span class="badge bg-warning text-dark">Pending</span>
            @elseif($wd->status=="paid")<span class="badge bg-success">Paid</span>
            @elseif($wd->status=="rejected")<span class="badge bg-danger">Rejected</span>
            @else<span class="badge bg-secondary">{{ $wd->status }}</span>@endif
        </td>
        <td>
        @if($wd->status=="pending")
            <form method="POST" action="{{ route('admin.affiliate.withdraw.approve', $wd->id) }}" style="display:inline">
                @csrf
                <button class="btn btn-sm btn-success" onclick="return confirm('Approve withdrawal Rp {{ number_format($wd->amount) }}?')">✅ Approve</button>
            </form>
            <form method="POST" action="{{ route('admin.affiliate.withdraw.reject', $wd->id) }}" style="display:inline">
                @csrf
                <input type="hidden" name="notes" value="Ditolak admin">
                <button class="btn btn-sm btn-danger" onclick="return confirm('Reject?')">❌ Reject</button>
            </form>
        @elseif($wd->status=="paid")
            <span class="text-muted" style="font-size:0.78rem;">Dibayar {{ $wd->paid_at?->format("d M Y") }}</span>
        @elseif($wd->status=="rejected")
            <span class="text-muted" style="font-size:0.78rem;">{{ $wd->notes }}</span>
        @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada withdrawal request</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@if($withdrawals->hasPages())
<div class="card-footer">{{ $withdrawals->links() }}</div>
@endif
</div>
</div>
@endsection
