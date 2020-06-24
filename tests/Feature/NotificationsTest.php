<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_notification_is_given_when_subscribed_thread_gets_a_new_reply_that_is_not_by_the_current_user()
    {

        $user = $this->signIn();
        $user2 =$this->create('App\User');


        $thread = $this->create('App\Thread');
        $thread->subscribe($user->id);
        $thread->subscribe($user2->id);

        //user 1 submit a reply
        $reply = $this->create('App\Reply', ['user_id' => $user->id, 'thread_id' => $thread->id, 'created_at' => Carbon::now()->subSeconds(61)]);

        $this->assertCount(0, $user->notifications);
        $this->assertCount(0, $user2->notifications);


        //user 1 is subbed, user 1 shouldn't get a notification but user2 should
        $this->post($thread->path() . '/replies', $reply->toArray());


        //other subscribed users gets a notification

        $this->assertCount(0, $user->fresh()->notifications);
        $this->assertCount(1, $user2->fresh()->notifications);
    }


    /** @test */
    public function user_can_set_notification_as_read()
    {

        $user = $this->signIn();

        $this->create(DatabaseNotification::class);


        $this->assertCount(1, $user->unreadNotifications);

        //he opens his notifications and we fetch unread notifics, first one only for now.
        $notificationId = $user->unreadNotifications->first()->id;

        //they get marked as read
        $this->delete('/profiles/' . $user->name . '/notifications/' . $notificationId);

        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }

    /** @test */
    public function user_can_fetch_his_unread_notifications()
    {
        $user = $this->signIn();

        $this->create(DatabaseNotification::class);


        $this->assertCount(1, $user->unreadNotifications);

        //notifications route
        $response = $this->getJson('/profiles/' . $user->name . '/notifications/')->json();

        $this->assertCount(1, $response);
    }


    /** @test */
    function mentioned_users_in_a_reply_are_notified()
    {
        // Given we have a user, JohnDoe, who is signed in.
        $john = $this->create('App\User', ['name' => 'JohnDoe']);

        $this->signIn($john);

        // And we also have a user, JaneDoe.
        $jane = $this->create('App\User', ['name' => 'JaneDoe']);

        // If we have a thread
        $thread = $this->create('App\Thread');

        // And JohnDoe replies to that thread and mentions @JaneDoe.
        $reply = $this->make('App\Reply', [
            'body' => 'Hey @JaneDoe check this out.'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        // Then @JaneDoe should receive a notification.
        $this->assertCount(1, $jane->fresh()->notifications);
    }
}
