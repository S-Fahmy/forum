<?php

namespace App;

use Illuminate\Support\Facades\Redis;

//this class is responsible for fetching trending threading from a redis sorted set
class Trending
{

    public function get()
    {
        $trending = Redis::zrevrange($this->cacheKey(), 0, 4);
        $trendingJson = [];
        foreach ($trending as $jsonText) {
            $trendingJson[] = json_decode($jsonText);
        }

        return $trendingJson;
    }

    public function push($thread)
    {

        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => $thread->path()
        ]));
    }

    /**
     * Get the cache key name.the testing env resets the redis set everytime so we want to different sets
     *
     * @return string
     */
    public function cacheKey()
    {
        return app()->environment('testing') ? 'testing_trending_threads' : 'trending_threads';
    }

    /**
     * Reset all trending threads.
     */
    public function reset()
    {
        Redis::del($this->cacheKey());
    }
}
