<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class NewConversation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $message;
    public $otherUser;

    protected int $receiverId;

    public function __construct(Conversation $conversation, ?Message $message, User $receiver)
    {
        $this->conversation = $conversation;
        $this->message = $message ? $message->load('user:id,name') : null;

        // المستلم هو الشخص اللي غادي يستقبل الحدث
        $this->receiverId = $receiver->id;

        // الراسل ديال المحادثة
        $this->otherUser = $message ? $message->user : Auth::user();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->receiverId);
    }

    public function broadcastAs()
    {
        return 'new.conversation';
    }
}


