<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchTest extends TestCase
{

        /** @test */
        public function a_user_can_search_threads()
        {
            //thread already is using the searchable trait, and we are connected to algolia services in the scout and .env files
            config(['scout.driver' => 'algolia']);
    
            $this->create('App\Thread', [], 2);
            $this->create('App\Thread', ['body' => 'A thread with the foobar term.'], 2);
    
            do {
                // Account for latency.
                sleep(.55);
    
                $results = $this->getJson('/threads/search?q=foobar')->json()['data']; //['data'] because this is a paginated object
            } while (empty($results));
    
            $this->assertCount(2, $results);
    
            // Clean up the 4 test threads from algolia
            Thread::latest()->take(4)->unsearchable();
        }


}
