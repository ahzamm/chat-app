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
    public $userName;

    public function __construct($group, $userName)
    {
        $this->group = $group;
        $this->userName = $userName;
    }

    public function broadcastOn()
    {
        $channels = [];

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
        return ['message' => $this->userName .' added you to the group ' . $this->group->name];
    }


    public function broadcastAs()
    {
        return 'group.create';
    }
}
