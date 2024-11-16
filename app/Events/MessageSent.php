<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        if ($this->message->group_id) {
            return new PrivateChannel('group.' . $this->message->group_id);
        }

        return new PrivateChannel('chat.' . $this->message->receiver_id);
    }


    public function broadcastWith()
    {
        return [
            'id'          => $this->message->id,
            'sender_id'   => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'message'     => $this->message->message,
            'created_at'  => $this->message->created_at->toDateTimeString(),
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

}
