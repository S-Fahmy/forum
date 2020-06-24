<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    public function auth_user_can_add_replies()
    {

        //make sure user is authenticaded
        $this->signIn();
       

        //user create a thread
        $thread = $this->create('App\Thread');
        $reply = factory('App\Reply')->make();

        //save reply to db
        $this->post($thread->path() . '/replies', $reply->toArray());

        //we test if we see the reply
        //now this worked when the display of the was handled by laravel, it wont work with vue,
        //because the its handled by javascript
        //$this->get($thread->path())->assertSee($reply->body);
        
        $this->assertDatabaseHas('replies' , ['body' => $reply->body]);
        //assert replies count
        $this->assertEquals(1 , $thread->fresh()->replies_count);
    }


    /** @test */
    public function reply_requires_body()
    {

        $this->withExceptionHandling();
        $this->signIn();

        $thread = $this->create('App\Thread');
        $reply = $this->make('App\Reply', ['body' => null]);

        $this->json('post',$thread->path() . '/replies', $reply->toArray())->assertStatus(422);
    }


    /** @test */
    public function unauth_user_cant_create_replies()
    {
        //create reply
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $thread = $this->create('App\Thread');

        $reply = $this->create('App\Reply');

        $this->post($thread->path() . '/replies', $reply->toArray());

        //we test if we see the reply

        $this->get($thread->path())->assertSee($reply->title);
    }

    /** @test */
    public function unauth_user_cant_delete_replies()
    {
        //create reply
        $this->withExceptionHandling();

        $reply = $this->create('App\Reply');

        $this->delete("replies/{$reply->id}")->assertRedirect('/login');

        //log in a user that doesn't own the reply, shouldn't be auth to delete too
        //403 forbidden

        $this->signIn();
        $this->delete("replies/{$reply->id}")->assertStatus(403);
    }


    /** @test */
    public function auth_user_can_delete_replies()
    {


        $this->signIn();
        $reply = $this->create('App\Reply', ['user_id' => auth()->id()]);


        $this->delete("replies/{$reply->id}")->assertStatus(302);

        //assert that its missing from the db
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0 , $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function auth_user_can_edit_reply()
    {

        $this->signIn();
        $reply = $this->create('App\Reply', ['user_id' => auth()->id()]);

        $updatedBody = "wassap fool";
        $this->patch("replies/{$reply->id}", ['body' => $updatedBody]);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updatedBody
        ]);
    }

    /** @test */
    public function unauth_user_cant_edit_reply()
    {
        $this->withExceptionHandling();


        $reply = $this->create('App\Reply');

        $updatedBody = "wassap fool";
        $this->patch("replies/{$reply->id}", ['body' => $updatedBody])->assertRedirect('/login');

        //not the owner should be forbidden form editing
        $this->signIn();
        $this->patch("replies/{$reply->id}", ['body' => $updatedBody])->assertStatus(403);

    }


        /** @test */
        function replies_that_contain_spam_may_not_be_created()
        {
            $this->withExceptionHandling(); // we don't catch the exception in the controller anymore so we need this
            $this->signIn();
    
            $thread = $this->create('App\Thread');

            $reply = $this->make('App\Reply', [
                'body' => 'Yahoo Customer Support'
            ]);
        
            $this->json('post', $thread->path() . '/replies', $reply->toArray())->assertStatus(422);
        }

            /** @test */
    function users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->withExceptionHandling(); //controller doesn't catch exceptions anymore.
        $this->signIn();

        $thread = $this->create('App\Thread');
        $reply = $this->make('App\Reply');

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(201);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(429);
    }
}
