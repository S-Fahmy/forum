<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Channel;
use App\Filters\ThreadFilters;
use App\Rules\Recaptcha;
use App\Rules\SpamFree;
use App\Trending;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ThreadController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $filters/*, Trending $trending*/)
    {
        $threads = $this->getThreads($channel, $filters);

        if (request()->wantsJson()) {
            return $threads;
        }

        return view('threads.index', ['threads' => $threads/*, 'trending' => $trending->get()*/]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Recaptcha $recaptcha)
    {

        request()->validate([
            'title' => ['required', new SpamFree],
            'body' => ['required', new SpamFree],
            'channel_id' => ['required', 'exists:channels,id'],
            'g-recaptcha-response' => ['required', $recaptcha]
        ]);


        //$spam->detect(request('body'));

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'slug' => request('title'), //set using laravel model mutators setters in thread class
            'body' => request('body')
        ]);




        return redirect($thread->path())->with('flash', 'Thread created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread /*Trending $trending*/)
    {
        //  dd($thread);

        //set a cache value of the last visit time if the user is logged in
        if (auth()->check()) {

            auth()->user()->read($thread);
        }

        // $trending->push($thread); //increase the redis trending counter set

        // $thread->visits()->record(); //increase the thread views in redis

        //return $thread;

        return view('threads.show', [
            'thread' => $thread

        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread)
    {

        $this->authorize('update', $thread);

        //if a thread gets deleted, the replies should be deleted, now its done using a static method
        //in the model class instead of the following 1line  of code.
        // $thread->replies()->delete();

        //delete thread.
        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return redirect('/threads');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update($channel, Thread $thread)
    {
        $this->authorize('update', $thread); // policy

        $thread->update(request()->validate([
            'title' => 'required',
            'body' => 'required'
        ]));

        return $thread;
    }







    /**
     * Fetch all relevant threads.
     * NOTE FOR ME: this probably can be approved, this way: fetching thread link has fault /all/ channel,
     * so if the channel keyword was all thn get all threads and apply the filter
     * if a channel was equal to something and not null do an eloquent query on the channel object instance to get its threads
     * $threads = $channel->threads and apply the filters?
     *
     * @param Channel       $channel
     * @param ThreadFilters $filters
     * @return mixed
     */
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        //get all the threads then apply the filter 
        $threads = Thread::latest()->filter($filters);

        //if the link had a channel in it, look for the related threads only
        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads->paginate(10);
    }
}
