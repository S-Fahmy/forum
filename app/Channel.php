<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Thread;

class Channel extends Model
{
    //

    public function getRouteKeyName()
    {
        //get channel objects using their name
        return 'slug';
    }



    public function threads(){

        return $this->hasMany(Thread::class);

    }
}
