<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachments extends Model
{
    protected $guarded = [];

    
    protected static function boot()
    {
        parent::boot();
        //when we delete a attachment from the dB we delete the file
        static::deleting(function ($attachmentFile) {
        
            Storage::disk('public')->delete($attachmentFile->url);

        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Fetch the associated posts for the attachment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function post()
    {
        return $this->morphTo();
    }

    public function belongsToAModel($model)
    {
        //when posting, attachments always exists first so after the post route is called, update it by making it belong to a reply or a thread


        $this->update([
            'post_type' =>  "App\\" . (new \ReflectionClass($model))->getShortName(),
            'post_id' => $model->id
        ]);

        $this->save();
    }
}
