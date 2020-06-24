<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class favorite extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    /**
     * Fetch the model that was favorited.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function favorited()
    {
        return $this->morphTo();
    }
}
