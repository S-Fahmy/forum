<?php

namespace App\Policies;

use App\User;
use App\reply;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\reply  $reply
     * @return mixed
     */
    public function view(User $user, reply $reply)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (!$lastReply = $user->fresh()->lastReply) { // if last reply returned null because user didn't post any yet return true
            return true;
        }

        return !$lastReply->wasPublishedLessThanMinuteAgo(); //return true this function return false

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\reply  $reply
     * @return mixed
     */
    public function update(User $user, reply $reply)
    {
        //

        //if the logged in user is the reply owner
        return $reply->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\reply  $reply
     * @return mixed
     */
    public function delete(User $user, reply $reply)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\reply  $reply
     * @return mixed
     */
    public function restore(User $user, reply $reply)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\reply  $reply
     * @return mixed
     */
    public function forceDelete(User $user, reply $reply)
    {
        //
    }
}
