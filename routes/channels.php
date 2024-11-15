<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    Log::info('Broadcasting message:');
    return (int) $user->id === (int) $receiverId || $user->contacts()->where('contact_id', $receiverId)->exists();
});