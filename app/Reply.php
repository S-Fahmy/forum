<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{

    use RecordsActivity;



    protected $guarded = [];
    // when a query calls to get a thread object, it includes User and Channel
    //tables data in the array too, so we dont call for 2 extra queries.
    //we add the names of the functions that define the relationship to the with array.

    protected $with = ['owner', 'favorites'];

    //this trick appends any custom attribute when the data returned is casted to json, used in the vue favorites.
    protected $appends = ['favoritesCount', 'isFavorited', 'isBest'];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reply) {
            //when a reply gets created increment threads replies count (can be added directly in the add function)

            $reply->thread->increment('replies_count');
        });

        //when we delete a reply we delete all of its associated favorites, and decrement thread reply_count
        static::deleting(function ($reply) {

            //delete the attachment if it has
            // $reply->attachments_count > 0 ?: Attachments::where('post_')
           
            $reply->attachments->each->delete();
            //each loop so the related favorite activities get deleted too.
            $reply->favorites->each->delete();

            $reply->thread->decrement('replies_count');
        });
    }


    public function attachments()
    {
        
        return $this->morphMany('App\Attachments', 'post');
    }

    public function owner()
    {

        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function favorites()
    {

        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite()
    {
        $userAttribute = ['user_id' => auth()->id()];

        if (!$this->favorites()->where($userAttribute)->exists()) {

            $this->favorites()->create($userAttribute);
        }
    }

    public function unfavorite()
    {

        $userAttribute = ['user_id' => auth()->id()];

        //when this function gets called, we are on the reply object instance, so we can use this.favorites 
        // $this->favorites()->where($userAttribute)->delete(); to be able to delete the activity record we have to delete 
        //each model instance by itself 
        $this->favorites()->where($userAttribute)->get()->each(function ($favorite) {
            $favorite->delete();
        });
    }


    public function isFavorited()
    {

        return !!$this->favorites()->where(['user_id' => auth()->id()])->count();
    }

    //same as above but the naming is for the returned casted to json array (i left the above function so i unsderstand why we name like that)

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }


    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    public function wasPublishedLessThanMinuteAgo()
    {

        return $this->created_at->gt(Carbon::now()->subSeconds(10)); //temporary set as 10 secs instead of a minute
    }

    public function mentionedUsers()
    {
        preg_match_all('/@([\w\-]+)/', $this->body, $matches);

        return $matches[1];
    }


    /**
     * Set the body attribute for the comment tagging
     *
     * @param string $body
     */
    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace(
            '/@([\w\-]+)/',
            '<a href="/profiles/$1">$0</a>',
            $body
        );
    }



    /**
     * Determine if the current reply is marked as the best.
     *
     * @return bool
     */
    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    public function getIsBestAttribute()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    /**
     * Determine the path to the reply.
     *
     * @return string
     */
    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }
}
