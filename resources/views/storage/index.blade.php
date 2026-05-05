@extends('layouts.app')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <x-validation-component></x-validation-component>
    </div>
    <!-- Main Storage Card -->
    <div class="col-lg-8 col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-0">
                <h3 class="card-title mb-0">
                    <i class="bx bx-data text-primary me-2"></i>
                    Ringkasan Pemakaian Storage
                </h3>
            </div>
            <div class="card-body">
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="mb-0">
                                <span class="text-primary fw-bold">{{number_format($summaries['using_storage'], 2)}} MB</span>
                                <span class="text-muted">/ {{number_format($summaries['total_storage'], 2)}} MB</span>
                            </h4>
                            <small class="text-muted">Pemakaian saat ini</small>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-lg 
                                    @if($summaries['storage_status'] == 'critical') bg-danger
                                    @elseif($summaries['storage_status'] == 'warning') bg-warning
                                    @else bg-success
                                    @endif">
                                {{$summaries['percentase']}}%
                            </span>
                        </div>
                    </div>

                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar 
                                @if($summaries['storage_status'] == 'critical') bg-danger progress-bar-animated
                                @elseif($summaries['storage_status'] == 'warning') bg-warning
                                @else bg-primary
                                @endif"
                            role="progressbar"
                            style="width: {{$summaries['percentase']}}%"
                            aria-valuenow="{{$summaries['percentase']}}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>

                    @if($summaries['storage_status'] == 'critical')
                    <div class="alert alert-danger mt-3 mb-0">
                        <div class="d-flex">
                            <div>
                                <i class="bx bx-error-circle fs-3 me-2"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">Storage Hampir Penuh!</h4>
                                <div class="text-muted">Storage Anda sudah terpakai {{$summaries['percentase']}}%. Segera upgrade untuk menghindari gangguan layanan.</div>
                            </div>
                        </div>
                    </div>
                    @elseif($summaries['storage_status'] == 'warning')
                    <div class="alert alert-warning mt-3 mb-0">
                        <div class="d-flex">
                            <div>
                                <i class="bx bx-error fs-3 me-2"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">Perhatian!</h4>
                                <div class="text-muted">Storage Anda sudah terpakai {{$summaries['percentase']}}%. Pertimbangkan untuk menambah kapasitas.</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Storage Details -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card bg-primary-lt border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="bx bx-package fs-1 text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Dari Paket Langganan</div>
                                        <h3 class="mb-0">{{number_format($summaries['storage_from_subscribe'], 2)}} MB</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-success-lt border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="bx bx-plus-circle fs-1 text-success"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Dari Addon</div>
                                        <h3 class="mb-0">{{number_format($summaries['storage_from_addons'], 2)}} MB</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-info-lt border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="bx bx-cloud-download fs-1 text-info"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Sisa Storage</div>
                                        <h3 class="mb-0">{{number_format($summaries['remaining_storage'], 2)}} MB</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Chart -->
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h3 class="card-title mb-0">Distribusi Storage</h3>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="storageChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Storage Addons -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title">
                    <i class="bx bx-shopping-bag text-primary me-2"></i>
                    Paket Addon Storage
                </h3>
            </div>
            <div class="card-body">
                @if($addons->isEmpty())
                <div class="empty">
                    <div class="empty-icon">
                        <i class="bx bx-package fs-1"></i>
                    </div>
                    <p class="empty-title">Belum ada addon tersedia</p>
                    <p class="empty-subtitle text-muted">
                        Addon storage akan segera tersedia
                    </p>
                </div>
                @else
                <div class="row row-cards">
                    @foreach($addons as $addon)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card card-addon border @if($storageAddonsId == $addon->id) border-primary @endif h-100">
                            <div class="card-body">
                                @if($storageAddonsId == $addon->id)
                                <div class="ribbon ribbon-top ribbon-bookmark bg-primary">
                                    <i class="bx bx-check"></i> Aktif
                                </div>
                                @endif

                                <div class="text-center mb-3">
                                    <div class="avatar avatar-xl bg-primary-lt rounded-circle mx-auto mb-3">
                                        <i class="bx bx-data fs-1 text-primary"></i>
                                    </div>
                                    <h3 class="card-title mb-1">{{$addon->name}}</h3>
                                    <div class="text-muted small mb-3">{{$addon->storage_name}}</div>

                                    <div class="mb-3">
                                        <h2 class="text-primary mb-0">{{currency_format($addon->price)}}</h2>
                                        <small class="text-muted">
                                            @if($addon->days_option == 'limited')
                                            / {{number_format($addon->add_days)}} hari
                                            @else
                                            / selamanya
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                <ul class="list-unstyled mb-3">
                                    <li class="mb-2">
                                        <i class="bx bx-check text-success me-2"></i>
                                        <strong>{{$addon->storage_name}}</strong> Storage
                                    </li>
                                    <li class="mb-2">
                                        <i class="bx bx-time-five text-muted me-2"></i>
                                        @if($addon->days_option == 'limited')
                                        Berlaku {{$addon->add_days}} hari
                                        @else
                                        Berlaku selamanya
                                        @endif
                                    </li>
                                </ul>

                                @if($storageAddonsId == $addon->id)
                                <button class="btn btn-outline-primary w-100" disabled>
                                    <i class="bx bx-check-circle me-1"></i> Sedang Aktif
                                </button>
                                @else
                                <a href="{{route('storage.create',$addon->id)}}" class="btn btn-primary w-100">
                                    <i class="bx bx-cart me-1"></i> Beli Sekarang
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Transaction History -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title">
                    <i class="bx bx-receipt text-primary me-2"></i>
                    Riwayat Transaksi Addon
                </h3>
            </div>
            <div class="card-body">
                @if($transactions->isEmpty())
                <div class="empty">
                    <div class="empty-icon">
                        <i class="bx bx-receipt fs-1"></i>
                    </div>
                    <p class="empty-title">Belum ada transaksi</p>
                    <p class="empty-subtitle text-muted">
                        Riwayat pembelian addon akan muncul di sini
                    </p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-vcenter ">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Paket</th>
                                <th>Durasi</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Berlaku Hingga</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>
                                    <div class="text-muted">
                                        {{$transaction->created_at->format('d/m/Y')}}
                                    </div>
                                    <small class="text-muted">{{$transaction->created_at->format('H:i')}}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                       
                                        <div>
                                            <div class="fw-bold">{{$transaction->package->name ?? 'N/A'}}</div>
                                            <small class="text-muted">{{$transaction->storage_name ?? 'N/A'}}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($transaction->days_option == 'limited')
                                    <span class="badge bg-info">{{$transaction->add_days}} Hari</span>
                                    @else
                                    <span class="badge bg-success-lt">Selamanya</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">{{currency_format($transaction->price)}}</div>
                                </td>
                                <td>
                                    @if($transaction->status == 'success')
                                    <span class="badge bg-success">Aktif</span>
                                    @elseif($transaction->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($transaction->status == 'rejected')
                                    <span class="badge bg-danger">Di Batalkan</span>
                                    @else
                                    <span class="badge bg-secondary">{{$transaction->status}}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->expire_date)
                                    <div class="text-muted">{{date('d/m/Y', strtotime($transaction->expire_date))}}</div>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->status == 'pending')
                                    <a class="btn btn-info btn-sm" href="{{ route('storage.detail', $transaction->id) }}">
                                        <i class="bx bx-list-ul me-2"></i> Bayar
                                    </a>
                                    <a class="btn btn-danger btn-sm" href="{{ route('storage.delete', $transaction->id) }}">
                                        <i class="bx bx-trash me-2"></i> {{ __('starter.reject_and_delete') }}
                                    </a>
                                   
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS & JS -->
<style>
    .bg-primary-lt {
        background-color: rgba(32, 107, 196, 0.1);
    }

    .bg-success-lt {
        background-color: rgba(5, 205, 153, 0.1);
    }

    .bg-info-lt {
        background-color: rgba(66, 153, 225, 0.1);
    }

    .card-addon {
        transition: all 0.3s ease;
    }

    .card-addon:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .avatar-xl {
        width: 4rem;
        height: 4rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ribbon {
        position: absolute;
        top: 0.75rem;
        right: -0.25rem;
        z-index: 1;
        padding: 0.25rem 0.75rem;
        font-size: 0.625rem;
        font-weight: 600;
        line-height: 1.5;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        background: #206bc4;
        border-radius: 0.25rem;
    }

    .ribbon-bookmark:before {
        position: absolute;
        right: 0;
        bottom: -0.5rem;
        width: 0;
        height: 0;
        content: "";
        border-style: solid;
        border-width: 0.5rem 0.25rem 0 0.25rem;
        border-color: #1a569e transparent transparent transparent;
    }

    .empty {
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-icon {
        margin-bottom: 1rem;
        color: #ccc;
    }

    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }

    @keyframes progress-bar-stripes {
        0% {
            background-position: 1rem 0;
        }

        100% {
            background-position: 0 0;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Storage Chart
        const ctx = document.getElementById('storageChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Terpakai', 'Tersisa'],
                    datasets: [{
                        data: [{{$summaries['using_storage']}}, {{$summaries['remaining_storage']}}],
                        backgroundColor: [
                            @if($summaries['storage_status'] == 'critical')
                            '#d63939'
                            @elseif($summaries['storage_status'] == 'warning')
                            '#f59f00'
                            @else '#206bc4'
                            @endif,
                            '#e9ecef'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed + ' MB';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection