<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    

    public function setUp():void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->signIn();
    }

    /** @test */
    function unauthorized_users_may_not_update_threads()
    {
        $thread = $this->create('App\Thread', ['user_id' => $this->create('App\User')->id]);

        $this->patch($thread->path(), [])->assertStatus(403); //signed in user cant edit this thread
    }

    /** @test */
    function a_thread_requires_a_title_and_body_to_be_updated()
    {
        $thread = $this->create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed'
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(), [
            'body' => 'Changed'
        ])->assertSessionHasErrors('title');
    }

    /** @test */
    function a_thread_can_be_updated_by_its_creator()
    {

        $thread = $this->create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed',
            'body' => 'Changed body.'
        ]);
     
            $this->assertEquals('Changed', $thread->fresh()->title);
            $this->assertEquals('Changed body.', $thread->fresh()->body);
   
    }
}
