<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\PrivateChannel;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'message'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // public function broadcastOn()
    // {
    //     return new PrivateChannel('chat.'.$this->receiver_id->id);
    // }

    // public function broadcastWith()
    // {
    //     return [
    //         'id' => $this->id,
    //         'sender_id' => $this->sender_id,
    //         'receiver_id' => $this->receiver_id,
    //         'message' => $this->message,
    //         'created_at' => $this->created_at->toDateTimeString(),
    //     ];
    // }
}
