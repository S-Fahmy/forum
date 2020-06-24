<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class RegisterConfirmationController extends Controller
{
    //

    public function confirm()
    {

        try {
            //check if the users table has any confirmation_token equal to ?token url parameter and call the confirm method
            User::where('confirmation_token', request('token'))->firstOrFail()->confirm();
            //  $user->confirm();

        } catch (\Throwable $th) {
            return redirect(route('threads'))
                ->with('flash', ['message' => 'Unknown Account!', 'level' => 'danger']);
        }


        return redirect(route('threads'))
            ->with('flash', ['message' => 'Your account is now confirmed! You may post to the forum.', 'level' => 'success']);
    }
}
