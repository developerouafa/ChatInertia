<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return Conversation::where('id', $conversationId)
        ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
        ->exists();
});

// قناة خاصة لكل مستخدم
Broadcast::channel('user.{authId}', function (User $user, $userId) {
    // نسمح للمستخدم بالاستماع إذا كان هو نفس الـ userId
    return (int) $user->id === (int) $userId;
});
