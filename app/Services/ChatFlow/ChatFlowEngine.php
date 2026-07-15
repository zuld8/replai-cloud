<?php

namespace App\Services\ChatFlow;

use App\Models\ChatFlow\ChatFlow;
use App\Models\ChatFlow\ChatFlowNode;
use App\Models\ChatFlow\ChatFlowOption;
use App\Models\ChatFlow\ChatFlowSession;
use App\Models\WhatsappKeyAccount;
use App\Services\Sistem\WabaNotificationService;
use Illuminate\Support\Facades\Log;

/**
 * ChatFlowEngine — Engine runtime Menu Otomatis (WhatsApp Interactive).
 *
 * ⚠️ PENTING — webhook context (tidak ada session login):
 *   - my_business() = null → FilterByBusinessScope jadi no-op.
 *   - SEMUA query model wajib pakai withoutGlobalScopes() + where('business_id', ...) eksplisit.
 *   - Create ChatFlowSession wajib set business_id eksplisit dari $history->business_id.
 *
 * Prioritas: Menu Otomatis > AI Agent > Manual Reply
 * handle() return true = engine menangani pesan → caller skip processAutoReplies.
 */
class ChatFlowEngine
{
    public function __construct(
        private WabaNotificationService $notif
    ) {}

    /**
     * Entry point — dipanggil dari WabaCallbackController sebelum processAutoReplies.
     *
     * @param  WhatsappKeyAccount  $device      WABA device
     * @param  mixed               $history     HistoryChat instance
     * @param  array               $rawMessage  Payload mentah dari Meta webhook
     * @param  string              $messageText Teks pesan (dari parseMessageContent)
     * @return bool  true = engine handle → caller skip auto-reply
     */
    public function handle(
        WhatsappKeyAccount $device,
        $history,
        array $rawMessage,
        string $messageText
    ): bool {
        try {
            // 1. Skip jika agen sudah takeover — biarkan agen handle
            if (($history->takeover ?? 'no') === 'yes') {
                return false;
            }

            $businessId = $device->business_id;

            // 2. Ambil reply_id dari balasan tombol/list (routing by ID, BUKAN title)
            $replyId = $rawMessage['interactive']['button_reply']['id']
                     ?? $rawMessage['interactive']['list_reply']['id'] ?? null;

            // 3A. Balasan interaktif (customer klik tombol/list)
            if ($replyId) {
                $option = ChatFlowOption::where('reply_id', $replyId)->first();
                if (!$option) return false;

                // Verifikasi option milik flow bisnis ini (tenant isolation)
                $node = ChatFlowNode::find($option->node_id);
                if (!$node) return false;

                $flow = ChatFlow::withoutGlobalScopes()
                    ->where('id', $node->flow_id)
                    ->where('business_id', $businessId)
                    ->first();
                if (!$flow) return false;

                $session = $this->getOrCreateSession($history, $flow, $node->id, $businessId);
                return $this->runOption($option, $device, $history, $session, $flow);
            }

            // 3B. Teks biasa — cek session aktif dulu
            $session = ChatFlowSession::withoutGlobalScopes()
                ->where('history_chat_id', $history->id)
                ->where('business_id', $businessId)
                ->where('status', 'active')
                ->first();

            if ($session) {
                $currentNode = ChatFlowNode::find($session->current_node_id);
                if ($currentNode) {
                    // Cocokin teks ke label option node sekarang (user ngetik bukan klik)
                    $option = $currentNode->options
                        ->first(fn($o) => strtolower(trim($o->label)) === strtolower(trim($messageText)));

                    if ($option) {
                        $flow = ChatFlow::withoutGlobalScopes()
                            ->where('id', $session->flow_id)
                            ->where('business_id', $businessId)
                            ->first();
                        return $this->runOption($option, $device, $history, $session, $flow);
                    }
                }
                // Teks tidak cocok → fallback
                return $this->handleFallback($session, $device, $history, $businessId);
            }

            // 3C. Tidak ada session aktif — cari trigger flow yang cocok
            $flow = $this->findTriggerFlow($businessId, $device, $messageText);
            if ($flow) {
                return $this->startFlow($flow, $device, $history, $businessId);
            }

            return false; // tidak ada yang cocok → lolos ke processAutoReplies

        } catch (\Exception $e) {
            Log::error('[ChatFlowEngine] Error: ' . $e->getMessage(), [
                'history_id' => $history->id ?? null,
                'device_id'  => $device->id ?? null,
                'trace'      => $e->getTraceAsString(),
            ]);
            return false; // error → fallback ke sistem lama, jangan crash webhook
        }
    }

