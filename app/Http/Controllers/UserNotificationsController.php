<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

class UserNotificationsController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
    }


    public function index(User $user)
    {
        // return $user->unreadNotifications;
        return auth()->user()->unreadNotifications;
    }


    public function destroy(User $user,  $notificationId)
    {


        //get the notification of the auth user with notification id and mark it as read
        $user->notifications()->findOrFail($notificationId)->markAsRead();
    }
}
