@extends('layouts.app')


@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/conversation_report.css') }}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/datatable/css/responsive.bootstrap.min.css')}}">
@endsection

@section('button')
<!-- <div class="btn-list"> 
    <a href="{{ route('reports.conversation.export', ['year' => $selectedYear, 'month' => $selectedMonth, 'format' => 'csv']) }}"
        class="btn btn-primary">
        <i class="bi bi-download"></i> Export CSV
    </a>
</div> -->
@endsection



@section('content')
<div class="row">
    <div class="col-12">
        <!-- Alert Messages -->
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Filters Section -->
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    <i class="bi bi-funnel"></i> Filter Data
                </div>
                <button class="btn btn-sm btn-outline-secondary" id="resetFilters">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </button>
            </div>
            <form method="GET" action="{{ route('reports.conversation') }}" id="filterForm" class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="year" class="form-label">Tahun</label>
                        <select name="year" id="year" class="form-select">
                            @foreach($yearOptions as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="month" class="form-label">Bulan</label>
                        <select name="month" id="month" class="form-select">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                                </option>
                                @endfor
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="agent_id" class="form-label">Agent (Opsional)</label>
                        <select name="agent_id" id="agent_id" class="form-select">
                            <option value="">Semua Agent</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ $selectedAgent == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }} ({{ $agent->email }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Period Info -->
        <div class="period-info">
            <i class="bi bi-calendar-range"></i>
            <strong>Period:</strong>
            {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->format('F Y') }}
            ({{ $data['period']['start_date'] }} - {{ $data['period']['end_date'] }})
        </div>

        <!-- Summary Cards -->
        <div class="summary-section">
            <div class="row g-4">
                <!-- Total Conversations Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Total Conversations</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($data['overall']['total_conversations']) }}</h4>
                                </div>
                                <div class="card-chart bg-pink-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-chat text-pink fs-24"></i> </div>
                            </div>
                            <span class="badge bg-primary">
                                {{ number_format($data['overall']['agent_coverage'], 1) }}% by Agents
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Active Agents Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Active Agents</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ $data['summary']['total_agents'] }}</h4>
                                </div>
                                <div class="card-chart bg-green-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-user text-green fs-24"></i> </div>
                            </div>
                            <div class="stat-content">
                                <div class="stat-detail">
                                    @if($data['summary']['top_performer']['name'] ?? false)
                                    <i class="bi bi-trophy-fill text-warning"></i>
                                    Top: {{ $data['summary']['top_performer']['name'] }}
                                    @else
                                    <span class="text-muted">No data</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avg Resolution Rate Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Avg Resolution Rate</h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($data['summary']['avg_resolution_rate'], 1) }}%</h4>
                                </div>
                                <div class="card-chart bg-info-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-check-circle text-info fs-24"></i> </div>
                            </div>
                            <div class="progress progress-sm mt-2">
                                <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{$data['summary']['avg_resolution_rate']}}" class="progress-bar bg-info wd-{{$data['summary']['avg_resolution_rate']}}p" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avg Response Time Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body iconfont text-start">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mb-3">Avg Response Time </h4>
                            </div>
                            <div class="d-flex mb-0">
                                <div class="">
                                    <h4 class="mb-1 font-weight-bold">{{ number_format($data['summary']['avg_response_time'], 1) }} min</h4>
                                </div>
                                <div class="card-chart bg-warning-transparent rounded-circle ms-auto mt-0"> <i class="bx bx-time text-warning fs-24"></i> </div>
                            </div>
                            <span class="badge {{ $data['summary']['avg_response_time'] <= 5 ? 'bg-success' : ($data['summary']['avg_response_time'] <= 15 ? 'bg-warning' : 'bg-danger') }}">
                                {{ $data['summary']['avg_response_time'] <= 5 ? 'Excellent' : ($data['summary']['avg_response_time'] <= 15 ? 'Good' : 'Needs Improvement') }}
                            </span>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="row g-4">
                <!-- Conversation Distribution Chart -->
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="bi bi-pie-chart"></i>
                                Conversation Distribution
                            </h5>
                        </div>
                        <div class="chart-body">
                            <canvas id="conversationDistributionChart"></canvas>
                        </div>
                        <div class="chart-legend">
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #4CAF50;"></span>
                                <span class="legend-label">By Agents ({{ number_format($data['overall']['handled_by_agents']) }})</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #2196F3;"></span>
                                <span class="legend-label">By AI ({{ number_format($data['overall']['handled_by_ai']) }})</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Distribution Chart -->
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="bi bi-pie-chart-fill"></i>
                                Message Distribution
                            </h5>
                        </div>
                        <div class="chart-body">
                            <canvas id="messageDistributionChart"></canvas>
                        </div>
                        <div class="chart-legend">
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #4CAF50;"></span>
                                <span class="legend-label">From Agents ({{ number_format($data['overall']['messages']['from_agents']) }})</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #2196F3;"></span>
                                <span class="legend-label">From AI ({{ number_format($data['overall']['messages']['from_ai']) }})</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #FF9800;"></span>
                                <span class="legend-label">From Users ({{ number_format($data['overall']['messages']['from_users']) }})</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agent Performance Chart -->
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h5 class="chart-title">
                                <i class="bi bi-bar-chart"></i>
                                Top 5 Agents by Resolution
                            </h5>
                        </div>
                        <div class="chart-body">
                            <canvas id="topAgentsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agent Performance Table -->
        <div class="card custom-card">
            <div class="card-header">
                <i class="bx bx-table me-2"></i>
                Agent Performance Details
            </div>
            <div class="card-body">
                <table id="provinceData" class="table table-bordered text-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th class="sortable" data-sort="name">
                                Agent

                            </th>
                            <th class="sortable text-center" data-sort="total">
                                Total Conv.

                            </th>
                            <th class="sortable text-center" data-sort="resolved">
                                Resolved

                            </th>
                            <th class="sortable text-center" data-sort="resolution_rate">
                                Resolution %

                            </th>
                            <th class="sortable text-center" data-sort="messages">
                                Messages

                            </th>
                            <th class="sortable text-center" data-sort="avg_messages">
                                Avg/Conv

                            </th>
                            <th class="sortable text-center" data-sort="response_time">
                                Avg Response

                            </th>
                            <th class="sortable text-center" data-sort="engagement">
                                Engagement %

                            </th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['agents'] as $agent)
                        <tr data-agent-name="{{ strtolower($agent['agent_name']) }}">
                            <td>
                                <div class="agent-info">
                                    <img src="{{ $agent['agent_photo'] ?: asset('images/user.png') }}"
                                        alt="{{ $agent['agent_name'] }}"
                                        class="agent-avatar">
                                    <div class="agent-details">
                                        <div class="agent-name">{{ $agent['agent_name'] }}</div>
                                        <div class="agent-email">{{ $agent['agent_email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <strong class="metric-value">{{ number_format($agent['conversations']['total']) }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="metric-value">{{ number_format($agent['conversations']['resolved']) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge metric-badge {{ $agent['conversations']['resolution_rate'] >= 80 ? 'bg-success' : ($agent['conversations']['resolution_rate'] >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ number_format($agent['conversations']['resolution_rate'], 1) }}%
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="metric-value">{{ number_format($agent['messages']['sent']) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="metric-value">{{ number_format($agent['messages']['avg_per_conversation'], 1) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge metric-badge {{ $agent['response_time']['avg_first_response'] <= 5 ? 'bg-success' : ($agent['response_time']['avg_first_response'] <= 15 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ number_format($agent['response_time']['avg_first_response'], 1) }} min
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="engagement-cell">
                                    <span class="metric-value">{{ number_format($agent['engagement']['engagement_rate'], 1) }}%</span>
                                    <div class="progress engagement-progress">
                                        <div class="progress-bar {{ $agent['engagement']['engagement_rate'] >= 90 ? 'bg-success' : ($agent['engagement']['engagement_rate'] >= 70 ? 'bg-warning' : 'bg-danger') }}"
                                            style="width: {{ $agent['engagement']['engagement_rate'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('reports.conversation.agent', $agent['agent_id']) }}?year={{ $selectedYear }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p class="empty-text">Tidak ada data agent untuk periode ini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div> 

    </div>


</div>

@endsection

@section('scripts')
<script src="{{asset('assets/libs/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/js/chart.js')}}"></script>
<script src="{{ asset('assets/js/conversation-rate.js') }}"></script>
<script>

     $(function(e) {
        'use strict';

        $('#provinceData').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: 'Cari Human Agent',
                sSearch: '',
            },
            "pageLength": 10,
        });

    });

    // Initialize charts with data
    const chartData = {
        conversationDistribution: {
            labels: ['Handled by Agents', 'Handled by AI'],
            data: [{{ $data['overall']['handled_by_agents'] }}, {{ $data['overall']['handled_by_ai'] }}],
            colors: ['#4CAF50', '#2196F3']
        },
        messageDistribution: {
            labels: ['From Agents', 'From AI', 'From Users'],
            data: [
                {{ $data['overall']['messages']['from_agents'] }}, 
                {{ $data['overall']['messages']['from_ai'] }}, 
                {{ $data['overall']['messages']['from_users'] }}
            ],
            colors: ['#4CAF50', '#2196F3', '#FF9800']
        },
        topAgents: {
            labels: {!! json_encode(array_slice(array_column($data['agents'], 'agent_name'), 0, 5)) !!},
            data: {!! json_encode(array_slice(array_column(array_column($data['agents'], 'conversations'), 'resolution_rate'), 0, 5)) !!}
        }
    };

    // Initialize charts when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts(chartData);
        initializeTableFeatures();
    });
</script>
@endsection