    // ─── Private: Flow Trigger ──────────────────────────────────────────────

    /**
     * Cari flow aktif yang trigger-nya cocok dengan pesan masuk.
     */
    private function findTriggerFlow(string $businessId, WhatsappKeyAccount $device, string $messageText): ?ChatFlow
    {
        $flows = ChatFlow::withoutGlobalScopes()
            ->where('business_id', $businessId)
            ->where('status', 'active')
            ->get();

        $lower = strtolower(trim($messageText));

        // Priority 1: keyword match
        foreach ($flows as $flow) {
            if ($flow->trigger_type === 'keyword' && $this->channelMatches($flow, $device)) {
                foreach ($flow->trigger_keywords ?? [] as $kw) {
                    if (strtolower(trim($kw)) === $lower) {
                        return $flow;
                    }
                }
            }
        }

        // Priority 2: default flow (cocok semua pesan)
        foreach ($flows as $flow) {
            if ($flow->trigger_type === 'default' && $this->channelMatches($flow, $device)) {
                return $flow;
            }
        }

        return null;
    }

    /**
     * Cek apakah flow berlaku untuk device/channel ini.
     * channels kosong = berlaku untuk semua device bisnis ini.
     */
    private function channelMatches(ChatFlow $flow, WhatsappKeyAccount $device): bool
    {
        $channels = $flow->channels ?? [];
        return empty($channels) || in_array((string) $device->id, array_map('strval', $channels));
    }

    // ─── Private: Flow Execution ────────────────────────────────────────────

    /**
     * Mulai flow: buat session + kirim start node.
     */
    private function startFlow(ChatFlow $flow, WhatsappKeyAccount $device, $history, string $businessId): bool
    {
        if (!$flow->start_node_id) return false;
        $startNode = ChatFlowNode::find($flow->start_node_id);
        if (!$startNode) return false;

        // ⚠️ Set business_id eksplisit — webhook: my_business() = null
        ChatFlowSession::withoutGlobalScopes()->create([
            'business_id'     => $businessId,
            'history_chat_id' => $history->id,
            'flow_id'         => $flow->id,
            'current_node_id' => $startNode->id,
            'status'          => 'active',
            'last_activity_at'=> now(),
        ]);

        return $this->sendNode($startNode, $device, $history);
    }

    /**
     * Jalankan aksi option: goto_node / handoff / back_to_start / end.
     */
    private function runOption(
        ChatFlowOption $option,
        WhatsappKeyAccount $device,
        $history,
        ChatFlowSession $session,
        ?ChatFlow $flow = null
    ): bool {
        switch ($option->target_action) {
            case 'goto_node':
                $target = ChatFlowNode::find($option->target_node_id);
                if (!$target) return false;
                $session->update(['current_node_id' => $target->id, 'last_activity_at' => now()]);
                return $this->sendNode($target, $device, $history, $session);

            case 'handoff':
                // Cari handoff node jika ada (untuk pesan + assign target)
                $handoffNode = $option->target_node_id ? ChatFlowNode::find($option->target_node_id) : null;
                return $this->doHandoff($device, $history, $session, $handoffNode);

            case 'back_to_start':
                if (!$flow) $flow = ChatFlow::withoutGlobalScopes()->find($session->flow_id);
                if (!$flow || !$flow->start_node_id) return false;
                $startNode = ChatFlowNode::find($flow->start_node_id);
                if (!$startNode) return false;
                $session->update(['current_node_id' => $startNode->id, 'last_activity_at' => now()]);
                return $this->sendNode($startNode, $device, $history, $session);

            case 'end':
                $session->update(['status' => 'ended', 'last_activity_at' => now()]);
                return true;

            default:
                return false;
        }
    }

    /**
     * Kirim satu node ke customer.
     */
    private function sendNode(
        ChatFlowNode $node,
        WhatsappKeyAccount $device,
        $history,
        ?ChatFlowSession $session = null
    ): bool {
        if ($session) {
            $session->update(['last_activity_at' => now()]);
        }

        if ($node->type === 'handoff') {
            return $this->doHandoff($device, $history, $session, $node);
        }

        if ($node->type === 'message') {
            return $this->sendTextNode($node, $device, $history);
        }

        if (in_array($node->type, ['buttons', 'list'])) {
            return $this->sendInteractiveNode($node, $node->options, $device, $history);
        }

        return false;
    }

