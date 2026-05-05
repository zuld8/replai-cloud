<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\LeadPipelineService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LeadPipelineController extends Controller
{
    protected $pipelineService;

    public function __construct(LeadPipelineService $pipelineService)
    {
        $this->pipelineService = $pipelineService;
    }

    /**
     * Get pipeline analytics (API)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period' => 'nullable|in:today,month,year,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $period = $request->input('period', 'month');
            $startDate = $request->input('start_date') 
                ? Carbon::parse($request->input('start_date')) 
                : null;
            $endDate = $request->input('end_date') 
                ? Carbon::parse($request->input('end_date')) 
                : null;

            $data = $this->pipelineService->getPipelineAnalytics($period, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pipeline analytics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Export pipeline data
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period' => 'nullable|in:today,month,year,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'nullable|in:csv,json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $period = $request->input('period', 'month');
            $startDate = $request->input('start_date') 
                ? Carbon::parse($request->input('start_date')) 
                : null;
            $endDate = $request->input('end_date') 
                ? Carbon::parse($request->input('end_date')) 
                : null;
            $format = $request->input('format', 'csv');

            $exportData = $this->pipelineService->exportData($period, $startDate, $endDate);

            if ($format === 'csv') {
                return $this->exportAsCsv($exportData, $period);
            }

            return response()->json([
                'success' => true,
                'data' => $exportData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Export data as CSV
     */
    private function exportAsCsv(array $data, string $period)
    {
        $filename = sprintf('lead_pipeline_%s_%s.csv', $period, now()->format('Y-m-d'));
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Dashboard view
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        try {
            $period = $request->input('period', 'month');
            $startDate = $request->input('start_date') 
                ? Carbon::parse($request->input('start_date')) 
                : null;
            $endDate = $request->input('end_date') 
                ? Carbon::parse($request->input('end_date')) 
                : null;

            $data = $this->pipelineService->getPipelineAnalytics($period, $startDate, $endDate);

            return view('reports.lead', [
                'data' => $data,
                'selectedPeriod' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'page'      => 'Lead Pipeline Analytics'
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('error', 'Failed to load pipeline analytics: ' . $e->getMessage());
        }
    }

    /**
     * Get chart data (AJAX)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getChartData(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 'month');
            $startDate = $request->input('start_date') 
                ? Carbon::parse($request->input('start_date')) 
                : null;
            $endDate = $request->input('end_date') 
                ? Carbon::parse($request->input('end_date')) 
                : null;

            $data = $this->pipelineService->getPipelineAnalytics($period, $startDate, $endDate);

            // Transform for charts
            $chartData = [
                'funnel' => array_map(function($label) {
                    return [
                        'label' => $label['label_name'],
                        'value' => $label['total_leads'],
                        'color' => $label['label_color'],
                    ];
                }, $data['leads_by_label']),
                
                'segments' => array_map(function($segment) {
                    return [
                        'name' => $segment['segment_name'],
                        'value' => $segment['total_leads'],
                        'color' => $segment['segment_color'],
                    ];
                }, $data['pipeline_segments']),
                
                'trend' => $data['trends'],
                
                'conversion' => $data['conversion_rates'],
            ];

            return response()->json([
                'success' => true,
                'data' => $chartData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get chart data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
