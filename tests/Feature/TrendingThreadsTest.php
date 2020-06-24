<?php

namespace Tests\Feature;

use App\Trending;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_increments_a_threads_score_each_time_it_is_read()
    {
        $trends = new Trending();

        $trends->reset();

        $this->assertEmpty($trends->get());

        $thread = $this->create('App\Thread');

        $this->call('GET', $thread->path()); //visit threads endpoint means thread visits increase by one

        $this->assertCount(1, $trends->get());

        $this->assertEquals($thread->title, $trends->get()[0]->title);
    }

    /** @test */
    public function thread_records_its_visits()
    {
        // create thread
        $thread = $this->create('App\Thread');
        $thread->visits()->reset();
        $this->assertSame(0, $thread->visits()->count());

        // visit thread
        $this->call('GET', $thread->path());
        $this->call('GET', $thread->path());

        //check on the visits number
        $this->assertEquals(2, $thread->visits()->count());
    }
}
