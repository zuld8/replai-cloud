@extends('layouts.app')

@section('title', 'Agent Performance Detail - ' . $agent->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/conversation_report.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card">
            <!-- Agent Header -->
            <div class="agent-detail-header">
                <div class="agent-profile">
                    <img src="{{ asset($agent->image_data) }}"
                        alt="{{ $agent->name }}"
                        class="agent-profile-image">
                    <div class="agent-profile-info">
                        <h2 class="agent-profile-name">{{ $agent->name }}</h2>
                        <p class="agent-profile-email">{{ $agent->email }}</p>
                        @if($agent->role)
                        <span class="badge bg-primary">{{ ucfirst($agent->role) }}</span>
                        @endif
                    </div>
                </div>
                <div class="agent-header-actions">
                    <form method="GET" class="year-selector">
                        <select name="year" class="form-select" onchange="this.form.submit()">
                            @for ($year = now()->year; $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                            @endfor
                        </select>
                    </form>
                    <a href="{{ route('reports.conversation') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>


        <!-- Current Month Summary -->
        @if(!empty($monthlyData['agents']))
        @php $agentData = $monthlyData['agents'][0]; @endphp
        <div class="summary-section">
            <h5 class="section-title">
                <i class="bi bi-calendar-check"></i>
                Performance Bulan Ini ({{ \Carbon\Carbon::create(null, now()->month, 1)->format('F Y') }})
            </h5>
            <div class="row g-4">

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Total Conversations</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($agentData['conversations']['total']) }}</h4>
                                </div>
                                <div class="card-chart bg-pink-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-chat text-pink fs-24"></i> </div>
                            </div>
                            <div class="stat-detail">
                                <span class="text-success">{{ $agentData['conversations']['resolved'] }} Resolved</span> •
                                <span class="text-warning">{{ $agentData['conversations']['open'] }} Open</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Resolution Rate</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($agentData['conversations']['resolution_rate'], 1) }}%</h4>
                                </div>
                                <div class="card-chart bg-info-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-check-circle text-info fs-24"></i> </div>
                            </div>
                            <div class="progress progress-sm mt-2">
                                <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{$agentData['conversations']['resolution_rate']}}" class="progress-bar bg-info wd-{{$agentData['conversations']['resolution_rate']}}p" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Messages Sent</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($agentData['messages']['sent']) }}</h4>
                                </div>
                                <div class="card-chart bg-green-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-send text-green fs-24"></i> </div>
                            </div>
                            <div class="stat-detail">
                                Avg {{ number_format($agentData['messages']['avg_per_conversation'], 1) }} per conversation
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Avg Response Time </h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($agentData['response_time']['avg_first_response'], 1) }} min</h4>
                                </div>
                                <div class="card-chart bg-warning-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-time text-warning fs-24"></i> </div>
                            </div>
                            <span class="badge {{ $agentData['response_time']['avg_first_response'] <= 5 ? 'bg-success' : ($agentData['response_time']['avg_first_response'] <= 15 ? 'bg-warning' : 'bg-danger') }}">
                                {{ $agentData['response_time']['avg_first_response'] <= 5 ? 'Excellent' : ($agentData['response_time']['avg_first_response'] <= 15 ? 'Good' : 'Needs Improvement') }}
                            </span>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Yearly Trend -->
        <div class="charts-section">
            <h5 class="section-title">
                <i class="bi bi-graph-up"></i>
                Performance Trend {{ $selectedYear }}
            </h5>
            <div class="row g-4">
                <!-- Conversations Trend -->
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="bi bi-bar-chart-line"></i>
                                Conversations per Month
                            </h5>
                        </div>
                        <div class="chart-body" style="height: 350px;">
                            <canvas id="conversationsTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Resolution Rate Trend -->
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="bi bi-graph-up"></i>
                                Resolution Rate Trend
                            </h5>
                        </div>
                        <div class="chart-body" style="height: 350px;">
                            <canvas id="resolutionTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Messages Trend -->
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="bi bi-chat-left-text-fill"></i>
                                Messages Sent per Month
                            </h5>
                        </div>
                        <div class="chart-body" style="height: 350px;">
                            <canvas id="messagesTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Response Time Trend -->
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="bi bi-speedometer2"></i>
                                Avg Response Time Trend
                            </h5>
                        </div>
                        <div class="chart-body" style="height: 350px;">
                            <canvas id="responseTimeTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Breakdown Table -->
        <div class="card custom-card">
            <div class="card-header">
                <i class="bx bx-calendar me-2"></i>
                Monthly Breakdown {{ $selectedYear }}
            </div>
            <div class="card-body">
                <table class="table table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-center">Total Conv.</th>
                            <th class="text-center">Resolved</th>
                            <th class="text-center">Resolution %</th>
                            <th class="text-center">Messages</th>
                            <th class="text-center">Avg Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($yearlyData['monthly_data'] as $monthData)
                        <tr>
                            <td>
                                <strong>{{ $monthData['month_name'] }}</strong>
                            </td>
                            <td class="text-center">
                                {{ number_format($monthData['metrics']['conversations']['total'] ?? 0) }}
                            </td>
                            <td class="text-center">
                                {{ number_format($monthData['metrics']['conversations']['resolved'] ?? 0) }}
                            </td>
                            <td class="text-center">
                                @php
                                $total = $monthData['metrics']['conversations']['total'] ?? 0;
                                $resolved = $monthData['metrics']['conversations']['resolved'] ?? 0;
                                $rate = $total > 0 ? ($resolved / $total) * 100 : 0;
                                @endphp
                                <span class="badge {{ $rate >= 80 ? 'bg-success' : ($rate >= 50 ? 'bg-warning' : 'bg-secondary') }}">
                                    {{ number_format($rate, 1) }}%
                                </span>
                            </td>
                            <td class="text-center">
                                {{ number_format($monthData['metrics']['messages']['sent'] ?? 0) }}
                            </td>
                            <td class="text-center">
                                @php
                                $avgResponse = $monthData['metrics']['response_time']['avg_first_response'] ?? 0;
                                @endphp
                                @if($avgResponse > 0)
                                <span class="badge {{ $avgResponse <= 5 ? 'bg-success' : ($avgResponse <= 15 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ number_format($avgResponse, 1) }} min
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script src="{{asset('assets/js/chart.js')}}"></script>
 <script>
    const yearlyData = @json($yearlyData['monthly_data']);
    
    document.addEventListener('DOMContentLoaded', function() {
        // Conversations Trend
        new Chart(document.getElementById('conversationsTrendChart'), {
            type: 'bar',
            data: {
                labels: yearlyData.map(d => d.month_name),
                datasets: [{
                    label: 'Total Conversations',
                    data: yearlyData.map(d => d.metrics.conversations?.total || 0),
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderRadius: 8
                }, {
                    label: 'Resolved',
                    data: yearlyData.map(d => d.metrics.conversations?.resolved || 0),
                    backgroundColor: 'rgba(76, 175, 80, 0.8)',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Resolution Rate Trend
        new Chart(document.getElementById('resolutionTrendChart'), {
            type: 'line',
            data: {
                labels: yearlyData.map(d => d.month_name),
                datasets: [{
                    label: 'Resolution Rate (%)',
                    data: yearlyData.map(d => {
                        const total = d.metrics.conversations?.total || 0;
                        const resolved = d.metrics.conversations?.resolved || 0;
                        return total > 0 ? (resolved / total) * 100 : 0;
                    }),
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
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: value => value + '%'
                        }
                    }
                }
            }
        });

        // Messages Trend
        new Chart(document.getElementById('messagesTrendChart'), {
            type: 'bar',
            data: {
                labels: yearlyData.map(d => d.month_name),
                datasets: [{
                    label: 'Messages Sent',
                    data: yearlyData.map(d => d.metrics.messages?.sent || 0),
                    backgroundColor: 'rgba(33, 150, 243, 0.8)',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Response Time Trend
        new Chart(document.getElementById('responseTimeTrendChart'), {
            type: 'line',
            data: {
                labels: yearlyData.map(d => d.month_name),
                datasets: [{
                    label: 'Avg Response Time (min)',
                    data: yearlyData.map(d => d.metrics.response_time?.avg_first_response || 0),
                    borderColor: '#FF9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            callback: value => value + ' min'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection