<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    protected $fillable = ['sender_id', 'group_id', 'message'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
