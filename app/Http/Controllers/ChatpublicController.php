<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\chatpublic;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatpublicController extends Controller
{
    // عرض الصفحة مع رسائل الشات
    public function index()
    {
        $messages = chatpublic::with('user')->get();
        $messagesarray = chatpublic::with('user')->get();

        return Inertia::render('Chat', [
            'messages' => $messages,
            'messagesarray' => $messagesarray
        ]);
    }

    // إرسال رسالة
    public function send(Request $request)
    {
        $message = chatpublic::create([
            'user_id' => $request->user_id,
            'content' => $request->content,
        ]);

        // event(new MessageSent($message));
    // باش يبان للآخرين فوراً (اختياري إذا كاين Echo/Pusher)
    broadcast(new MessageSent($message))->toOthers();

        return response()->json(['success' => true, 'message' => $message]);
    }
}
