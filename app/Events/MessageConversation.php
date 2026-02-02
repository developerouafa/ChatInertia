<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageConversation implements ShouldBroadcast
{
    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('user:id,name');
    }

    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }

    public function broadcastAs()
    {
        return 'message.conversation';
    }
}
