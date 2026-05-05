<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Reports\ConversationReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationReportController extends Controller
{
    protected $conversationRateService;

    public function __construct(ConversationReportService $conversationRateService)
    {
        $this->conversationRateService = $conversationRateService;
    }

    /**
     * Main dashboard view
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);
            $month = $request->input('month', now()->month);
            $agentId = $request->input('agent_id');

            // Get conversation rate data
            $data = $this->conversationRateService->getConversationRate($year, $month, $agentId);

            // Get list of agents for filter dropdown
            $agents = User::where('role', 'agent')
                ->orWhere('role', 'admin')
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->get();

            // Generate year options (from 2020 to next year)
            $yearOptions = range(now()->year + 1, 2020);

            return view('reports.conversation.index', [
                'data' => $data,
                'selectedYear' => $year,
                'selectedMonth' => $month,
                'selectedAgent' => $agentId,
                'agents' => $agents,
                'yearOptions' => $yearOptions,
                'page'  => 'Conversation Rate Reports'
            ]);
        } catch (\Exception $e) { 
            return back()->with('error', 'Failed to load conversation rate data: ' . $e->getMessage());
        }
    }

    /**
     * Agent detail view
     * 
     * @param Request $request
     * @param string $agentId
     * @return \Illuminate\View\View
     */
    public function agentDetail(Request $request, string $agentId)
    {
        try {
            $year = $request->input('year', now()->year);

            // Get agent info
            $agent = User::findOrFail($agentId);

            // Get yearly comparison
            $yearlyData = $this->conversationRateService->getYearlyComparison($agentId, $year);

            // Get current month detailed data
            $currentMonth = now()->month;
            $monthlyData = $this->conversationRateService->getConversationRate($year, $currentMonth, $agentId);

            return view('reports.conversation.agent', [
                'agent' => $agent,
                'yearlyData' => $yearlyData,
                'monthlyData' => $monthlyData,
                'selectedYear' => $year,
                'page'  => 'Human Agent Performance Detail'
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('error', 'Failed to load agent details: ' . $e->getMessage());
        }
    }

    /**
     * Comparison view - compare multiple agents
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function comparison(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);
            $month = $request->input('month', now()->month);
            $agentIds = $request->input('agent_ids', []);

            // Get all agents for selection
            $allAgents = User::where('role', 'agent')
                ->orWhere('role', 'admin')
                ->select('id', 'name', 'email', 'photo')
                ->orderBy('name')
                ->get();

            $comparisonData = [];

            if (!empty($agentIds)) {
                foreach ($agentIds as $agentId) {
                    $data = $this->conversationRateService->getConversationRate($year, $month, $agentId);
                    if (!empty($data['agents'])) {
                        $comparisonData[] = $data['agents'][0];
                    }
                }
            }

            return view('analytics.conversation-rate.comparison', [
                'allAgents' => $allAgents,
                'comparisonData' => $comparisonData,
                'selectedYear' => $year,
                'selectedMonth' => $month,
                'selectedAgentIds' => $agentIds,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load comparison data: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get data for charts
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getChartData(Request $request): JsonResponse
    {
        try {
            $year = $request->input('year', now()->year);
            $month = $request->input('month', now()->month);
            $agentId = $request->input('agent_id');

            $data = $this->conversationRateService->getConversationRate($year, $month, $agentId);

            // Transform data for charts
            $chartData = [
                'agents' => array_map(function ($agent) {
                    return [
                        'name' => $agent['agent_name'],
                        'total_conversations' => $agent['conversations']['total'],
                        'resolved' => $agent['conversations']['resolved'],
                        'resolution_rate' => $agent['conversations']['resolution_rate'],
                        'messages_sent' => $agent['messages']['sent'],
                        'avg_response_time' => $agent['response_time']['avg_first_response'],
                        'engagement_rate' => $agent['engagement']['engagement_rate'],
                    ];
                }, $data['agents']),
                'overall' => $data['overall'],
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

    /**
     * AJAX: Get agent performance data
     * 
     * @param Request $request
     * @param string $agentId
     * @return JsonResponse
     */
    public function getAgentPerformance(Request $request, string $agentId): JsonResponse
    {
        try {
            $year = $request->input('year', now()->year);
            $month = $request->input('month', now()->month);

            $data = $this->conversationRateService->getConversationRate($year, $month, $agentId);

            return response()->json([
                'success' => true,
                'data' => $data['agents'][0] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get agent performance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print/PDF view
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function print(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);
            $month = $request->input('month', now()->month);
            $agentId = $request->input('agent_id');

            $data = $this->conversationRateService->getConversationRate($year, $month, $agentId);

            return view('reports.conversation-print', [
                'data' => $data,
                'year' => $year,
                'month' => $month,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate print view');
        }
    }
}
