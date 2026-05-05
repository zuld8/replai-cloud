@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <!-- Validation Component -->
    <div class="row mb-4">
        <div class="col-12">
            <x-validation-component></x-validation-component>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Statistics Cards -->
        <div class="col-xl-4 col-lg-5">
            <div class="row g-4">
                <!-- MUA Package -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 fw-medium">MUA dari Paket</p>
                                    <h3 class="mb-2 fw-bold">{{number_format($summaries['package_mua_limit'])}}</h3>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-info-circle me-1"></i>
                                        MUA dari Paket Subscription Aktif
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-label-primary rounded">
                                        <i class="bx bx-user-check bx-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MUA Top Up -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 fw-medium">MUA Top Up</p>
                                    <h3 class="mb-2 fw-bold">{{number_format($summaries['topup_mua_limit'])}}</h3>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-infinite me-1"></i>
                                        MUA dari Top Up Tambahan
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-label-success rounded">
                                        <i class="bx bx-user-plus bx-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total MUA Limit -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 fw-medium">Total MUA Limit</p>
                                    <h3 class="mb-2 fw-bold">{{number_format($summaries['total_mua_limit'])}}</h3>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-group me-1"></i>
                                        Total Limit User Aktif per Bulan
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-label-info rounded">
                                        <i class="bx bx-calendar bx-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Usage -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 fw-medium">MUA Terpakai (30 Hari)</p>
                                    <h3 class="mb-2 fw-bold">{{number_format($summaries['current_mua_usage'])}}</h3>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-trending-up me-1"></i>
                                        Unique User yang Chat dalam 30 Hari
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-label-warning rounded">
                                        <i class="bx bx-line-chart bx-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Percentage -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 fw-medium">Persentase Penggunaan</p>
                                    <h3 class="mb-2 fw-bold">{{number_format($summaries['percentage'], 1)}}%</h3>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-pie-chart-alt me-1"></i>
                                        Dari Total MUA Limit
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-label-danger rounded">
                                        <i class='bx bx-bar-chart-alt-2 bx-md'></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Progress Bar -->
                            <div class="mt-3">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar 
                                        @if($summaries['percentage'] >= 90) bg-danger
                                        @elseif($summaries['percentage'] >= 70) bg-warning
                                        @else bg-success
                                        @endif"
                                        role="progressbar"
                                        style="width: {{min($summaries['percentage'], 100)}}%;"
                                        aria-valuenow="{{$summaries['percentage']}}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sisa MUA -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0 bg-gradient-primary">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-white-50 mb-2 fw-medium">Sisa MUA Tersedia</p>
                                    <h2 class="mb-2 fw-bold text-white">{{number_format($summaries['sisa_mua_limit'])}}</h2>
                                    <small class="text-white-50 d-block">
                                        <i class="bx bx-check-circle me-1"></i>
                                        User Aktif yang Masih Bisa Ditambahkan
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-white bg-opacity-25 rounded">
                                        <i class="bx bx-badge-check bx-md text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Transactions & Top Up -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm border-0">
                <!-- Card Header with Tabs -->
                <div class="card-header border-0 pb-0">
                    <ul class="nav nav-pills nav-justified w-100 tab-style-6" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#transactions-tab"
                                type="button" role="tab" aria-controls="transactions-tab" aria-selected="true">
                                <i class="bx bx-list-ul me-2"></i>
                                <span class="d-none d-sm-inline">Daftar Transaksi</span>
                                <span class="d-inline d-sm-none">Transaksi</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#topup-tab"
                                type="button" role="tab" aria-controls="topup-tab" aria-selected="false">
                                <i class="bx bx-wallet me-2"></i>
                                <span class="d-none d-sm-inline">Top Up MUA</span>
                                <span class="d-inline d-sm-none">Top Up</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Transactions Tab -->
                        <div class="tab-pane fade show active" id="transactions-tab" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="text-nowrap">Tanggal</th>
                                            <th scope="col" class="text-nowrap">MUA Limit</th>
                                            <th scope="col" class="text-nowrap">Nominal</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $transaction)
                                        <tr>
                                            <td class="text-nowrap">
                                                <small class="text-muted">{{ $transaction->created_at->format('d M Y') }}</small><br>
                                                <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                            </td>
                                            <td class="fw-semibold">
                                                <span class="badge bg-label-primary fs-6">
                                                    {{ number_format($transaction->new_order_mua_limit) }} Users
                                                </span>
                                            </td>
                                            <td class="fw-semibold">{{currency_format($transaction->final_total)}}</td>
                                            <td class="text-center">
                                                @if($transaction->status == 'pending')
                                                <span class="badge bg-warning">
                                                    <i class="bx bx-time-five me-1"></i>{{ __('starter.pending') }}
                                                </span>
                                                @elseif($transaction->status == 'process')
                                                <span class="badge bg-info">
                                                    <i class="bx bx-loader-circle me-1"></i>{{ __('starter.process') }}
                                                </span>
                                                @elseif($transaction->status == 'success')
                                                <span class="badge bg-success">
                                                    <i class="bx bx-check-circle me-1"></i>{{ __('starter.complete') }}
                                                </span>
                                                @elseif($transaction->status == 'rejected')
                                                <span class="badge bg-danger">
                                                    <i class="bx bx-x-circle me-1"></i>{{ __('starter.rejected') }}
                                                </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($transaction->status == 'pending')
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('mua.detail', $transaction->id) }}">
                                                                <i class="bx bx-credit-card me-2"></i>Bayar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger deletebutton"
                                                                href="{{ route('mua.delete', $transaction->id) }}">
                                                                <i class="bx bx-trash me-2"></i>{{ __('starter.reject_and_delete') }}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="bx bx-inbox bx-lg text-muted"></i>
                                                <p class="text-muted mt-2 mb-0">Belum ada transaksi MUA</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Top Up Tab -->
                        <div class="tab-pane fade" id="topup-tab" role="tabpanel">
                            <form action="{{route('mua.create')}}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="alert alert-info d-flex align-items-center" role="alert">
                                            <i class="bx bx-info-circle me-2 fs-5"></i>
                                            <div>
                                                <strong>Informasi:</strong> 1 Qty = {{ number_format($internal->mua_per_price) }} MUA (Monthly Active Users)
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Jumlah Top Up (Qty)</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">
                                                <i class="bx bx-purchase-tag"></i>
                                            </span>
                                            <input type="number" class="form-control"
                                                placeholder="Masukkan jumlah"
                                                id="qtyMua" name="qty"
                                                value="{{old('qty', 1)}}"
                                                min="1" required>
                                        </div>
                                        <small class="text-muted">Minimal pembelian 1 Qty</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Total MUA Limit</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">
                                                <i class="bx bx-group"></i>
                                            </span>
                                            <input type="text" class="form-control fw-bold"
                                                id="muaLimit"
                                                value="{{number_format(old('pricing', (int)$internal->mua_per_price))}}"
                                                readonly>
                                            <span class="input-group-text">Users</span>
                                        </div>
                                        <small class="text-muted">Limit MUA yang akan Anda dapatkan</small>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Total Pembayaran</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control fw-bold text-primary fs-4"
                                                value="{{number_format($internal->price_mua)}}"
                                                id="priceMua"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="card-title mb-3">
                                                    <i class="bx bx-receipt me-2"></i>Ringkasan Pembelian
                                                </h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Harga per Qty:</span>
                                                    <span class="fw-semibold">{{currency_format($internal->price_mua)}}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">MUA per Qty:</span>
                                                    <span class="fw-semibold">{{number_format($internal->mua_per_price)}} Users</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold">Total yang harus dibayar:</span>
                                                    <span class="fw-bold text-primary fs-5" id="summaryPrice">
                                                        {{currency_format($internal->price_mua)}}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary btn-lg w-100" type="submit">
                                            <i class="bx bx-cart me-2"></i>
                                            Lanjutkan ke Pembayaran
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qtyInput = document.getElementById("qtyMua");
        const muaLimitInput = document.getElementById("muaLimit");
        const priceMuaInput = document.getElementById("priceMua");
        const summaryPrice = document.getElementById("summaryPrice");

        // Get initial values
        const muaPerPrice = parseInt("{{ $internal->mua_per_price }}", 10);
        const pricePerMua = parseInt("{{ $internal->price_mua }}", 10);

        // Update function
        function updateCalculation() {
            let qty = parseInt(qtyInput.value, 10) || 1;

            // Ensure minimum qty is 1
            if (qty < 1) {
                qty = 1;
                qtyInput.value = 1;
            }

            // Calculate values
            const totalMua = muaPerPrice * qty;
            const totalPrice = pricePerMua * qty;

            // Update displays with formatting
            muaLimitInput.value = new Intl.NumberFormat("id-ID").format(totalMua);
            priceMuaInput.value = new Intl.NumberFormat("id-ID").format(totalPrice);
            summaryPrice.textContent = "Rp " + new Intl.NumberFormat("id-ID").format(totalPrice);
        }

        // Add event listener
        qtyInput.addEventListener("input", updateCalculation);

        // Initial calculation
        updateCalculation();
    });

    // Delete confirmation
    document.querySelectorAll('.deletebutton').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection