<?php

namespace App\Events\Crm;

use App\Http\Resources\Crm\MessagestResource;
use App\Models\ChatBot\HistoryChatDetail;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent
{
    use Dispatchable, SerializesModels;

    public $message;

    public function __construct(HistoryChatDetail $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('chat');
    }

    public function broadcastWith()
    {
        return MessagestResource::make($this->message);
    }
}
