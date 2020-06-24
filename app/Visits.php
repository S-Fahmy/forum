<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Visits
{

    protected $relation;

    public function __construct($relation)
    {

        $this->relation = $relation;
    }
    /**record a visit */
    public function record()
    {
        Redis::incr($this->getKey());

        return $this;
    }

    /**get the number of thread visits */
    public function count()
    {   //if null return 0 visits
        return Redis::get($this->getKey()) ?: 0;
    }

    public function reset()
    {
        Redis::del($this->getKey());
        return $this;
    }


    public function getKey()
    {
        return "threads.{$this->relation->id}.visits";
    }
}
