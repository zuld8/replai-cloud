@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/lead_report.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Period Filters -->
        <div class="card custom-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="bi bi-funnel"></i> Filter Data
                </div>
            </div>
            <form method="GET" action="{{ route('reports.leads') }}" id="filterForm" class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="period" class="form-label">Period</label>
                        <select name="period" id="period" class="form-select" onchange="toggleCustomDates()">
                            <option value="today" {{ $selectedPeriod == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="month" {{ $selectedPeriod == 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="year" {{ $selectedPeriod == 'year' ? 'selected' : '' }}>This Year</option>
                            <option value="custom" {{ $selectedPeriod == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>

                    <div class="col-md-3" id="startDateDiv" style="display: {{ $selectedPeriod == 'custom' ? 'block' : 'none' }}">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                    </div>

                    <div class="col-md-3" id="endDateDiv" style="display: {{ $selectedPeriod == 'custom' ? 'block' : 'none' }}">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Apply Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Period Info -->
        <div class="period-info">
            <i class="bi bi-calendar-event"></i>
            <strong>Period:</strong>
            {{ ucfirst($selectedPeriod) }} - {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}
        </div>

        <!-- Summary Cards -->
        <div class="summary-section">
            <div class="row g-4">
                <!-- Total Leads -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Total Leads</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($data['summary']['total_leads']) }}</h4>
                                </div>
                                <div class="card-chart bg-info-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-user text-info fs-24"></i> </div>
                            </div>
                            <span class="badge bg-primary">
                                {{ $data['summary']['total_labels'] }} Labels
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Closed Leads -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Closed Leads</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($data['summary']['closed_leads']) }}</h4>
                                </div>
                                <div class="card-chart bg-succes-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-check-circle text-succes fs-24"></i> </div>
                            </div>
                            <span class="badge bg-primary">
                                {{ number_format($data['summary']['closing_rate'], 1) }}% Rate
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Active in Pipeline</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($data['summary']['active_pipeline_value']) }}</h4>
                                </div>
                                <div class="card-chart bg-warning-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-timer text-warning fs-24"></i> </div>
                            </div>
                            <span class="badge bg-warning">
                                Working leads
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Avg Time to Close -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Avg Time to Close</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($data['summary']['avg_time_to_close'], 1) }}</h4>
                                </div>
                                <div class="card-chart bg-primary-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-timer text-primary fs-24"></i> </div>
                            </div>
                            <span class="badge bg-primary">
                                Days
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Charts Row -->
    <div class="charts-section">
        <div class="row g-4">
            <!-- Pipeline Funnel Chart -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="bi bi-funnel-fill"></i>
                            Pipeline Funnel
                        </h5>
                    </div>
                    <div class="chart-body" style="height: 400px;">
                        <canvas id="funnelChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Conversion Rates -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="bi bi-graph-up-arrow"></i>
                            Conversion Rates
                        </h5>
                    </div>
                    <div class="chart-body" style="height: 400px;">
                        <canvas id="conversionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Chart -->
    <div class="charts-section">
        <div class="row">
            <div class="col-12">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5 class="chart-title">
                            <i class="bi bi-graph-down"></i>
                            Leads Trend
                        </h5>
                    </div>
                    <div class="chart-body" style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline Segments -->
    <div class="pipeline-segments-section">
        <h5 class="section-title">
            <i class="bi bi-diagram-3"></i>
            Pipeline Segments Breakdown
        </h5>

        <div class="row g-4">
            @forelse($data['pipeline_segments'] as $segment)
            <div class="col-lg-6">
                <div class="segment-card">
                    <div class="segment-header" style="background: {{ $segment['segment_color'] ?? '#667eea' }}20; border-left: 4px solid {{ $segment['segment_color'] ?? '#667eea' }}">
                        <h6 class="segment-name">{{ $segment['segment_name'] }}</h6>
                        <div class="segment-total">{{ number_format($segment['total_leads']) }} leads</div>
                    </div>
                    <div class="segment-body">
                        @forelse($segment['labels'] as $label)
                        <div class="label-item">
                            <div class="label-info-data">
                                <span class="label-color" style="background: {{ $label['label_color'] ?? '#ccc' }}"></span>
                                <span class="label-name">{{ $label['label_name'] }}</span>
                                @if($label['is_closeable'])
                                <span class="badge badge-sm bg-success">Closeable</span>
                                @endif
                            </div>
                            <div class="label-count">{{ number_format($label['total_leads']) }}</div>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3">No labels in this segment</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No pipeline segments configured</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Labels Performance Table -->
    <div class="card custom-card">
        <div class="card-header">
            <i class="bx bx-table me-2"></i>
            Labels Performance
        </div>
        <div class="card-body">
            <table class="table table-bordered text-nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Pipeline Segment</th>
                        <th class="text-center">Total Leads</th>
                        <th class="text-center">Conversion Rate</th>
                        <th class="text-center">Drop-off</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['leads_by_label'] as $index => $label)
                    <tr>
                        <td>
                            <div class="label-cell">
                                <span class="label-dot" style="background: {{ $label['label_color'] ?? '#ccc' }}"></span>
                                <strong>{{ $label['label_name'] }}</strong>
                            </div>
                        </td>
                        <td>{{ $label['pipeline_segment'] ?? '-' }}</td>
                        <td class="text-center">
                            <strong>{{ number_format($label['total_leads']) }}</strong>
                        </td>
                        <td class="text-center">
                            @if(isset($data['conversion_rates'][$index]))
                            <span class="badge bg-primary">
                                {{ number_format($data['conversion_rates'][$index]['conversion_rate'], 1) }}%
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(isset($data['conversion_rates'][$index]) && $data['conversion_rates'][$index]['drop_off'] > 0)
                            <span class="badge bg-warning">
                                {{ number_format($data['conversion_rates'][$index]['drop_off']) }}
                                ({{ number_format($data['conversion_rates'][$index]['drop_off_rate'], 1) }}%)
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($label['is_closeable'])
                            <span class="badge bg-success">Closeable</span>
                            @else
                            <span class="badge bg-secondary">In Progress</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>No leads data for this period</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/js/chart.js')}}"></script>
<script>
    // Toggle custom date fields
    function toggleCustomDates() {
        const period = document.getElementById('period').value;
        const startDiv = document.getElementById('startDateDiv');
        const endDiv = document.getElementById('endDateDiv');

        if (period === 'custom') {
            startDiv.style.display = 'block';
            endDiv.style.display = 'block';
        } else {
            startDiv.style.display = 'none';
            endDiv.style.display = 'none';
        }
    }

    // Chart data
    const leadsByLabel = @json($data['leads_by_label']);
    const conversionRates = @json($data['conversion_rates']);
    const trends = @json($data['trends']);

    // Funnel Chart
    new Chart(document.getElementById('funnelChart'), {
        type: 'bar',
        data: {
            labels: leadsByLabel.map(l => l.label_name),
            datasets: [{
                label: 'Leads',
                data: leadsByLabel.map(l => l.total_leads),
                backgroundColor: leadsByLabel.map(l => l.label_color || '#667eea'),
                borderRadius: 8,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Conversion Chart
    new Chart(document.getElementById('conversionChart'), {
        type: 'line',
        data: {
            labels: conversionRates.map(c => c.label_name),
            datasets: [{
                label: 'Conversion Rate (%)',
                data: conversionRates.map(c => c.conversion_rate),
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: v => v + '%'
                    }
                }
            }
        }
    });

    // Trend Chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trends.map(t => t.label),
            datasets: [{
                label: 'New Leads',
                data: trends.map(t => t.total_leads),
                borderColor: '#2196F3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection