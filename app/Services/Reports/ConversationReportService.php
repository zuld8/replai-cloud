<?php

namespace App\Services\Reports;

use App\Models\ChatBot\HistoryChat;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConversationReportService
{
    /**
     * Get conversation rate metrics per agent
     * 
     * @param int|null $year
     * @param int|null $month
     * @param string|null $agentId
     * @return array
     */
    public function getConversationRate(?int $year = null, ?int $month = null, ?string $agentId = null): array
    {
        // Set default to current month if no parameters provided
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        // Build date range
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Cache key based on all parameters
        $cacheKey = 'conv_report_' . ($businessId ?? 'all') . '_' . $year . '_' . $month . '_' . ($agentId ?? 'all');
        if ($cached = \Illuminate\Support\Facades\Cache::get($cacheKey)) {
            return $cached;
        }

        // Get agent metrics
        $agentMetrics = $this->calculateAgentMetrics($startDate, $endDate, $agentId);

        // Get overall statistics
        $overallStats = $this->calculateOverallStats($startDate, $endDate);

        $result = [
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'agents' => $agentMetrics,
            'overall' => $overallStats,
            'summary' => $this->generateSummary($agentMetrics)
        ];

        // Cache for 5 minutes (reports are read-heavy)
        \Illuminate\Support\Facades\Cache::put($cacheKey, $result, 300);
        return $result;
    }

    /**
     * Calculate metrics per agent
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string|null $agentId
     * @return array
     */
    private function calculateAgentMetrics(Carbon $startDate, Carbon $endDate, ?string $agentId = null): array
    {
        $businessId = my_business();
        $query = HistoryChat::query()
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($agentId, function ($q) use ($agentId) {
                $q->where('handled_by', $agentId);
            });

        // Get conversations handled by each agent
        $conversationsByAgent = (clone $query)
            ->select('handled_by')
            ->whereNotNull('handled_by')
            ->groupBy('handled_by')
            ->selectRaw('COUNT(*) as total_conversations')
            ->selectRaw('COUNT(CASE WHEN status = "resolved" THEN 1 END) as resolved_conversations')
            ->selectRaw('COUNT(CASE WHEN status = "open" THEN 1 END) as open_conversations')
            ->get()
            ->keyBy('handled_by');

        // Get message statistics per agent using efficient query
        $messageStats = HistoryChatDetail::query()
            ->join('history_chats', 'history_chat_details.history_chat_id', '=', 'history_chats.id')
            ->whereBetween('history_chat_details.created_at', [$startDate, $endDate])
            ->whereNotNull('history_chats.handled_by')
            ->whereNotNull('history_chat_details.reply_by_id') // Only human agent messages
            ->where('history_chat_details.from', 'device')
            ->when($businessId, fn($q) => $q->where('history_chats.business_id', $businessId))
            ->when($agentId, function ($q) use ($agentId) {
                $q->where('history_chats.handled_by', $agentId);
            })
            ->select('history_chats.handled_by')
            ->groupBy('history_chats.handled_by')
            ->selectRaw('COUNT(*) as total_messages_sent')
            ->selectRaw('COUNT(DISTINCT history_chat_details.history_chat_id) as conversations_with_replies')
            ->get()
            ->keyBy('handled_by');

        // Get response time metrics
        $responseTimeStats = $this->calculateResponseTime($startDate, $endDate, $agentId);

        // Get agent details
        $agentIds = $conversationsByAgent->keys()
            ->merge($messageStats->keys())
            ->unique()
            ->values();

        $agents = User::whereIn('id', $agentIds)
            ->select('id', 'name', 'email', 'photo')
            ->get()
            ->keyBy('id');

        // Combine all metrics
        $results = [];
        foreach ($agentIds as $id) {
            $conversation = $conversationsByAgent->get($id);
            $messages = $messageStats->get($id);
            $responseTimes = $responseTimeStats->get($id, []);

            $totalConversations = $conversation->total_conversations ?? 0;
            $resolvedConversations = $conversation->resolved_conversations ?? 0;
            $messagesSent = $messages->total_messages_sent ?? 0;

            $results[] = [
                'agent_id' => $id,
                'agent_name' => $agents->get($id)?->name ?? 'Unknown',
                'agent_email' => $agents->get($id)?->email ?? '',
                'agent_photo' => asset($agents->get($id)?->image_data ?? ''),
                'conversations' => [
                    'total' => $totalConversations,
                    'resolved' => $resolvedConversations,
                    'open' => $conversation->open_conversations ?? 0,
                    'resolution_rate' => $totalConversations > 0
                        ? round(($resolvedConversations / $totalConversations) * 100, 2)
                        : 0,
                ],
                'messages' => [
                    'sent' => $messagesSent,
                    'avg_per_conversation' => $totalConversations > 0
                        ? round($messagesSent / $totalConversations, 2)
                        : 0,
                ],
                'response_time' => [
                    'avg_first_response' => $responseTimes['avg_first_response'] ?? 0,
                    'avg_response' => $responseTimes['avg_response'] ?? 0,
                    'first_response_rate' => $responseTimes['first_response_rate'] ?? 0,
                ],
                'engagement' => [
                    'conversations_with_replies' => $messages->conversations_with_replies ?? 0,
                    'engagement_rate' => $totalConversations > 0
                        ? round((($messages->conversations_with_replies ?? 0) / $totalConversations) * 100, 2)
                        : 0,
                ]
            ];
        }

        // Sort by total conversations descending
        usort($results, function ($a, $b) {
            return $b['conversations']['total'] <=> $a['conversations']['total'];
        });

        return $results;
    }

    /**
     * Calculate response time metrics for agents
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string|null $agentId
     * @return \Illuminate\Support\Collection
     */
    private function calculateResponseTime(Carbon $startDate, Carbon $endDate, ?string $agentId = null)
    {
        // Step 1: Get first user message per conversation
        $firstUserMessages = DB::table('history_chats as hc')
            ->join('history_chat_details as hcd', 'hc.id', '=', 'hcd.history_chat_id')
            ->whereBetween('hc.created_at', [$startDate, $endDate])
            ->whereNotNull('hc.handled_by')
            ->where('hcd.from', '=', 'user')
            ->when($agentId, function ($q) use ($agentId) {
                $q->where('hc.handled_by', $agentId);
            })
            ->select('hc.id as conversation_id', 'hc.handled_by')
            ->selectRaw('MIN(hcd.created_at) as first_user_message_time')
            ->groupBy('hc.id', 'hc.handled_by')
            ->get()
            ->keyBy('conversation_id');

        // Step 2: Get first agent response per conversation
        $firstAgentResponses = DB::table('history_chats as hc')
            ->join('history_chat_details as hcd', 'hc.id', '=', 'hcd.history_chat_id')
            ->whereBetween('hc.created_at', [$startDate, $endDate])
            ->whereNotNull('hc.handled_by')
            ->where('hcd.from', '=', 'device')
            ->whereNotNull('hcd.reply_by_id')
            ->when($agentId, function ($q) use ($agentId) {
                $q->where('hc.handled_by', $agentId);
            })
            ->select('hc.id as conversation_id', 'hc.handled_by')
            ->selectRaw('MIN(hcd.created_at) as first_agent_response_time')
            ->groupBy('hc.id', 'hc.handled_by')
            ->get()
            ->keyBy('conversation_id');

        // Step 3: Combine and calculate response times
        $responseTimes = collect();

        foreach ($firstUserMessages as $conversationId => $userMsg) {
            if (isset($firstAgentResponses[$conversationId])) {
                $agentMsg = $firstAgentResponses[$conversationId];

                // Only count if agent responded AFTER user message
                $userTime = Carbon::parse($userMsg->first_user_message_time);
                $agentTime = Carbon::parse($agentMsg->first_agent_response_time);

                if ($agentTime->greaterThan($userTime)) {
                    $responseTimes->push([
                        'handled_by' => $userMsg->handled_by,
                        'conversation_id' => $conversationId,
                        'response_time_minutes' => $agentTime->diffInMinutes($userTime),
                    ]);
                }
            }
        }

        // Step 4: Calculate metrics per agent
        $stats = $responseTimes->groupBy('handled_by')->map(function ($responses) {
            $times = $responses->pluck('response_time_minutes');
            $totalConversations = $responses->count();

            return [
                'avg_first_response' => $totalConversations > 0
                    ? round($times->avg(), 2)
                    : 0,
                'avg_response' => $totalConversations > 0
                    ? round($times->avg(), 2)
                    : 0,
                'first_response_rate' => $totalConversations > 0
                    ? 100.0  // All conversations in this collection have responses
                    : 0,
            ];
        });

        return $stats;
    }

    /**
     * Calculate overall statistics for the period
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function calculateOverallStats(Carbon $startDate, Carbon $endDate): array
    {
        // Total conversations
        $totalConversations = HistoryChat::whereBetween('created_at', [$startDate, $endDate])->count();

        // Handled by agents
        $handledByAgents = HistoryChat::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('handled_by')
            ->count();

        // Handled by AI only
        $handledByAI = HistoryChat::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('handled_by')
            ->count();

        // Messages sent by agents (human)
        $agentMessages = HistoryChatDetail::whereBetween('created_at', [$startDate, $endDate])
            ->where('from', 'device')
            ->whereNotNull('reply_by_id')
            ->count();

        // Messages sent by AI
        $aiMessages = HistoryChatDetail::whereBetween('created_at', [$startDate, $endDate])
            ->where('from', 'device')
            ->whereNull('reply_by_id')
            ->count();

        // Total messages from users
        $userMessages = HistoryChatDetail::whereBetween('created_at', [$startDate, $endDate])
            ->where('from', 'user')
            ->count();

        return [
            'total_conversations' => $totalConversations,
            'handled_by_agents' => $handledByAgents,
            'handled_by_ai' => $handledByAI,
            'agent_coverage' => $totalConversations > 0
                ? round(($handledByAgents / $totalConversations) * 100, 2)
                : 0,
            'messages' => [
                'from_agents' => $agentMessages,
                'from_ai' => $aiMessages,
                'from_users' => $userMessages,
                'total_outbound' => $agentMessages + $aiMessages,
            ]
        ];
    }

    /**
     * Generate summary insights
     * 
     * @param array $agentMetrics
     * @return array
     */
    private function generateSummary(array $agentMetrics): array
    {
        if (empty($agentMetrics)) {
            return [
                'total_agents' => 0,
                'top_performer' => null,
                'avg_resolution_rate' => 0,
                'avg_response_time' => 0,
            ];
        }

        $resolutionRates = array_column(array_column($agentMetrics, 'conversations'), 'resolution_rate');
        $responseTimes = array_column(array_column($agentMetrics, 'response_time'), 'avg_first_response');

        // Find top performer by resolution rate
        $topPerformer = collect($agentMetrics)->sortByDesc(function ($agent) {
            return $agent['conversations']['resolution_rate'];
        })->first();

        return [
            'total_agents' => count($agentMetrics),
            'top_performer' => [
                'name' => $topPerformer['agent_name'] ?? null,
                'resolution_rate' => $topPerformer['conversations']['resolution_rate'] ?? 0,
            ],
            'avg_resolution_rate' => count($resolutionRates) > 0
                ? round(array_sum($resolutionRates) / count($resolutionRates), 2)
                : 0,
            'avg_response_time' => count($responseTimes) > 0
                ? round(array_sum($responseTimes) / count($responseTimes), 2)
                : 0,
        ];
    }

    /**
     * Get yearly comparison for an agent
     * 
     * @param string $agentId
     * @param int|null $year
     * @return array
     */
    public function getYearlyComparison(string $agentId, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $data = $this->getConversationRate($year, $month, $agentId);

            $agentData = collect($data['agents'])->firstWhere('agent_id', $agentId);

            $monthlyData[] = [
                'month' => $month,
                'month_name' => Carbon::create($year, $month, 1)->format('F'),
                'metrics' => $agentData ?? [
                    'conversations' => ['total' => 0, 'resolved' => 0],
                    'messages' => ['sent' => 0],
                    'response_time' => ['avg_first_response' => 0],
                ],
            ];
        }

        return [
            'year' => $year,
            'agent_id' => $agentId,
            'monthly_data' => $monthlyData,
        ];
    }

    /**
     * Export conversation rate data
     * 
     * @param int|null $year
     * @param int|null $month
     * @return array
     */
    public function exportData(?int $year = null, ?int $month = null): array
    {
        $data = $this->getConversationRate($year, $month);

        // Transform for export (CSV/Excel friendly)
        $exportData = [];
        foreach ($data['agents'] as $agent) {
            $exportData[] = [
                'Agent Name' => $agent['agent_name'],
                'Agent Email' => $agent['agent_email'],
                'Total Conversations' => $agent['conversations']['total'],
                'Resolved Conversations' => $agent['conversations']['resolved'],
                'Open Conversations' => $agent['conversations']['open'],
                'Resolution Rate (%)' => $agent['conversations']['resolution_rate'],
                'Messages Sent' => $agent['messages']['sent'],
                'Avg Messages Per Conversation' => $agent['messages']['avg_per_conversation'],
                'Avg First Response Time (minutes)' => $agent['response_time']['avg_first_response'],
                'Engagement Rate (%)' => $agent['engagement']['engagement_rate'],
            ];
        }

        return $exportData;
    }
}
