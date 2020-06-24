<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'confirmed' => 'boolean'
    ];


    /**
     * Get the route key name for Laravel.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Fetch all threads that were created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    //fetch only the last reply
    public function lastReply()
    {

        return $this->hasOne(Reply::class)->latest();
    }

    //set thread as read in the cache
    public function read($thread)
    {
        //key string
        $key = $this->visitedThreadCacheKey($thread);
        //save a cache with the current time of this visit
        cache()->forever($key, Carbon::now());
    }

    public function visitedThreadCacheKey($thread)
    {
        //build a key string that gets saved in the cache
        $formattedString = sprintf("users.%s.visits.%s", $this->id, $thread->id);

        return $formattedString;
    }


    //confirm the user
    public function confirm()
    {
        $this->confirmed = true;
        $this->confirmation_token = null;
        $this->save();
    }

    /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return in_array($this->name, ['JohnDoe', 'sf']);
    }
}
