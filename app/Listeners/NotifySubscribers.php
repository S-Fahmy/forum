<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;


class NotifySubscribers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        //notify each thread subscriber.
        $event->thread->subscribers->each->buildNotification($event->thread, $event->reply);

        //the reply owner thread state should be read so he doesn't see thread as unread because of his own reply
        auth()->user()->read($event->thread);
    }
}
