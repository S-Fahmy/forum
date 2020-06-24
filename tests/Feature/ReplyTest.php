<?php

namespace Tests\Feature;

use App\Reply;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_knows_if_it_was_just_published()
    {
        $reply = $this->create('App\Reply');

        $this->assertTrue($reply->wasPublishedLessThanMinuteAgo());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasPublishedLessThanMinuteAgo());
    }

    /** @test */
    public function it_can_reads_mentioned_users()
    {
        $reply = $this->create('App\Reply', ['body' => 'hey guys, i miss @sf and @sky']);

        $this->assertEquals(['sf', 'sky'], $reply->mentionedUsers());
        //TODO regexp needs to not include marks
    }

        /** @test */
        function it_wraps_mentioned_usernames_in_the_body_within_anchor_tags()
        {
            $reply = new Reply([
                'body' => 'Hello @Jane-Doe.'
            ]);
    
            $this->assertEquals(
                'Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.',
                $reply->body
            );
    
        }

        
    /** @test */
    function it_knows_if_it_is_the_best_reply()
    {
        $reply = $this->create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->isBest());

    }
}
