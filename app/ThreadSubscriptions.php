<?php

namespace App;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;

class ThreadSubscriptions extends Model
{
    protected $guarded = [];


    public function user()
    {

        return $this->belongsTo(User::class);
    }
    
    public function thread()
    {

        return $this->belongsTo(Thread::class);
    }



    public function buildNotification($thread, $reply)
    {
        //we don't want to notify the same person who posted the reply about his own reply

        if ($this->user_id != $reply->user_id) {
            $this->user->notify(new ThreadWasUpdated($thread, $reply));
        }
    }
}
