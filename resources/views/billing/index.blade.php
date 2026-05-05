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
                <!-- AI Credit Gratis -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 fw-medium">AI Credit Gratis</p>
                                    <h3 class="mb-2 fw-bold">{{number_format($summaries['package_credit'])}}</h3>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-info-circle me-1"></i>
                                        AI Credit Gratis Akan Reset Setiap Tanggal 1 (Kecuali Paket Lifetime)
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-label-primary rounded">
                                        <i class="bx bx-coin-stack bx-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Credit Top Up -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 fw-medium">AI Credit Top Up</p>
                                    <h3 class="mb-2 fw-bold">{{number_format($summaries['top_up_credit'])}}</h3>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-infinite me-1"></i>
                                        AI Credit Top Up Tidak Akan Reset (Diakumulasi Terus)
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-label-success rounded">
                                        <i class="bx bx-coin bx-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Penggunaan AI Credit -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 fw-medium">Penggunaan AI Credit</p>
                                    <h3 class="mb-2 fw-bold">{{number_format($summaries['using_credit'])}}</h3>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-trending-down me-1"></i>
                                        Jumlah AI Credit yang Sudah Terpakai
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-label-warning rounded">
                                        <i class="bx bx-transfer bx-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Persentase Penggunaan -->
                <div class="col-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 fw-medium">Persentase Penggunaan</p>
                                    <h3 class="mb-2 fw-bold">{{number_format($summaries['percentase'])}}%</h3>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-pie-chart-alt me-1"></i>
                                        Persentase AI Credit Yang Sudah Terpakai
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-label-info rounded">
                                        <i class='bx bx-bar-chart-alt-2 bx-md'></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Progress Bar -->
                            <div class="mt-3">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?=$summaries['percentase'];?>%;" 
                                         aria-valuenow="{{$summaries['percentase']}}" 
                                         aria-valuemin="0" aria-valuemax="100">
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
                                <span class="d-none d-sm-inline">Top Up AI Credit</span>
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
                                            <th scope="col" class="text-nowrap">AI Kredit</th>
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
                                            <td class="fw-semibold">{{ number_format($transaction->new_order_ai_response) }}</td>
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
                                                            <a class="dropdown-item" href="{{ route('billing.detail', $transaction->id) }}">
                                                                <i class="bx bx-credit-card me-2"></i>Bayar
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger deletebutton" 
                                                               href="{{ route('billing.delete', $transaction->id) }}">
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
                                                <p class="text-muted mt-2 mb-0">Belum ada transaksi</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Top Up Tab -->
                        <div class="tab-pane fade" id="topup-tab" role="tabpanel">
                            <form action="{{route('billing.create')}}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="alert alert-info d-flex align-items-center" role="alert">
                                            <i class="bx bx-info-circle me-2 fs-5"></i>
                                            <div>
                                                <strong>Informasi:</strong> 1 Qty = {{ number_format($internal->token_per_price) }} AI Credit
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
                                                   id="qtyToken" name="qty" 
                                                   value="{{old('qty', 1)}}" 
                                                   min="1" required>
                                        </div>
                                        <small class="text-muted">Minimal pembelian 1 Qty</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Total AI Credit</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">
                                                <i class="bx bx-coin-stack"></i>
                                            </span>
                                            <input type="text" class="form-control fw-bold" 
                                                   id="tokenCredit" 
                                                   value="{{number_format(old('pricing', (int)$internal->token_per_price))}}" 
                                                   readonly>
                                            <span class="input-group-text">Credit</span>
                                        </div>
                                        <small class="text-muted">AI Credit yang akan Anda dapatkan</small>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Total Pembayaran</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text"></span>
                                            <input type="text" class="form-control fw-bold text-primary fs-4" 
                                                   value="{{number_format($internal->price_token)}}" 
                                                   id="priceToken" 
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
                                                    <span class="fw-semibold">{{currency_format($internal->price_token)}}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Credit per Qty:</span>
                                                    <span class="fw-semibold">{{number_format($internal->token_per_price)}} Credit</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold">Total yang harus dibayar:</span>
                                                    <span class="fw-bold text-primary fs-5" id="summaryPrice">
                                                        {{currency_format($internal->price_token)}}
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
        const qtyInput = document.getElementById("qtyToken");
        const tokenCreditInput = document.getElementById("tokenCredit");
        const priceTokenInput = document.getElementById("priceToken");
        const summaryPrice = document.getElementById("summaryPrice");

        // Get initial values
        const tokenPerPrice = parseInt("{{ $internal->token_per_price }}", 10);
        const pricePerToken = parseInt("{{ $internal->price_token }}", 10);

        // Update function
        function updateCalculation() {
            let qty = parseInt(qtyInput.value, 10) || 1;
            
            // Ensure minimum qty is 1
            if (qty < 1) {
                qty = 1;
                qtyInput.value = 1;
            }

            // Calculate values
            const totalCredit = tokenPerPrice * qty;
            const totalPrice = pricePerToken * qty;

            // Update displays with formatting
            tokenCreditInput.value = new Intl.NumberFormat("id-ID").format(totalCredit);
            priceTokenInput.value = new Intl.NumberFormat("id-ID").format(totalPrice);
            summaryPrice.textContent = "" + new Intl.NumberFormat("id-ID").format(totalPrice);
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