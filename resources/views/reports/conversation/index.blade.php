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

        {{-- 3c: Kartu Selesai oleh Bot (containment) --}}
        @php $ct = $data['overall']['containment'] ?? 0; @endphp
        <div class="row g-4 mt-0 mb-1">
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body iconfont text-start">
                        <div class="d-flex justify-content-between align-items-start">
                            <h4 class="card-title mb-3">
                                Selesai oleh Bot
                                <span class="ms-1" data-bs-toggle="tooltip" title="Percakapan yang beres tanpa perlu diambil-alih agen manusia.">
                                    <i class="bi bi-question-circle text-muted fs-12"></i>
                                </span>
                            </h4>
                        </div>
                        <div class="d-flex mb-2 align-items-end">
                            <h4 class="mb-0 font-weight-bold {{ $ct >= 70 ? 'text-success' : ($ct >= 40 ? 'text-warning' : 'text-danger') }}">
                                {{ number_format($ct, 1) }}%
                            </h4>
                            <div class="card-chart bg-{{ $ct >= 70 ? 'success' : ($ct >= 40 ? 'warning' : 'danger') }}-transparent rounded-circle ms-auto mt-0">
                                <i class="bx bx-bot text-{{ $ct >= 70 ? 'success' : ($ct >= 40 ? 'warning' : 'danger') }} fs-24"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-{{ $ct >= 70 ? 'success' : ($ct >= 40 ? 'warning' : 'danger') }}"
                                 style="width: {{ min(100,$ct) }}%" role="progressbar"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            {{ number_format($data['overall']['handed_off'] ?? 0) }} dari {{ number_format($data['overall']['total_conversations']) }} percakapan perlu agen
                        </small>
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
                                <span class="legend-label">Oleh Agen ({{ number_format($data['overall']['handled_by_agents']) }})</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #2196F3;"></span>
                                <span class="legend-label">Otomatis ({{ number_format($data['overall']['handled_by_ai']) }})</span>
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
                                <span class="legend-label">Dari Agen ({{ number_format($data['overall']['messages']['from_agents']) }})</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #2196F3;"></span>
                                <span class="legend-label">Otomatis ({{ number_format($data['overall']['messages']['from_ai']) }})</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #FF9800;"></span>
                                <span class="legend-label">Dari User ({{ number_format($data['overall']['messages']['from_users']) }})</span>
                            </div>
                        </div>
                        <div class="mt-2 px-1">
                            <small class="text-muted" style="font-size:11px;line-height:1.4">
                                <i class="bi bi-info-circle"></i>
                                'Otomatis' mencakup AI, broadcast, notifikasi &amp; menu.
                                Rinciannya lihat <em>Rincian Balasan Keluar</em> di bawah.
                            </small>
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


        {{-- 3a: Rincian Balasan Keluar --}}
        @php
            $rSources = $data['overall']['reply_sources'] ?? [];
            $rTotal   = max(1, array_sum($rSources));
            $rLabels  = [
                'agen'       => ['Agen (manual)',        '#2E8DE1'],
                'ai'         => ['AI Chatbot',           '#5B3FB0'],
                'menu'       => ['Menu Otomatis',        '#16A34A'],
                'broadcast'  => ['Broadcast',            '#EF9F27'],
                'notifikasi' => ['Notifikasi',           '#64748B'],
                'followup'   => ['Follow-up',            '#06B6D4'],
                'echo'       => ['Dibalas dari HP',      '#94A3B8'],
            ];
        @endphp
        <div class="card custom-card mt-3">
            <div class="card-header">
                <i class="bi bi-send me-2"></i>
                Rincian Balasan Keluar
                <small class="text-muted ms-1">(berdasarkan pengirim)</small>
            </div>
            <div class="card-body">
                @if(empty($rSources))
                    <p class="text-muted text-center py-3">Tidak ada data balasan untuk periode ini.</p>
                @else
                    @foreach($rLabels as $key => [$label, $color])
                        @if(isset($rSources[$key]) && $rSources[$key] > 0)
                        @php $pct = round($rSources[$key] / $rTotal * 100, 1); @endphp
                        <div class="d-flex align-items-center mb-2 gap-2">
                            <div style="width:140px;font-size:13px;color:#334155;white-space:nowrap;">{{ $label }}</div>
                            <div class="flex-grow-1">
                                <div class="progress" style="height:18px;border-radius:6px;background:#F1F5F9;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width:{{ $pct }}%;background:{{ $color }};border-radius:6px;"
                                         aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            <div style="width:120px;text-align:right;font-size:13px;color:#334155;">
                                <strong>{{ number_format($rSources[$key], 0, ',', '.') }}</strong>
                                <span class="text-muted">({{ $pct }}%)</span>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

        {{-- 3b: Percakapan per Channel --}}
        @php
            $channels = $data['overall']['by_channel'] ?? [];
            $chTotal  = max(1, array_sum($channels));
            $chColors = [
                'WhatsApp API' => '#25D366',
                'WA Personal'  => '#128C7E',
                'Instagram'    => '#E1306C',
                'Messenger'    => '#0084FF',
                'Telegram'     => '#2CA5E0',
                'Live Chat'    => '#EF9F27',
                'Lainnya'      => '#94A3B8',
            ];
        @endphp
        <div class="card custom-card mt-3">
            <div class="card-header">
                <i class="bi bi-diagram-3 me-2"></i>
                Percakapan per Channel
            </div>
            <div class="card-body">
                @if(empty($channels) || array_sum($channels) == 0)
                    <p class="text-muted text-center py-3">Tidak ada data channel untuk periode ini.</p>
                @else
                    @foreach($channels as $ch => $cnt)
                    @if($cnt > 0)
                    @php $pct = round($cnt / $chTotal * 100, 1); $col = $chColors[$ch] ?? '#94A3B8'; @endphp
                    <div class="d-flex align-items-center mb-2 gap-2">
                        <div style="width:140px;font-size:13px;color:#334155;white-space:nowrap;">{{ $ch }}</div>
                        <div class="flex-grow-1">
                            <div class="progress" style="height:18px;border-radius:6px;background:#F1F5F9;">
                                <div class="progress-bar" role="progressbar"
                                     style="width:{{ $pct }}%;background:{{ $col }};border-radius:6px;"
                                     aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        <div style="width:120px;text-align:right;font-size:13px;color:#334155;">
                            <strong>{{ number_format($cnt, 0, ',', '.') }}</strong>
                            <span class="text-muted">({{ $pct }}%)</span>
                        </div>
                    </div>
                    @endif
                    @endforeach
                @endif
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
                            {{-- Engagement disembunyikan: selalu 100% (gak informatif untuk 1 agen) --}}
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
                            {{-- Engagement TD disembunyikan --}}
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
                            <td colspan="8" class="text-center py-5">
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
            labels: ['Oleh Agen', 'Oleh Bot/AI'],
            data: [{{ $data['overall']['handled_by_agents'] }}, {{ $data['overall']['handled_by_ai'] }}],
            colors: ['#4CAF50', '#2196F3']
        },
        messageDistribution: {
            labels: ['Dari Agen', 'Otomatis (bot/broadcast/dll)', 'Dari User'],
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