<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GroupMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }


    public function broadcastOn(): array
    {
        Log::info('Broadcasting message on Group :', ['group_id'=>$this->message->group_id]);
        return [
            new PrivateChannel('group.chat.'.$this->message->group_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id'          => $this->message->id,
            'sender_id'   => $this->message->sender_id,
            'message'     => $this->message->message,
            'created_at'  => $this->message->created_at->toDateTimeString(),
        ];
    }

    public function broadcastAs()
    {
        return 'group.chat';
    }
}
