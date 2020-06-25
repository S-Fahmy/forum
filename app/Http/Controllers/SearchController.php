<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Trending;
use Illuminate\Http\Request;

class SearchController extends Controller
{
     /**
     * Show the search results.
     *
     * @param  \App\Trending $trending
     * @return mixed
     */
    public function show(/*Trending $trending*/)
    {
        //thread already is using the searchable trait, and we are connected to algolia services in the scout and .env files
        // $threads = Thread::search(request('q'))->paginate(25); 

        // if (request()->expectsJson()) {
        //     return $threads;
        // }

        // return view('threads.index', [
        //     'threads' => $threads,
        //     'trending' => $trending->get()
        // ]);

        if (request()->expectsJson()) {
           
            return Thread::search(request('query'))->paginate(25);
        }
        return view('search', [
            
            'trendingThreads' => [] //json_encode($trending->get())
        ]);
    }
}
