<?php

namespace App\Http\Controllers\api;

use App\Attachments;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentsController extends Controller
{
    public function store()
    {

        request()->validate([
            'attached' => ['required']
        ]);

        $path = request()->file('attached')->store(auth()->id() . '/attachments/', 'public'); //user_uploads root is defined in filesystem.php


        return  Attachments::create([

            'url' =>  Storage::url($path),
            'user_id' => auth()->id(),
        ]);
    }

    public function destroy($attachmentId)
    {
        $attachment = Attachments::findOrFail($attachmentId);

        $this->authorize('update', $attachment);
        $attachment->delete();


        if (request()->expectsJson()) {
            return response(['status' => 'attachment deleted']);
        }
        return back(); //302
    }
}
