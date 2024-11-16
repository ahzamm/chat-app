<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $fillable = ['group_id', 'user_id'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
