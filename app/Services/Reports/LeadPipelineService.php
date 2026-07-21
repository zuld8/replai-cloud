<?php

namespace App\Services\Reports;

use App\Models\Master\Label;
use App\Models\Master\PipelineSegment;
use App\Models\Store\Store;
use Carbon\Carbon;

class LeadPipelineService
{
    /**
     * Get pipeline analytics
     * 
     * @param string $period 'today', 'month', 'year', or 'custom'
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function getPipelineAnalytics(
        string $period = 'month',
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        // FIX perf: cache 15 menit — query Store 444rb + histories 2.6jt, mahal
        $bid = my_business();
        [$startCalc, $endCalc] = $this->getDateRange($period, $startDate, $endDate);
        $cacheKey = "lead_pipeline_{$bid}_{$period}_{$startCalc->toDateString()}_{$endCalc->toDateString()}";

        return \Cache::remember($cacheKey, 900, function () use ($period, $startDate, $endDate) {
            [$start, $end] = $this->getDateRange($period, $startDate, $endDate);

            // Hitung totalLeads SEKALI (dulu dipanggil 2x)
            $totalLeads    = $this->getTotalLeads($start, $end);
            $leadsByLabel  = $this->getLeadsByLabel($start, $end);
            $closingMetrics = $this->getClosingMetrics($start, $end, $totalLeads);
            $pipelineSegments = $this->getPipelineSegmentsData($start, $end);
            $conversionRates = $this->calculateConversionRates($leadsByLabel, $totalLeads);
            $velocityMetrics = $this->calculateVelocityMetrics($start, $end);

            return [
                'period' => [
                    'type'       => $period,
                    'start_date' => $start->format('Y-m-d'),
                    'end_date'   => $end->format('Y-m-d'),
                    'start_time' => $start->format('Y-m-d H:i:s'),
                    'end_time'   => $end->format('Y-m-d H:i:s'),
                ],
                'summary' => [
                    'total_leads'          => $totalLeads,
                    'closed_leads'         => $closingMetrics['total_closed'],
                    'closing_rate'         => $closingMetrics['closing_rate'],
                    'avg_time_to_close'    => $closingMetrics['avg_time_to_close'],
                    'total_labels'         => count($leadsByLabel),
                    'active_pipeline_value' => $totalLeads - $closingMetrics['total_closed'],
                ],
                'leads_by_label'    => $leadsByLabel,
                'pipeline_segments' => $pipelineSegments,
                'conversion_rates'  => $conversionRates,
                'velocity_metrics'  => $velocityMetrics,
                'closing_metrics'   => $closingMetrics,
                'trends'            => $this->getTrendData($period, $start, $end),
            ];
        });
    }

    /**
     * Get date range based on period
     */
    private function getDateRange(string $period, ?Carbon $startDate, ?Carbon $endDate): array
    {
        switch ($period) {
            case 'today':
                return [
                    now()->startOfDay(),
                    now()->endOfDay()
                ];

            case 'month':
                return [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ];

            case 'year':
                return [
                    now()->startOfYear(),
                    now()->endOfYear()
                ];

            case 'custom':
                return [
                    $startDate ?? now()->startOfMonth(),
                    $endDate ?? now()->endOfMonth()
                ];

            default:
                return [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ];
        }
    }

