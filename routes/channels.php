<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Log;



Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id === (int) $receiverId;
});


Broadcast::channel('group.create.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});



Broadcast::channel('group.chat.{groupId}', function ($user, $groupId) {
    $isMember = GroupMember::where('group_id', $groupId)
        ->where('user_id', $user->id)
        ->exists();

        Log::info('User attempting to join group chat:', [
            'user_id' => $user->id,
            'name' => $user->name,
            'group_id' => $groupId,
            'is_member' => $isMember,
        ]);

    return $isMember;
});

