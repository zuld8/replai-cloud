@extends('layouts.admin')
@section('content')
<div class="container-xl">
<div class="page-header mb-3">
    <div class="row align-items-center">
        <div class="col-auto"><h2 class="page-title">Affiliate Management</h2></div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.affiliate.withdrawals') }}" class="btn btn-warning btn-sm">💸 Withdrawal Requests</a>
        </div>
    </div>
</div>
<div class="card">
<div class="table-responsive">
<table class="table table-vcenter">
    <thead><tr>
        <th>Affiliator</th><th>Kode</th><th>Klik</th><th>Referral</th>
        <th>Total Earned</th><th>Total WD</th><th>Status</th><th></th>
    </tr></thead>
    <tbody>
    @forelse($affiliates as $aff)
    <tr>
        <td>{{ $aff->user->name ?? "-" }}<br><small class="text-muted">{{ $aff->user->email ?? "" }}</small></td>
        <td><code>{{ $aff->code }}</code></td>
        <td>{{ number_format($aff->total_click) }}</td>
        <td>{{ number_format($aff->total_referral) }}</td>
        <td class="fw-bold text-success">Rp {{ number_format($aff->total_earned) }}</td>
        <td>Rp {{ number_format($aff->total_withdrawn) }}</td>
        <td>
            @if($aff->status == "active")
            <span class="badge bg-success">Aktif</span>
            @else
            <span class="badge bg-danger">Banned</span>
            @endif
        </td>
        <td>
            @if($aff->status == "active")
            <a href="{{ route('admin.affiliate.ban', $aff->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Ban affiliator ini?')">Ban</a>
            @else
            <a href="{{ route('admin.affiliate.unban', $aff->id) }}" class="btn btn-sm btn-outline-success">Unban</a>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada affiliator</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@if($affiliates->hasPages())
<div class="card-footer">{{ $affiliates->links() }}</div>
@endif
</div>
</div>
@endsection