    /**
     * Kirim node tipe "message" (teks biasa).
     */
    private function sendTextNode(ChatFlowNode $node, WhatsappKeyAccount $device, $history): bool
    {
        if (!$node->body_text) return false;

        // Simpan ke CRM timeline (from=device, source=bot)
        $history->details()->create([
            'history_chat_id' => $history->id,
            'from'            => 'device',
            'source'          => 'bot',
            'message'         => $node->body_text,
        ]);

        // Kirim ke customer via WABA
        $this->notif->sendText($history->from_number, $node->body_text, $device, $history->bsuid ?? null);

        return true;
    }

    /**
     * Kirim node tipe "buttons" atau "list".
     */
    private function sendInteractiveNode(
        ChatFlowNode $node,
        $options,
        WhatsappKeyAccount $device,
        $history
    ): bool {
        // Simpan preview ke CRM (tampil sebagai teks di timeline)
        $preview = ($node->type === 'buttons' ? '[Tombol] ' : '[Menu] ') . $node->body_text;
        $history->details()->create([
            'history_chat_id' => $history->id,
            'from'            => 'device',
            'source'          => 'bot',
            'message'         => $preview,
        ]);

        // Kirim interactive ke customer
        return $this->notif->sendInteractive(
            $history->from_number,
            $node,
            $options,
            $device,
            $history->bsuid ?? null
        );
    }

    /**
     * Fallback saat input tidak cocok option mana pun.
     */
    private function handleFallback(
        ChatFlowSession $session,
        WhatsappKeyAccount $device,
        $history,
        string $businessId
    ): bool {
        $flow = ChatFlow::withoutGlobalScopes()
            ->where('id', $session->flow_id)
            ->where('business_id', $businessId)
            ->first();

        if (!$flow) return false;

        switch ($flow->fallback_action) {
            case 'repeat_menu':
                $currentNode = ChatFlowNode::find($session->current_node_id);
                if ($currentNode) {
                    return $this->sendNode($currentNode, $device, $history, $session);
                }
                return false;

            case 'ai_agent':
            case 'manual_reply':
            default:
                // Return false → lolos ke processAutoReplies (AI/Manual)
                return false;
        }
    }

    /**
     * Handoff ke agen: set takeover=yes, assign, stop bot.
     * Reuse mekanisme takeover CRM.
     */
    private function doHandoff(
        WhatsappKeyAccount $device,
        $history,
        ?ChatFlowSession $session,
        ?ChatFlowNode $node = null
    ): bool {
        // Kirim pesan handoff ke customer jika ada
        if ($node && $node->body_text) {
            $history->details()->create([
                'history_chat_id' => $history->id,
                'from'            => 'device',
                'source'          => 'bot',
                'message'         => $node->body_text,
            ]);
            $this->notif->sendText($history->from_number, $node->body_text, $device, $history->bsuid ?? null);
        }

        // Update session status
        if ($session) {
            $session->update(['status' => 'handoff', 'last_activity_at' => now()]);
        }

        // Set takeover=yes + assign ke agen/tim (reuse mekanisme CRM)
        $update = ['takeover' => 'yes'];
        if ($node && $node->handoff_assign_to) {
            $update['handled_by']    = $node->handoff_assign_to;
            $update['assigned_by']   = $node->handoff_assign_to;
            $update['assignment_at'] = now();
        }
        $history->update($update);

        return true;
    }

    /**
     * Ambil session aktif atau buat baru (dipakai routing by reply_id).
     */
    private function getOrCreateSession($history, ChatFlow $flow, string $nodeId, string $businessId): ChatFlowSession
    {
        $session = ChatFlowSession::withoutGlobalScopes()
            ->where('history_chat_id', $history->id)
            ->where('business_id', $businessId)
            ->where('status', 'active')
            ->first();

        if ($session) {
            $session->update(['current_node_id' => $nodeId, 'last_activity_at' => now()]);
            return $session;
        }

        // ⚠️ Set business_id eksplisit (webhook: my_business() = null)
        return ChatFlowSession::withoutGlobalScopes()->create([
            'business_id'     => $businessId,
            'history_chat_id' => $history->id,
            'flow_id'         => $flow->id,
            'current_node_id' => $nodeId,
            'status'          => 'active',
            'last_activity_at'=> now(),
        ]);
    }
}
