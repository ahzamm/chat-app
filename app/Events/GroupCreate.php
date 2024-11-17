<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GroupCreate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;
    public $notification_message;

    public function __construct($group, $notification_message)
    {
        $this->group = $group;
        $this->notification_message = $notification_message;
    }

    public function broadcastOn()
    {
        $channels = [];
        Log::info('Group members being broadcasted to:', $this->group->members->pluck('user_id')->toArray());


        foreach ($this->group->members as $member) {
            if ($member->user_id !== $this->group->created_by) {
                Log::info('Broadcasting on Member with ID ', ['user_id' => $member->user_id]);

                $channels[] = new PrivateChannel('group.create.' . $member->user_id);
            }
        }

        return $channels;
    }

    public function broadcastWith()
    {
        return ['message' => $this->notification_message];
    }


    public function broadcastAs()
    {
        return 'group.create';
    }
}
