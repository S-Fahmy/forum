<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAvatarController extends Controller
{
    public function store()
    {   
        //the request has data.avatar
        request()->validate([
            'avatar' => ['required', 'image']
        ]);


        auth()->user()->update([
            'avatar_path' => request()->file('avatar')->store('images/avatars', 'users_uploads')//user_uploads root is defined in filesystem.php
        ]);
        return response([], 204);
    }
}
