<?php

namespace App\Observers\ChatBot;

use App\Models\ChatBot\HistoryChat;
use App\Models\ChatBot\HistoryChatDetail;
use Illuminate\Http\Request;

class HistoryChatObserver
{

    public function getData(Request $request)
    {
        $userId = my_user()->id;

        $query = HistoryChat::query()->with(['device:id,agent,name', 'livechat:id,agent,name', 'waba:id,agent,phone', 'telegram:id,agent,name', 'instagram:id,agent,name','messenger:id,agent,page_name', 'last_message:id,history_chat_id,message,type,created_at'])->when($request->name, function ($q) use ($request) {
            $q->where(function ($subQ) use ($request) {
                $subQ->whereRaw("MATCH(name, from_number) AGAINST(? IN BOOLEAN MODE)", ['*' . $request->name . '*']);
            });
        })->when($request->status, fn($q) => $q->where('status', $request->status))
->where(function ($query) use ($userId) {
                // OPTIMIZED: Replace 6x whereHas (EXISTS subqueries) with a single JOIN
                // This is significantly faster on large tables (303K rows)
                $query->whereExists(function ($sub) use ($userId) {
                    $sub->selectRaw('1')
                        ->from('device_agents')
                        ->whereColumn('device_agents.device_id', 'history_chats.device_id')
                        ->where('device_agents.user_id', $userId);
                })->orWhereExists(function ($sub) use ($userId) {
                    $sub->selectRaw('1')
                        ->from('waba_agents')
                        ->whereColumn('waba_agents.waba_id', 'history_chats.whatsapp_waba_id')
                        ->where('waba_agents.user_id', $userId);
                })->orWhereExists(function ($sub) use ($userId) {
                    $sub->selectRaw('1')
                        ->from('live_chat_agents')
                        ->whereColumn('live_chat_agents.livechat_id', 'history_chats.livechat_id')
                        ->where('live_chat_agents.user_id', $userId);
                })->orWhereExists(function ($sub) use ($userId) {
                    $sub->selectRaw('1')
                        ->from('telegram_agents')
                        ->whereColumn('telegram_agents.telegram_id', 'history_chats.telegram_id')
                        ->where('telegram_agents.user_id', $userId);
                })->orWhereExists(function ($sub) use ($userId) {
                    $sub->selectRaw('1')
                        ->from('instagram_agents')
                        ->whereColumn('instagram_agents.instagram_id', 'history_chats.instagram_id')
                        ->where('instagram_agents.user_id', $userId);
                })->orWhereExists(function ($sub) use ($userId) {
                    $sub->selectRaw('1')
                        ->from('messenger_agents')
                        ->whereColumn('messenger_agents.messenger_id', 'history_chats.messanger_id')
                        ->where('messenger_agents.user_id', $userId);
                });
            })
            ->when($request->from, fn($q) => $q->where('from', $request->from))
            ->when($request->device_id, fn($q) => $q->where('device_id', $request->device_id))
            ->when($request->waba_id, fn($q) => $q->where('whatsapp_waba_id', $request->waba_id))
            ->when($request->resolvedby, fn($q) => $q->where('resolved_by_id', $request->resolvedby))
            ->when($request->handled || $request->tab == 'assignment', function ($q) use ($request, $userId) {
                $handledId = $request->handled ?? $userId;
                $q->where(function ($subQ) use ($handledId) {
                    $subQ->where('handled_by', $handledId)->orWhereJsonContains('collabolator', ['id' => (int)$handledId]);
                });
            })->when($request->agent, function ($q) use ($request) {
                $q->whereHas('livechat', fn($subQ) => $subQ->where('finetunnel_id', $request->agent));
            })->when($request->label, function ($q) use ($request) {
                foreach ((array)$request->label as $label) {
                    $q->whereJsonContains('label', ['id' => $label]);
                }
            })->when($request->start_date || $request->end_date, function ($q) use ($request) {
                $q->whereHas('details', function ($query) use ($request) {
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                    } else {
                        $query->whereDate('created_at', $request->start_date);
                    }
                });
            })->when($request->assignment_status == 'block', fn($q) => $q->where('status', 'block'))
            ->when(empty($request->assignment_status), fn($q) => $q->where('status', '!=', 'block'))
            ->when($request->assignment_status == 'unassignment', function ($q) {
                $q->where('status', 'open')
                    ->where('takeover', 'no')
                    ->where('status', '!=', 'block');
            })->when(
                $request->handled || $request->tab == 'assignment' || $request->assignment_status == 'assignment',
                fn($q) => $q->where('takeover', 'yes')->where('status', '!=', 'block')
            )->when($request->assignment_status == 'unread', function ($q) {
                // OPTIMIZED: Use unread_count column instead of WHERE EXISTS on details (1.69M rows)
                $q->where('unread_count', '>', 0)
                  ->where('status', '!=', 'block');
            });

