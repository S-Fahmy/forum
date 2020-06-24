<?php

namespace App;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];


    /**
     * Fetch the associated subject for the activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }


    //get the activity feed collection used in the profilesController
    /**
     * Fetch an activity feed for the given user.it eager loads the thread or replies data ir etc(subject data)
     * and grouped by date.
     *
     * @param  User $user
     * @param  int  $take
     * @return \Illuminate\Database\Eloquent\Collection;
     */
    public static function feed($user, $take = 50)
    {
        return static::where('user_id', $user->id)->latest()->with('subject')->take($take)->get()
            ->groupBy(function ($activity) {
                return $activity->created_at->format('Y-m-d');
            });
    }
}
