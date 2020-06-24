<?php

namespace App\Events;

use App\Reply;
use App\Thread;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThreadReceivedNewReply
{
    use Dispatchable, SerializesModels;

  
    public $reply;
    public $thread;
    

    public function __construct(Thread $thread, Reply $reply)
    {

        $this->reply = $reply;
        $this->thread = $thread;
    }


  

    
}
