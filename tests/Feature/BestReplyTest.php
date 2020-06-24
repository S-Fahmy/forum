<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
        $this->signIn();

        $thread = $this->create('App\Thread', ['user_id' => auth()->id()]);

        $replies = $this->create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-replies.store', [$replies[1]->id]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /** @test */
    function only_the_thread_creator_may_mark_a_reply_as_best()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = $this->create('App\Thread', ['user_id' => auth()->id()]);

        $replies = $this->create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->signIn($this->create('App\User'));

        $this->postJson(route('best-replies.store', [$replies[1]->id]))->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }

        /** @test */
        function if_a_best_reply_is_deleted_then_the_thread_is_properly_updated_to_reflect_that()
        {
            $this->signIn();
    
            $reply = $this->create('App\Reply', ['user_id' => auth()->id()]);
    
            $reply->thread->markBestReply($reply);
            $this->assertTrue($reply->fresh()->isBest());
            $this->assertEquals($reply->thread->fresh()->best_reply_id, $reply->id);

    
            $this->deleteJson(route('replies.destroy', $reply));
    
            $this->assertNull($reply->thread->fresh()->best_reply_id);
        }
}
