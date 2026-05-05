<?php

namespace App\Services\Platform;

use App\Models\Meta\InstagramAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class InstagramService
{
    public function getData(Request $request)
    {
        return InstagramAccount::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->whereHas('agents', function ($q) {
            $q->where('user_id', my_user()->id);
        })->orderBy('name', 'asc');
    }

    public function checkLimit()
    {
        if (my_user()->role == 'user') {
            $deviceLimitation  = instagram_limitation(my_business());
            if (!$deviceLimitation) {
                return false;
            }
        }


        return true;
    }

    public function createData($igData)
    {
        $expiresAt = isset($igData['expires_in'])
            ? now()->addSeconds($igData['expires_in'])
            : now()->addDays(60);

        $instagram = InstagramAccount::updateOrCreate(
            [
                'instagram_id' => $igData['instagram_id'],
            ],
            [
                'agent'     => my_user()->id,
                'username' => $igData['username'],
                'name' => $igData['name'],
                'profile_picture_url' => $igData['profile_picture_url'],
                'biography' => $igData['biography'],
                'website' => $igData['website'],
                'followers_count' => $igData['followers_count'],
                'follows_count' => $igData['follows_count'],
                'media_count' => $igData['media_count'],
                'page_id' => $igData['page_id'],
                'page_name' => $igData['page_name'],
                'access_token' => $igData['access_token'],
                'token_expires_at' => $expiresAt,
                'auto_reply_certain_time'   => 'no',
                'method'    => 'chatbot',
                'daily_limit'   => 'no',
                'status' => 'active',
                'details' => $igData,
            ]
        );

        $instagram->agents()->syncWithoutDetaching([my_user()->id]);

        return $instagram;
    }

    public function updateData(InstagramAccount $instagram, $data)
    {

        $instagram->update([
            'username' => $data['username'] ?? $instagram->username,
            'name' => $data['name'] ?? $instagram->name,
            'profile_picture_url' => $data['profile_picture_url'] ?? $instagram->profile_picture_url,
            'biography' => $data['biography'] ?? $instagram->biography,
            'website' => $data['website'] ?? $instagram->website,
            'followers_count' => $data['followers_count'] ?? $instagram->followers_count,
            'follows_count' => $data['follows_count'] ?? $instagram->follows_count,
            'media_count' => $data['media_count'] ?? $instagram->media_count,
            'status' => 'active',
        ]);
    }

    public function editData(Request $request, InstagramAccount $instagram)
    {

        $agents = $request->agent ?? [];
        $currentUserId = my_user()->id;

        if (!in_array($currentUserId, $agents)) {
            $agents[] = $currentUserId;
        }

        $instagram->update([
            'agent'                         => implode(",", $agents),
            'limit_per_day'                 => $request->daily_limit == 'yes' ? $request->limit : 0,
            'auto_reply_method'             => $request->method,
            'fine_tunnel_id'                => $request->method == 'ai' || $request->method == 'all' ? $request->tunnel : null,
            'daily_limit'                   => $request->daily_limit,
            'auto_reply_certain_day'        => $request->certain_day,
            'days'                          => $request->certain_day == 'yes' ? implode(',', $request->days) : null,
            'auto_reply_certain_time'       => $request->certain_time,
            'start_time'                    => $request->certain_time == 'yes' ? $request->start_time : null,
            'end_time'                      => $request->certain_time == 'yes' ? $request->end_time : null,
        ]);

        $instagram->agents()->sync($agents);
    }

    public function sendMessage(InstagramAccount $instagram, $type = 'text', $from, $message = '', $file = null, $attachmentType = null)
    {

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

        $response   = Http::withToken($instagram->access_token)
            ->post("https://graph.facebook.com/v22.0/me/messages", [
                'recipient' => ['id' => $from],
                'message' => $dataOfMessage
            ]);

        return $response;
    }
}