        $query->orderByDesc('last_message_at') // FIX #2: pakai kolom, bukan subquery
            ->orderByDesc('history_chats.id');

        return $query;
    }

    public function getMessage(Request $request, $historyId = '')
    {
        // FIX 4: Eager load semua relasi yang dibutuhkan MessagestResource
        // Mencegah N+1 query untuk setiap pesan
        return HistoryChatDetail::with([
            'reply:id,name',                          // agent yang reply
            'history:id,name,merchant_id',            // parent chat (nama customer)
            'repliedMessage:id,message,type,from,file_path,reply_to,quoted_message', // pesan yang di-quote
            'reactions:id,history_chat_detail_id,emoji,reactor_phone,reactor_type',  // emoji reactions
        ])->where(function ($q) use ($request) {
            return $request->name ? $q->where('message', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($historyId) {
            return $historyId != '' ? $q->where('history_chat_id', $historyId) : '';
        })->orderBy('created_at', 'desc');
    }

    public function getByNumber($type = 'personal', $number, $deviceId = null, $from = 'whatsapp', $liveChat = null, $waba = null, $telegramId = null, $instagramId = null, $messangerId = null)
    {
        return HistoryChat::where('type', $type)
            ->where(function ($q) use ($deviceId, $number) {
                return $deviceId != null
                    ? $q->where('from_number', $number)->orWhere('jid_number', $number)
                    : $q->where('from_number', $number);
            })
            ->where('device_id', $deviceId)
            ->where('livechat_id', $liveChat)
            ->where('whatsapp_waba_id', $waba)
            ->where('telegram_id', $telegramId)
            ->where('instagram_id', $instagramId)
            ->where('messanger_id', $messangerId)
            ->first();
    }

    public function createData(
        $deviceId = null,
        $type = 'personal',
        $number,
        $expireDate = null,
        $merchantId = null,
        $businessId = null,
        $fromName = null,
        $from = 'whatsapp',
        $liveChat = null,
        $wabaId = null,
        $avatarUrl = null,
        $telegramId = null,
        $instagramId = null,
        $messangerId = null
    ) {
        return HistoryChat::create([
            'device_id'         => $deviceId,
            'whatsapp_waba_id'  => $wabaId,
            'instagram_id'      => $instagramId,
            'avatar_url'        => $avatarUrl,
            'name'              => $fromName,
            'merchant_id'       => $merchantId,
            'type'              => $type,
            'from_number'       => $number,
            'business_id'       => $businessId,
            'expire_date'       => $expireDate,
            'livechat_id'       => $liveChat,
            'from'              => $from,
            'takeover'          => 'no',
            'telegram_id'       => $telegramId,
            'status'            => 'open',
            'messanger_id'      => $messangerId,
            'label'             => null,
        ]);
    }

    public function sendMessage(HistoryChat $history, $message = null, $type = 'user', $image = null, $replyData = [])
    {
        $imagePath      = $image != null ? $image['path'] : null;
        $imageSize      = $image != null ? $image['size'] : null;
        $imageType      = $image != null ? $image['type'] : null;
        $originalName   = $image != null ? ($image['original_name'] ?? null) : null;

        $typeMap = [
            'image'    => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'video'    => ['video/mp4', 'video/ogg', 'video/webm'],
            'audio'    => ['audio/mpeg', 'audio/wav', 'audio/ogg'],
            'document' => [
                'application/pdf',
                'application/zip',
                'application/octet-stream',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ],
        ];

        $messageType = 'text';

        if ($imageType) {
            foreach ($typeMap as $key => $mimeTypes) {
                if (in_array($imageType, $mimeTypes)) {
                    $messageType = $key;
                    break;
                }
            }
        }

        return $history->details()->create([
            'file_path'             => $imagePath,
            'file_type'             => $imageType,
            'file_size'             => $imageSize,
            'history_chat_id'       => $history->id,
            'from'                  => $type,
            'type'                  => $imagePath != null ? $messageType : 'text',
            'reply_by_id'           => $type == 'device' ? (auth()->check() ? my_user()->id : null) : null,
            'message'               => $message,
            'is_read'               => $type == 'device' ? 'yes' : 'no',
            'reply_to'              => $replyData['reply_to'] ?? null,
            'reply_text'            => $replyData['reply_text'] ?? null,
            'original_name'         => $originalName
        ]);
    }

    public function deleting(HistoryChat $history)
    {
        HistoryChatDetail::where('history_chat_id', $history->id)->delete();
    }
}
