<?php

namespace App\Http\Controllers;

use App\favorite;
use App\Reply;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Reply $reply)
    {
        $reply->favorite();
        return back();
        //$reply->favorites()->create(['user_id' => auth()->id()]);

        // favorite::create([
        //     'user_id' => auth()->id(),
        //     'favorited_id' => $reply->id,
        //     'favorited_type' => get_class($reply)

        // ]);
    }

    public function destroy(Reply $reply)
    {

        $reply->unfavorite();
    }
}
