<?php

namespace App;

use App\Events\ThreadReceivedNewReply;
use Illuminate\Database\Eloquent\Model;
use App\Filters\ThreadFilters;
use Carbon\Carbon;
use Laravel\Scout\Searchable;

class Thread extends Model
{
    // calling the record activity trait
    use RecordsActivity, Searchable;


    protected $guarded = [];
    //eager loading, when a query calls to get a thread object, it includes User and Channel
    //tables data in the array too, so we dont call for 2 extra queries.
    //we add the names of the functions that define the relationship to the with array.
    protected $with = ['owner', 'channel'];

    //for json
    protected $appends = ['path', 'isSubscribed', 'hasUpdatesFor'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'locked' => 'boolean'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        //global scop used to get a variable and add it to the query whenever a thread is called
        // static::addGlobalScope('replyCount', function ($builder) {
        // i added a replies_count column to threads db so i dont need this for now
        //     $builder->withCount('replies');
        // });

        //always delete the thread's associated replies when a thread gets deleted

        static::deleting(function ($thread) {
            //because we when a thread gets deleted, we deleted its associated replies
            //and replies also use the recordsActivity trait which is set to deleting
            //models associated activity, so both threads an replies activities will
            //get deleted.
            //the each make sure a reply is deleted one by one to trigger whats explained above on each reply.

            $thread->replies->each->delete();
        });
    }





    public function owner()
    {

        return $this->belongsTo(User::class, 'user_id');
    }



    public function channel()
    {

        return $this->belongsTo(Channel::class);
    }

    public function replies()
    {

        return $this->hasMany(Reply::class);
    }


    public function subscribers()
    {
        //return the relationship
        return $this->hasMany(ThreadSubscriptions::class);
    }




    /** end of relations functions */




    public function addReply($reply, $attachments = null)
    {

        $reply = $this->replies()->create($reply);

        $this->checkForIncludedAttachments($attachments, $reply);


        //$this->increment('replies_count'); another way to this rn is adding a boot method on the Reply model.

        event(new ThreadReceivedNewReply($this, $reply));

        return $reply;
    }

    public function checkForIncludedAttachments($attachments, $model)
    {
        if ($attachments) {
            //currently it nto an array, i get from the dB because $attachment is a json object not a model object
            //then we update it in the dB with the new model id
            $attachmentModel = Attachments::findOrFail($attachments['id']);

            $attachmentModel->belongsToAModel($model);
        }
    }


    public function subscribe($userId = null)
    {
        $this->subscribers()->create([
            //its possible that the userId may be null so we get the signed up user
            'user_id' => $userId ?: auth()->id(),
            'thread_id' => $this->id
        ]);

        //returning the thread object instance
        return $this;
    }

    public function unsubscribe($userId = null)
    {

        $what = $this->subscribers()->where('user_id', $userId ?: auth()->id())->delete();
        //dd($what);
        return $what;
    }


    //gonna call this function and append its value to the json response every time a thread object is requested
    public function getIsSubscribedAttribute($userId = null)
    {
        // there the user is subscribed to this thread return true

        return $this->subscribers()->where('user_id', $userId ?: auth()->id())->exists();
    }


    //check if this thread has been updated since the user last visit
    public function getHasUpdatesForAttribute($user = null)
    {
        if (auth()->check()) {
            $theUser = $user ?: auth()->user();

            //get the key string, to extract the cache relating to it
            $key = $theUser->visitedThreadCacheKey($this);

            //if the cache date related to the key is earlier then thread last updated then we have news.
            return $this->updated_at > cache($key);
        }
        return false;
    }

    /**
     * Apply all relevant thread filters.
     *
     * @param  Builder       $query
     * @param  ThreadFilters $filters
     * @return Builder
     */
    public function scopeFilter($query, ThreadFilters $filters)
    {
        return $filters->apply($query);
    }


    //return a Visits object
    public function visits()
    {
        return new Visits($this);
    }

    /**
     * Mark the given reply as the best answer.
     *
     * @param Reply $reply
     */
    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
    }

    /**
     * Lock the thread.
     */
    public function lock()
    {
        $this->update(['locked' => true]);
    }


    public function postDate()
    {

        return Carbon::createFromFormat('Y-m-d H:i:s.u', $this->created_at);
    }


    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Set the proper slug attribute.
     *
     * @param string $value
     */
    public function setSlugAttribute($value)
    {
        //TODO: simplify this shit and just add the thread id to the end of the slug
        $slug = str_slug($value);
        //is that slug already in the Db? increment it : just set it
        if (static::whereSlug($slug)->exists()) {
            $slug = $this->incrementSlug($slug);
        }

        $this->attributes['slug'] = $slug; //ordinary laravel mutators stuff
    }

    /**
     * Increment a slug's suffix.
     *
     * @param  string $slug
     * @return string
     */
    protected function incrementSlug($slug)
    {
        //if slugs are equal it means, title exist, so i get the slug of the latest thread with that title
        $latestSlug = static::whereTitle($this->title)->latest('id')->value('slug');


        //foo-title, //foo-title-3
        //if the last digit of the slug is a number, increment it
        if (is_numeric($latestSlug[-1])) {
            return preg_replace_callback('/(\d+)$/', function ($matches) {
                return $matches[1] + 1;
            }, $latestSlug);
        }
        //if there are no last digits yet then add -2, foo-tittle-2
        return "{$slug}-2";
    }

    public function path()
    {
        // return '/threads/' . $this->channel->slug . '/' . $this->id;
        return '/threads/' . $this->channel->slug . '/' . $this->slug;
    }

    public function getPathAttribute()
    {
        if ($this->channel) {
            return '/threads/' . $this->channel->slug . '/' . $this->slug;
        }

        return '';
    }
}
