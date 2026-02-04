<?php

namespace App\Http\Controllers;

use App\Events\MessageConversation;
use App\Events\MessageUpdated;
use App\Events\NewConversation;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConversationUserController extends Controller
{
    public function index()
    {
        $authId = Auth::id();

        $conversations = Conversation::whereHas('users', fn($q) => $q->where('users.id', $authId))
            ->with([
                'users:id,name',
                'messages' => fn($q) => $q->latest()->limit(1)
            ])
            ->latest('updated_at')
            ->get()
            ->map(function ($conversation) use ($authId) {
                $otherUser = $conversation->users->firstWhere('id', '!=', $authId);

                $unreadCount = $conversation->messages()
                    ->where('user_id', '!=', $authId)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'id' => $conversation->id,
                    'other_user' => $otherUser,
                    'last_message' => $conversation->messages->first(),
                    'unread_count' => $unreadCount,
                ];
            });


        return Inertia::render('ChatList', [
            'conversations' => $conversations
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id'
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);

        $authId = Auth::id();

        // نفرغ الـ unread count (مثلاً بتحديث جدول pivot)
        $conversation->messages()
            ->where('user_id', '!=', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'ok']);
    }


    public function show(Conversation $conversation)
    {
        $authId = Auth::id();

        // حماية
        abort_if(
            ! $conversation->users()->where('users.id', $authId)->exists(),
            403
        );

        // 🔹 Mark unread messages as read
        $conversation->messages()
            ->where('user_id', '!=', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversation->load([
            'users:id,name',
            'messages.user:id,name'
        ]);

        $otherUser = $conversation->users->firstWhere('id', '!=', $authId);

        return Inertia::render('ChatShow', [
            'conversation' => $conversation,
            'messages'     => $conversation->messages,
            'otherUser'    => $otherUser
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        $conversation = $message->conversation;
        $conversation->touch();

        // 1️⃣ بث الرسالة داخل المحادثة
        broadcast(new MessageConversation($message))->toOthers();

            // 2️⃣ بث للمستخدم الآخر فقط لعرض المحادثة في ChatList realtime
            // $otherUser = $conversation->users()
            //     ->where('id', '!=', Auth::id())
            //     ->first();

            $otherUser = $conversation->users
                ->firstWhere('id', '!=', Auth::id());

            if ($otherUser) {
                broadcast(new NewConversation($conversation, $message, $otherUser));
            }


        return $message->load('user:id,name');
    }

    public function updateMessage(Request $request, Message $message)
    {
        abort_if($message->user_id !== Auth::id(), 403);

        $request->validate([
            'content' => 'required|string'
        ]);

        $message->update([
            'content' => $request->content,
            'edited_at' => now(),
        ]);
        broadcast(new MessageUpdated($message));

        // broadcast(new MessageUpdated($message))->toOthers();

        return $message;
    }

    public function deleteConversation(Conversation $conversation)
    {
        $conversation->users()->updateExistingPivot(
            Auth::id(),
            ['deleted_at' => now()]
        );

        return response()->json(['status' => 'deleted']);
    }
}
