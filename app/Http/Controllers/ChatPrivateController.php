<?php

namespace App\Http\Controllers;

use App\Events\MessageConversation;
use App\Events\MessageSentPrivate;
use App\Events\NewConversation;
use App\Models\Conversation;
use App\Models\conversations;
use App\Models\ConversationUser;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatPrivateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return Inertia::render('ChatPrivate', [
            'users' => $users
        ]);
    }

    public function openOrCreate(User $other_user)
    {
        $authUser = Auth::user();

        // 🔍 نقلبو على conversation موجودة بيناتهم
        $conversation = Conversation::whereHas('users', function ($q) use ($authUser) {
                $q->where('users.id', $authUser->id);
            })
            ->whereHas('users', function ($q) use ($other_user) {
                $q->where('users.id', $other_user->id);
            })
            ->first();

        // 🆕 إلا ما كانتش، ننشئوها
        if (! $conversation) {
            $conversation = Conversation::create();

            $conversation->users()->syncWithoutDetaching([$authUser->id, $other_user->id]);

            // $conversation->users()->attach([
            //     $authUser->id,
            //     $other_user->id
            // ]);
        }

        // 📩 نجيبو الرسائل
        $messages = $conversation->messages()
            ->with('user:id,name')
            ->orderBy('created_at')
            ->get();

        return Inertia::render('Show', [
            'conversation' => $conversation,
            'messages'     => $messages,
            'otherUser'    => $other_user,
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string'
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

            $conversation = $message->conversation;
            $conversation->touch();

            // 👇 الرسالة داخل المحادثة
            broadcast(new MessageConversation($message))->toOthers();

            // 👇 المستخدم الآخر (اللي غادي يستقبل أول مرة)

            $otherUser = $conversation->users
                ->firstWhere('id', '!=', Auth::id());

            if ($otherUser) {
                broadcast(new NewConversation($conversation, $message, $otherUser));
            }

        return $message->load('user:id,name');

    }

}
