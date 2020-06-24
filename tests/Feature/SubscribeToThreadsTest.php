<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{

    use DatabaseMigrations;


    /** @test */
    public function a_user_can_subscribe_to_a_thread()
    {

        //create a thread
        $thread = $this->create('App\Thread');

        //authenticated user
        $user = $this->signIn();
        $user2 = $this->signIn();

        //user can subscribe to a thread
        $thread->subscribe($user->id);
        $thread->subscribe($userId = 4);
        //assert thread has 1 sub
        $this->assertCount(2, $thread->subscribers);

        //fetch user threads subscriptions
        // $this->assertCount(1, $user->subscriptions);

    }

    /** @test */
    public function a_user_can_unsubscribe_to_a_thread()
    {

        //create a thread
        $thread = $this->create('App\Thread');

        //authenticated user
        $user = $this->signIn();

        //user can subscribe to a thread
        $thread->subscribe($user->id);
        $thread->subscribe(5);

        //unsubscribe
        $thread->unsubscribe($user->id);

        //assert thread has 0 sub
        $this->assertCount(1, $thread->subscribers);

        //fetch user threads subscriptions
        // $this->assertCount(1, $user->subscriptions);

    }



    /** @test */
    public function a_user_can_submit_subscribe_to_a_thread()
    {

        //create a thread
        $thread = $this->create('App\Thread');

        //authenticated user
        $this->signIn();


        $this->assertCount(0, $thread->subscribers);

        //user can subscribe to a thread
        $this->post($thread->path() . '/subscriptions');

        //assert thread has 1 sub
        $this->assertCount(1, $thread->fresh()->subscribers);


    }


    // /** @test */
    // public function a_user_can_not_subscribe_twice()
    // {
    //     //create a thread
    //     $thread = $this->create('App\Thread');

    //     //authenticated user
    //     $this->signIn();

    //     //user can subscribe to a thread
    //     $response = $this->post($thread->path() . '/subscriptions');
    //     $response2 = $this->post($thread->path() . '/subscriptions');


    //     //assert thread has 1 sub
    //     $this->assertCount(1, $thread->subscribers);
    // }

    /** @test */
    public function find_if_user_is_subscribed_to_thread()
    {

        $thread = $this->create('App\Thread');

        //authenticated user
        $this->signIn();

        $this->assertFalse($thread->getIsSubscribedAttribute(auth()->id()));

        //sub
        // $thread->subscribe(auth()->id());
        $this->post($thread->path() . '/subscriptions');

        $this->assertTrue($thread->getIsSubscribedAttribute(auth()->id()));
    }


    /** @test */
    public function a_user_can_submit_an_unsubscribe_to_a_thread()
    {

        //create a thread
        $thread = $this->create('App\Thread');

        //authenticated user
        $user = $this->signIn();

        //user can subscribe to a thread
        $thread->subscribe(auth()->id());

        //unsubscribe
        $this->delete($thread->path() . '/subscriptions');

        //assert thread has 0 sub
        $this->assertCount(0, $thread->subscribers);
    }
}
