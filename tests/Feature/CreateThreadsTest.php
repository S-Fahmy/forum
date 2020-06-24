<?php

namespace Tests\Feature;

use App\Activity;
use App\Rules\Recaptcha;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use SebastianBergmann\Environment\Console;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{


    /** @test */
    public function guest_cant_create_new_thread()
    {


        $this->expectException('Illuminate\Auth\AuthenticationException');

        $thread = $this->make('App\Thread');

        $this->post('/threads', $thread->toArray());

        $this->get($thread->path())->assertSee($thread->title);
    }


    /** @test */
    public function user_can_create_new_thread()
    {

        $this->signIn();
        

        $thread = $this->create('App\Thread');
        $response = $this->post('/threads', $thread->toArray() +['g-recaptcha-response' => 'kosmayteenomak']);
        
        $this->get($response->headers->get('location'))->assertSee($thread->title);
    }


    // /** @test */
    // function a_thread_requires_recaptcha_verification()
    // {
    //     unset(app()[Recaptcha::class]); //unbind from the mock class
    //     $this->publishThread(['g-recaptcha-response' => 'test'])
    //         ->assertSessionHasErrors('g-recaptcha-response');
    // }



    /** @test */
    function a_thread_requires_a_unique_slug()
    {
        $this->signIn();
        $this->withExceptionHandling();


        $thread = $this->create('App\Thread', ['title' => 'Foo Title', 'slug' => 'foo-title']);
        $thread2 = $this->create('App\Thread', ['title' => 'Foo Title', 'slug' => 'foo-title']);

        //  $this->postJson(route('threads'), $thread->toArray());


        $this->assertEquals("foo-title", $thread['slug']);

        // $this->postJson(route('threads'), $thread2->toArray());

        $this->assertEquals("foo-title-2", $thread2['slug']);
    }



    /** @test */
    public function thread_requires_title()
    {

        $this->publishThread(['title' => null])->assertSessionHasErrors('title');
    }

    /** @test */
    public function thread_requires_body()
    {

        $this->publishThread(['body' => null])->assertSessionHasErrors('body');
    }

    /** @test */
    public function thread_requires_channel_id()
    {

        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])->assertSessionHasErrors('channel_id');

        //channel id also has to exist in the channels table
        $this->publishThread(['channel_id' => 1111])->assertSessionHasErrors('channel_id');
    }



    /** @test */
    public function a_user_can_view_his_threads()
    {

        $this->signIn($this->create('App\User', ['name' => 'Sf']));

        $threadBySf = $this->create('App\Thread', ['user_id' => auth()->id()]);
        $threadByNotBySf = $this->create('App\Thread');

        $this->get('/threads?by=Sf')->assertSee($threadBySf->title)
            ->assertDontSee($threadByNotBySf->title);
    }




    /** @test */
    public function unauthorized_cant_delete_thread()
    {
        $this->withExceptionHandling();

        $thread = $this->create('App\Thread');

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    /** @test */
    public function an_auth_user_can_delete_thread()
    {
        $this->signIn();

        $thread = $this->create('App\Thread', ['user_id' => auth()->id()]);
        $reply = $this->create('App\Reply', ['thread_id' => $thread->id]);

        //delete 

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        //replies and activities(all types) associated with the thread should get deleted

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);

        $this->assertEquals(0, Activity::count());
    }

    /** @test */
    function authenticated_uncofirmer_users_may_not_create_new_threads()
    {
        //user factory make users confirmed by default for the sake of ease of use. so i make a fresh unconfirmed user
        $user = $this->create(
            'App\User',
            ["confirmed" => false]
        );
        $this->signIn($user);

        $thread = $this->make('App\Thread');


        $this->post('/threads', $thread->toArray())
            ->assertRedirect('/threads')
            ->assertSessionHas('flash');
    }




    public function publishThread($attributes = [])
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = $this->make('App\Thread', $attributes);
        return $this->post('/threads', $thread->toArray());
    }
}
