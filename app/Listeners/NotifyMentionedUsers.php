<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Notifications\YouWereMentioned;
use App\User;

class NotifyMentionedUsers
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
        
        $names = $event->reply->mentionedUsers();
        //dd($names);
        foreach ($names as $name) {
            if ($user = User::where('name', $name)->first()) {
                
                $user->notify(new YouWereMentioned($event->thread, $event->reply));
                
            }
        }
    }
}
