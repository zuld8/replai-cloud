<?php

namespace App\Services\Platform;

use App\Models\Meta\MessengerAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MesaangerService
{


    public function getData(Request $request)
    {
        return MessengerAccount::where(function ($q) use ($request) {
            return $request->name ? $q->where('page_name', 'like', '%' . $request->name . '%') : '';
        })->whereHas('agents', function ($q) {
            $q->where('user_id', my_user()->id);
        })->orderBy('page_name', 'asc');
    }



    public function checkLimit()
    {
        if (my_user()->role == 'user') {
            $messengerLimitation = messenger_limitation(my_business());
            if (!$messengerLimitation) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create or update messenger account
     */
    public function createData($pageData)
    {
        $expiresAt = isset($pageData['expires_in'])
            ? now()->addSeconds($pageData['expires_in'])
            : now()->addDays(60);

        $messenger = MessengerAccount::updateOrCreate(
            [
                'page_id' => $pageData['page_id'],
            ],
            [
                'agent' => my_user()->id,
                'page_name' => $pageData['page_name'],
                'page_username' => $pageData['page_username'] ?? null,
                'page_picture_url' => $pageData['page_picture_url'] ?? null,
                'category' => $pageData['category'] ?? null,
                'about' => $pageData['about'] ?? null,
                'phone' => $pageData['phone'] ?? null,
                'email' => $pageData['email'] ?? null,
                'website' => $pageData['website'] ?? null,
                'followers_count' => $pageData['followers_count'] ?? 0,
                'access_token' => $pageData['access_token'],
                'token_expires_at' => $expiresAt,
                'auto_reply_certain_time'   => 'no',
                'method'    => 'chatbot',
                'daily_limit'   => 'no',
                'status' => 'active',
                'details' => $pageData,
            ]
        );

        // Insert pivot directly with UUID (syncWithoutDetaching bypasses Pivot model and omits id)
        $userId = my_user()->id;
        $exists = DB::table('messenger_agents')
            ->where('messenger_id', $messenger->id)
            ->where('user_id', $userId)
            ->exists();
        if (!$exists) {
            DB::table('messenger_agents')->insert([
                'id'           => (string) Str::uuid(),
                'messenger_id' => $messenger->id,
                'user_id'      => $userId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    /**
     * Update messenger account data
     */
    public function updateData(MessengerAccount $messenger, $data)
    {
        $messenger->update([
            'page_name' => $data['page_name'] ?? $messenger->page_name,
            'page_username' => $data['page_username'] ?? $messenger->page_username,
            'page_picture_url' => $data['page_picture_url'] ?? $messenger->page_picture_url,
            'category' => $data['category'] ?? $messenger->category,
            'about' => $data['about'] ?? $messenger->about,
            'phone' => $data['phone'] ?? $messenger->phone,
            'email' => $data['email'] ?? $messenger->email,
            'website' => $data['website'] ?? $messenger->website,
            'followers_count' => $data['followers_count'] ?? $messenger->followers_count,
            'status' => 'active',
            'error_message' => null,
        ]);
    }

    /**
     * Edit messenger settings
     */
    public function editData($request, MessengerAccount $messenger)
    {

        $agents = $request->agent ?? [];
        $currentUserId = my_user()->id;

        if (!in_array($currentUserId, $agents)) {
            $agents[] = $currentUserId;
        }

        $messenger->update([
            'agent'         => implode(",", $agents),
            'limit_per_day' => $request->daily_limit == 'yes' ? $request->limit : 0,
            'auto_reply_method' => $request->method,
            'fine_tunnel_id' => in_array($request->method, ['ai', 'all']) ? $request->tunnel : null,
            'daily_limit' => $request->daily_limit,
            'auto_reply_certain_day' => $request->certain_day,
            'days' => $request->certain_day == 'yes' ? implode(',', $request->days) : null,
            'auto_reply_certain_time' => $request->certain_time,
            'start_time' => $request->certain_time == 'yes' ? $request->start_time : null,
            'end_time' => $request->certain_time == 'yes' ? $request->end_time : null,
        ]);

        $messenger->agents()->sync($agents);
    }

    /**
     * Sync page data from Facebook
     */
    public function syncPageData(MessengerAccount $messenger)
    {
        try {
            $response = Http::get("https://graph.facebook.com/v22.0/{$messenger->page_id}", [
                'fields' => 'name,username,picture,category,about,phone,emails,website,fan_count',
                'access_token' => $messenger->access_token
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $this->updateData($messenger, [
                    'page_name' => $data['name'] ?? null,
                    'page_username' => $data['username'] ?? null,
                    'page_picture_url' => $data['picture']['data']['url'] ?? null,
                    'category' => $data['category'] ?? null,
                    'about' => $data['about'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'email' => $data['emails'][0] ?? null,
                    'website' => $data['website'] ?? null,
                    'followers_count' => $data['fan_count'] ?? 0,
                ]);

                return [
                    'status' => true,
                    'message' => 'Page synced successfully'
                ];
            }

            return [
                'status' => false,
                'message' => 'Failed to sync page data'
            ];
        } catch (\Exception $e) {
            Log::error('Messenger sync error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get page conversations
     */
    public function getConversations(MessengerAccount $messenger)
    {
        try {
            $response = Http::get("https://graph.facebook.com/v22.0/{$messenger->page_id}/conversations", [
                'fields' => 'participants,updated_time,message_count,unread_count',
                'access_token' => $messenger->access_token
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get messenger conversations: ' . $e->getMessage());
            return [];
        }
    }

    public function sendMessage(
        MessengerAccount $messenger,
        $type = 'text',
        $from,
        $message = '',
        $file = null,
        $attachmentType = null
    ) {
        if ($type == 'file') {
            $dataOfMessage = [
                'attachment' => [
                    'type' => $attachmentType,
                    'payload' => [
                        'url' => asset($file),
                        'is_reusable' => true
                    ]
                ]
            ];
        } else {
            $dataOfMessage = ['text' => $message];
        }

        $response = Http::withToken($messenger->access_token)
            ->post("https://graph.facebook.com/v22.0/me/messages", [
                'recipient' => ['id' => $from],
                'messaging_type' => 'RESPONSE',
                'message' => $dataOfMessage
            ]);

        return $response;
    }
}
