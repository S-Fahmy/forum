<?php

namespace App\Http\Controllers;

use App\Activity;
use App\User;
use Illuminate\Http\Request;


class ProfilesController extends Controller
{
     /**
     * Show the user's profile.
     *
     * @param  User $user
     * @return \Response
     */
    public function show(User $user)
    {

        //get the activities of the user and eager load the subject() of the activity, morphto relation defined 
        //in the activity class
       // $activities = $user->activity()->latest()->with('subject')->get();

       //return Activity::feed($user);
        return view('profiles.show', [
            'profileUser' => $user,
            'activities' => Activity::feed($user)
        ]);
    }
}
