<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GroupCreate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;

    public function __construct($group)
    {
        $this->group = $group;
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
        return [
            'group' => [
                'id' => $this->group->id,
                'name' => $this->group->name,
            ],
            'message' => ' added you to the group ' . $this->group->name,
        ];
    }


    public function broadcastAs()
    {
        return 'group.create';
    }
}
