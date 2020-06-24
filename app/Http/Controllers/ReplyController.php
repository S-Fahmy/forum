<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Reply;
use App\Rules\SpamFree;
use App\Thread;


class ReplyController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {

        //this is an api method, it returns a dataset of paginated thread replies back to an axios ajax request in replies.vue
        return $thread->replies()->oldest()->paginate(10);
    }


    public function store($channelId, Thread $thread, CreatePostRequest $postFormValidation)
    {
        //The incoming form request is validated before the controller method is called using the createPostRequest
        //If validation fails, a redirect 303 response is sent,If the request was AJAX, a HTTP json response with a 422 status code 
        //will be sent.
        if (!auth()->user()->isAdmin()) {
            if ($thread->locked) {
                return response('Thread is locked', 422);
            }
        }


        return $thread->addReply([

            'user_id' => auth()->id(),
            'body' => request('body'),
            'attachments_count' => request('includesAttachments') ? 1 : 0

        ], request('includesAttachments'))->load('owner'); // eager load reply owner data in the json.
    }



    public function update(Reply $reply)
    {

        $this->authorize('update', $reply); //policy

        try {

            request()->validate(['body' => ['required', new SpamFree]]);


            $reply->update(['body' => request('body')]);
        } catch (\Exception $e) {

            return response('Sorry, your reply could not be saved at this time.', 422);
        }
    }


    public function destroy(Reply $reply)
    {
        //only auth users
        // if ($reply->user_id != auth()->id()) {

        //     return response([], 403); //forbidden
        // }

        $this->authorize('update', $reply);
        $reply->delete();

        //if its an ajax request then return a status as a  response instead of back() redirect
        if (request()->expectsJson()) {
            return response(['status' => 'reply deleted']);
        }
        return back(); //302

    }
}
