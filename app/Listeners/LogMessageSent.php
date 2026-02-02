<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;

class LogMessageSent  implements ShouldQueue
{
    public function handle(MessageSent $event)
    {
        logger('Message sent: '.$event->message);
    }
}
