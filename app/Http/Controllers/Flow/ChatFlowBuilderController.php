<?php
namespace App\Http\Controllers\Flow;

use App\Http\Controllers\Controller;
use App\Models\ChatFlow\ChatFlow;
use App\Models\ChatFlow\ChatFlowNode;
use App\Models\ChatFlow\ChatFlowOption;
use App\Models\ChatFlow\ChatFlowSession;
use App\Models\WhatsappKeyAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatFlowBuilderController extends Controller
{
    public function index()
    {
        $flows = ChatFlow::withCount('nodes')
            ->orderByDesc('created_at')->get();
        return view('menu_otomatis.index', compact('flows'));
    }

    public function create()
    {
        $devices = WhatsappKeyAccount::where('business_id', my_business()->id ?? null)
            ->where('status', 'active')->get(['id', 'phone']);
        return view('menu_otomatis.builder', ['flowId' => null, 'devices' => $devices]);
    }

    public function edit(ChatFlow $chatFlow)
    {
        $devices = WhatsappKeyAccount::where('business_id', $chatFlow->business_id)
            ->where('status', 'active')->get(['id', 'phone']);
        return view('menu_otomatis.builder', ['flowId' => $chatFlow->id, 'devices' => $devices]);
    }

    public function data(ChatFlow $chatFlow)
    {
        $chatFlow->load(['nodes.options']);
        $devices = WhatsappKeyAccount::where('business_id', $chatFlow->business_id)
            ->where('status', 'active')->get(['id', 'phone']);
        return response()->json(['flow' => $chatFlow, 'devices' => $devices]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'id'                           => 'nullable|uuid',
            'name'                         => 'required|string|max:120',
            'trigger_type'                 => 'required|in:keyword,welcome,default',
            'keyword_match'                => 'required|in:exact,contains',
            'trigger_keywords'             => 'nullable|array',
            'trigger_keywords.*'           => 'string|max:60',
            'channels'                     => 'nullable|array',
            'fallback_action'              => 'required|in:ai_agent,manual_reply,repeat_menu',
            'session_timeout_min'          => 'required|integer|min:1|max:1440',
            'status'                       => 'required|in:active,inactive',
            'start_temp_id'                => 'required|string',
            'nodes'                        => 'required|array|min:1',
            'nodes.*.temp_id'              => 'required|string',
            'nodes.*.type'                 => 'required|in:message,buttons,list,handoff',
            'nodes.*.body_text'            => 'nullable|string|max:1024',
            'nodes.*.header'               => 'nullable|string|max:60',
            'nodes.*.footer'               => 'nullable|string|max:60',
            'nodes.*.list_button_label'    => 'nullable|string|max:20',
            'nodes.*.position'             => 'required|integer',
            'nodes.*.options'              => 'nullable|array',
            'nodes.*.options.*.kind'           => 'required|in:button,list_row',
            'nodes.*.options.*.label'          => 'required|string|max:24',
            'nodes.*.options.*.description'    => 'nullable|string|max:72',
            'nodes.*.options.*.order'          => 'required|integer',
            'nodes.*.options.*.target_action'  => 'required|in:goto_node,handoff,back_to_start,end',
            'nodes.*.options.*.target_temp_id' => 'nullable|string',
        ]);

        // Server-side guardrail batas WA
        foreach ($data['nodes'] as $n) {
            $opts = $n['options'] ?? [];
            if ($n['type'] === 'buttons' && count($opts) > 3)  abort(422, 'Node tombol maksimal 3 pilihan.');
            if ($n['type'] === 'list'    && count($opts) > 10) abort(422, 'Node daftar maksimal 10 pilihan.');
            foreach ($opts as $o) {
                $max = $o['kind'] === 'button' ? 20 : 24;
                if (mb_strlen($o['label']) > $max) {
                    abort(422, 'Label "' . $o['label'] . '" terlalu panjang (maks ' . $max . ' karakter).');
                }
            }
        }

        $businessId = my_business()->id ?? null;
        if (!$businessId) abort(403, 'Business tidak ditemukan.');

        return DB::transaction(function () use ($data, $businessId) {
            // 1. Upsert flow
            $flow = ChatFlow::firstOrNew(['id' => $data['id'] ?? null]);
            if ($flow->exists && $flow->business_id !== $businessId) abort(403);

            $flow->fill([
                'business_id'         => $businessId,
                'merchant_id'         => my_business()->merchant_id ?? null,
                'name'                => $data['name'],
                'trigger_type'        => $data['trigger_type'],
                'keyword_match'       => $data['keyword_match'],
                'trigger_keywords'    => $data['trigger_type'] === 'keyword'
                    ? array_values(array_filter($data['trigger_keywords'] ?? []))
                    : null,
                'channels'            => $data['channels'] ?? [],
                'fallback_action'     => $data['fallback_action'],
                'session_timeout_min' => $data['session_timeout_min'],
                'status'              => $data['status'],
            ]);
            $flow->save();

            // 2. Hapus node+option lama + reset sesi
            $oldNodeIds = ChatFlowNode::where('flow_id', $flow->id)->pluck('id');
            ChatFlowOption::whereIn('node_id', $oldNodeIds)->delete();
            ChatFlowNode::where('flow_id', $flow->id)->delete();
            ChatFlowSession::where('flow_id', $flow->id)->update(['status' => 'ended']);

            // 3. Pass A — insert nodes, bangun map tempId -> UUID
            $map = [];
            foreach ($data['nodes'] as $n) {
                $node = ChatFlowNode::create([
                    'flow_id'           => $flow->id,
                    'type'              => $n['type'],
                    'body_text'         => $n['body_text'] ?? null,
                    'header'            => $n['header'] ?? null,
                    'footer'            => $n['footer'] ?? null,
                    'list_button_label' => $n['type'] === 'list' ? ($n['list_button_label'] ?: 'Pilih') : null,
                    'position'          => $n['position'],
                ]);
                $map[$n['temp_id']] = $node->id;
            }

            // 4. Pass B — insert options
            foreach ($data['nodes'] as $n) {
                foreach (($n['options'] ?? []) as $o) {
                    $optId  = \Ramsey\Uuid\Uuid::uuid4()->toString();
                    $target = $o['target_action'] === 'goto_node' ? ($map[$o['target_temp_id'] ?? ''] ?? null) : null;
                    ChatFlowOption::create([
                        'id'             => $optId,
                        'node_id'        => $map[$n['temp_id']],
                        'kind'           => $o['kind'],
                        'label'          => mb_substr($o['label'], 0, $o['kind'] === 'button' ? 20 : 24),
                        'description'    => ($o['kind'] === 'list_row' && !empty($o['description']))
                            ? mb_substr($o['description'], 0, 72) : null,
                        'section'        => null,
                        'order'          => $o['order'],
                        'target_action'  => $o['target_action'],
                        'target_node_id' => $target,
                        'reply_id'       => $optId,
                    ]);
                }
            }

            // 5. Set start_node_id
            $flow->update(['start_node_id' => $map[$data['start_temp_id']] ?? null]);

            return response()->json(['status' => 'ok', 'id' => $flow->id]);
        });
    }

    public function toggle(ChatFlow $chatFlow)
    {
        $chatFlow->update(['status' => $chatFlow->status === 'active' ? 'inactive' : 'active']);
        return response()->json(['status' => 'ok', 'new_status' => $chatFlow->status]);
    }

    public function destroy(ChatFlow $chatFlow)
    {
        $ids = $chatFlow->nodes()->pluck('id');
        ChatFlowOption::whereIn('node_id', $ids)->delete();
        ChatFlowSession::where('flow_id', $chatFlow->id)->delete();
        $chatFlow->nodes()->delete();
        $chatFlow->delete();
        return response()->json(['status' => 'ok']);
    }

    public function duplicate(ChatFlow $chatFlow)
    {
        return DB::transaction(function () use ($chatFlow) {
            $chatFlow->load(['nodes.options']);
            $newFlow = $chatFlow->replicate();
            $newFlow->id     = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $newFlow->name   = $chatFlow->name . ' (salinan)';
            $newFlow->status = 'inactive';
            $newFlow->start_node_id = null;
            $newFlow->save();

            $map = [];
            foreach ($chatFlow->nodes as $node) {
                $newNode = $node->replicate();
                $newNode->id      = \Ramsey\Uuid\Uuid::uuid4()->toString();
                $newNode->flow_id = $newFlow->id;
                $newNode->save();
                $map[$node->id] = $newNode->id;
            }
            foreach ($chatFlow->nodes as $node) {
                foreach ($node->options as $opt) {
                    $newOpt = $opt->replicate();
                    $optId  = \Ramsey\Uuid\Uuid::uuid4()->toString();
                    $newOpt->id             = $optId;
                    $newOpt->reply_id       = $optId;
                    $newOpt->node_id        = $map[$opt->node_id];
                    $newOpt->target_node_id = $opt->target_node_id ? ($map[$opt->target_node_id] ?? null) : null;
                    $newOpt->save();
                }
            }
            if ($chatFlow->start_node_id && isset($map[$chatFlow->start_node_id])) {
                $newFlow->update(['start_node_id' => $map[$chatFlow->start_node_id]]);
            }

            return response()->json(['status' => 'ok', 'id' => $newFlow->id]);
        });
    }
    public function testSend(Request $request, ChatFlow $chatFlow)
    {
        // Pastikan flow milik business yang login
        $bizId = my_business()->id ?? null;
        if (!$bizId || $chatFlow->business_id !== $bizId) abort(403);

        $data = $request->validate([
            'phone'     => 'required|string',
            'device_id' => 'nullable|uuid',
        ]);

        // Pilih device WABA: dari request atau device pertama business
        $device = \App\Models\WhatsappKeyAccount::withoutGlobalScopes()
            ->where('business_id', $chatFlow->business_id)
            ->where('status', 'active')
            ->when($data['device_id'] ?? null, fn($q, $id) => $q->where('id', $id))
            ->first();

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Belum ada device WABA aktif untuk business ini.'], 422);
        }

        // Load langkah pertama
        $start = \App\Models\ChatFlow\ChatFlowNode::with('options')
            ->where('id', $chatFlow->start_node_id)
            ->first();

        if (!$start) {
            return response()->json(['status' => 'error', 'message' => 'Flow belum punya langkah awal. Simpan dulu.'], 422);
        }

        // Normalisasi nomor: 08xx → 628xx
        $phone = preg_replace('/[^0-9]/', '', $data['phone']);
        if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
        if (!$phone) {
            return response()->json(['status' => 'error', 'message' => 'Nomor tidak valid.'], 422);
        }

        $notif = app(\App\Services\Sistem\WabaNotificationService::class);

        // Kirim sesuai tipe node awal (gak nyentuh DB / history_chats)
        try {
            if (in_array($start->type, ['buttons', 'list'])) {
                $ok = $notif->sendInteractive($phone, $start, $start->options, $device, null);
            } else {
                $ok = $notif->sendText($phone, $start->body_text ?? '-', $device, null);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[testSend] ' . $e->getMessage());
            $ok = false;
        }

        return response()->json([
            'status'  => $ok ? 'ok' : 'error',
            'message' => $ok
                ? "Terkirim ke {$phone}! Ini hanya langkah pertama. Tes penuh: aktifkan flow, chat dari nomor sendiri."
                : 'Gagal kirim. Cek: (1) nomor valid, (2) device WABA aktif, (3) akun Meta tidak error.',
        ]);
    }

}
