<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    public function non_administrators_may_not_lock_threads()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = $this->create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);
    }


     /** @test */
     function administrators_can_lock_threads()
     {
         $this->signIn(factory('App\User')->states('administrator')->create());
 
         $thread = $this->create('App\Thread', ['user_id' => auth()->id()]);
 
         $this->post(route('locked-threads.store', $thread));
 
         $this->assertTrue($thread->fresh()->locked, 'Failed asserting that the thread was locked.');
     }

          /** @test */
          function administrators_can_unlock_threads()
          {
              $this->signIn(factory('App\User')->states('administrator')->create());
      
              $thread = $this->create('App\Thread', ['user_id' => auth()->id()]);
      
              $this->delete(route('locked-threads.store', $thread));
      
              $this->assertFalse($thread->fresh()->locked, 'Failed asserting that the thread was locked.');
          }

    /** @test */
    public function once_locked_threads_cant_receive_new_replies()
    {
        $this->signIn();

        $thread = $this->create('App\Thread');

        $thread->lock();

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }

    
  
}