    /**
     * Get total leads (stores with conversations)
     */
    private function getTotalLeads(Carbon $start, Carbon $end): int
    {
        return Store::whereHas('histories', function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end]);
        })
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /**
     * Get leads breakdown by label
     */
    private function getLeadsByLabel(Carbon $start, Carbon $end): array
    {
        $leadsData = Store::whereHas('histories')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('label_id')
            ->select('label_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('label_id')
            ->get();

        $labelIds = $leadsData->pluck('label_id');
        $labels = Label::with('pipeline')
            ->whereIn('id', $labelIds)
            ->get()
            ->keyBy('id');

        $result = [];
        foreach ($leadsData as $data) {
            $label = $labels->get($data->label_id);
            if ($label) {
                $result[] = [
                    'label_id' => $label->id,
                    'label_name' => $label->name,
                    'label_tag' => $label->tag,
                    'label_color' => $label->color,
                    'pipeline_segment' => $label->pipeline ? $label->pipeline->name : null,
                    'pipeline_segment_id' => $label->pipeline_segment_id,
                    'is_closeable' => $label->is_closeable === 'yes',
                    'total_leads' => $data->total,
                    'position' => $label->position ?? 0,
                ];
            }
        }

        // Sort by position
        usort($result, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        return $result;
    }

    /**
     * Get closing metrics
     */
    private function getClosingMetrics(Carbon $start, Carbon $end, int $totalLeads = 0): array
    {
        $closeableLabels = Label::where('is_closeable', 'yes')->pluck('id')->toArray();

        if (empty($closeableLabels)) {
            return ['total_closed' => 0, 'closing_rate' => 0, 'avg_time_to_close' => 0, 'closed_by_label' => []];
        }

        $totalClosed = Store::whereHas('histories')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('label_id', $closeableLabels)
            ->count();

        // FIX: $closedLeads (variabel tidak terdefinisi) dihapus — avg_time_to_close = 0
        $avgTimeToClose = 0;

        // Closed by label — 1 query grouped (bukan N+1 Label::find per baris)
        $closedByLabel = Store::whereHas('histories')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('label_id', $closeableLabels)
            ->selectRaw('label_id, COUNT(*) as total')
            ->groupBy('label_id')
            ->get();

        $labelMap = Label::whereIn('id', $closeableLabels)->pluck('name', 'id');
        $closedBreakdown = $closedByLabel->map(fn($d) => [
            'label_id'   => $d->label_id,
            'label_name' => $labelMap[$d->label_id] ?? '-',
            'total'      => $d->total,
        ])->all();

        return [
            'total_closed'      => $totalClosed,
            'closing_rate'      => $totalLeads > 0 ? round(($totalClosed / $totalLeads) * 100, 2) : 0,
            'avg_time_to_close' => $avgTimeToClose,
            'closed_by_label'   => $closedBreakdown,
        ];
    }

    /**
     * Get pipeline segments data
     */
    private function getPipelineSegmentsData(Carbon $start, Carbon $end): array
    {
        $segments = PipelineSegment::with(['labels' => fn($q) => $q->orderBy('position')])
            ->orderBy('position')->get();

        // FIX N+1: 1 query untuk semua label counts (bukan 1 query per label)
        $allLabelIds = $segments->flatMap(fn($s) => $s->labels->pluck('id'))->unique()->all();
        $countsByLabel = empty($allLabelIds) ? [] : Store::whereHas('histories')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('label_id', $allLabelIds)
            ->selectRaw('label_id, COUNT(*) as total')
            ->groupBy('label_id')
            ->pluck('total', 'label_id')
            ->all();

        $result = [];
        foreach ($segments as $segment) {
            $segmentLeads = 0;
            $labelsData   = [];
            foreach ($segment->labels as $label) {
                $leadCount     = (int) ($countsByLabel[$label->id] ?? 0);
                $segmentLeads += $leadCount;
                $labelsData[]  = [
                    'label_id'     => $label->id,
                    'label_name'   => $label->name,
                    'label_color'  => $label->color,
                    'total_leads'  => $leadCount,
                    'is_closeable' => $label->is_closeable === 'yes',
                ];
            }
            $result[] = [
                'segment_id'    => $segment->id,
                'segment_name'  => $segment->name,
                'segment_color' => $segment->color,
                'total_leads'   => $segmentLeads,
                'labels'        => $labelsData,
            ];
        }
        return $result;
    }

    /**
     * Calculate conversion rates between labels
     */
    private function calculateConversionRates(array $leadsByLabel, int $totalLeads): array
    {
        if (empty($leadsByLabel) || $totalLeads === 0) {
            return [];
        }

        $conversions = [];

        foreach ($leadsByLabel as $index => $label) {
            $conversionRate = ($label['total_leads'] / $totalLeads) * 100;

            // Calculate drop-off to next stage
            $dropOff = 0;
            if (isset($leadsByLabel[$index + 1])) {
                $nextLabel = $leadsByLabel[$index + 1];
                $dropOff = $label['total_leads'] - $nextLabel['total_leads'];
                $dropOffRate = $label['total_leads'] > 0
                    ? round(($dropOff / $label['total_leads']) * 100, 2)
                    : 0;
            } else {
                $dropOffRate = 0;
            }

            $conversions[] = [
                'label_name' => $label['label_name'],
                'total_leads' => $label['total_leads'],
                'conversion_rate' => round($conversionRate, 2),
                'drop_off' => $dropOff,
                'drop_off_rate' => $dropOffRate,
            ];
        }

        return $conversions;
    }

    /**
     * Calculate velocity metrics (how fast leads move through pipeline)
     */
    private function calculateVelocityMetrics(Carbon $start, Carbon $end): array
    {
        // Get stores with label changes in the period
        $stores = Store::whereHas('histories')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('label_id')
            ->with(['histories' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end])
                    ->orderBy('created_at', 'asc');
            }])
            ->get();

        $velocityData = [];
        $totalDays = [];

        foreach ($stores as $store) {
            if ($store->histories->count() > 0) {
                $firstContact = $store->histories->first()->created_at;
                $lastUpdate = $store->updated_at;

                $daysInPipeline = $firstContact->diffInDays($lastUpdate);
                $totalDays[] = $daysInPipeline;
            }
        }

        $avgDaysInPipeline = !empty($totalDays)
            ? round(array_sum($totalDays) / count($totalDays), 1)
            : 0;

        return [
            'avg_days_in_pipeline' => $avgDaysInPipeline,
            'total_active_leads' => count($totalDays),
            'fastest_close' => !empty($totalDays) ? min($totalDays) : 0,
            'slowest_close' => !empty($totalDays) ? max($totalDays) : 0,
        ];
    }

    /**
     * Get trend data for charts
     */
    private function getTrendData(string $period, Carbon $start, Carbon $end): array
    {
        if ($period === 'today') {
            return $this->getHourlyTrend($start, $end);
        } elseif ($period === 'month') {
            return $this->getDailyTrend($start, $end);
        } elseif ($period === 'year') {
            return $this->getMonthlyTrend($start, $end);
        }

        return $this->getDailyTrend($start, $end);
    }

    /**
     * Get daily trend
     */
    private function getDailyTrend(Carbon $start, Carbon $end): array
    {
        // FIX N+1: 1 query GROUP BY DATE (bukan 1 query per hari)
        $rows = Store::whereHas('histories')
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->all();

        $days = [];
        $current = $start->copy()->startOfDay();
        while ($current->lte($end)) {
            $key    = $current->format('Y-m-d');
            $days[] = ['date' => $key, 'label' => $current->format('d M'), 'total_leads' => (int)($rows[$key] ?? 0)];
            $current->addDay();
        }
        return $days;
    }

    /**
     * Get monthly trend
     */
    private function getMonthlyTrend(Carbon $start, Carbon $end): array
    {
        // FIX N+1: 1 query GROUP BY MONTH (bukan 1 query per bulan)
        $rows = Store::whereHas('histories')
            ->whereBetween('created_at', [$start->copy()->startOfMonth(), $end->copy()->endOfMonth()])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        $months = [];
        $current = $start->copy()->startOfMonth();
        while ($current->lte($end)) {
            $key      = $current->format('Y-m');
            $months[] = ['date' => $key, 'label' => $current->format('M Y'), 'total_leads' => (int)($rows[$key] ?? 0)];
            $current->addMonth();
        }
        return $months;
    }

    /**
     * Get hourly trend (for today)
     */
    private function getHourlyTrend(Carbon $start, Carbon $end): array
    {
        $hours = [];
        $current = $start->copy()->startOfHour();

        while ($current->lte($end)) {
            $hourStart = $current->copy();
            $hourEnd = $current->copy()->addHour()->subSecond();

            $leads = Store::whereHas('histories')
                ->whereBetween('created_at', [$hourStart, $hourEnd])
                ->count();

            $hours[] = [
                'date' => $current->format('Y-m-d H:i'),
                'label' => $current->format('H:00'),
                'total_leads' => $leads,
            ];

            $current->addHour();
        }

        return $hours;
    }

    /**
     * Export pipeline data
     */
    public function exportData(string $period = 'month', ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $data = $this->getPipelineAnalytics($period, $startDate, $endDate);

        $exportData = [];

        // Export leads by label
        foreach ($data['leads_by_label'] as $label) {
            $exportData[] = [
                'Label' => $label['label_name'],
                'Pipeline Segment' => $label['pipeline_segment'] ?? '-',
                'Total Leads' => $label['total_leads'],
                'Is Closeable' => $label['is_closeable'] ? 'Yes' : 'No',
            ];
        }

        return $exportData;
    }
}
