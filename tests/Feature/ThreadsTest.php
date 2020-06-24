<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;



class ThreadsTest extends TestCase
{
    use DatabaseMigrations;






    /** @test */
    public function a_User_Can_Browse_Threads()
    {

        $thread = factory('App\Thread')->create();

        $this->get('/threads')->assertSee($thread->title);
    }


    /** @test */
    public function a_User_Can_Browse_a_Thread()
    {

        $thread = factory('App\Thread')->create();
        $this->get($thread->path())->assertSee($thread->title);
    }

    /** @test */
    public function a_thread_belongs_to_a_channel()
    {

        $thread = $this->create('App\Thread');
        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    /** @test */
    public function a_thread_return_full_path()
    {

        $thread = $this->create('App\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path());
    }




    /** @test */
    public function a_user_can_filter_threads_by_tags()
    {


        //create a channel tag
        $channel =  $this->create('App\Channel');
        //associate and create a thread with it
        $associatedThread = $this->create('App\Thread', ['channel_id' => $channel->id]);
        //create a none associated thread
        $unassociatedThread = $this->create('App\Thread', ['channel_id' => 93837]);

        //assert we see the related and not see the unrelated
        $this->get('/threads/' . $channel->slug)->assertSee($associatedThread->title)
            ->assertDontSee($unassociatedThread->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {

        $thread2 = $this->create('App\Thread');
        $this->create('App\Reply', ['thread_id' => $thread2->id], 2);


        $thread3 = $this->create('App\Thread');
        $this->create('App\Reply', ['thread_id' => $thread3->id], 3);


        $thread0 = $this->create('App\Thread');


        $response = $this->getJson('threads?popular=1')->json();

        //$response is a pagination object so we call for the ['data'] which will return the data on the first page
        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /** @test */
    public function a_user_can_filter_threads_by_unanswered()
    {
        $thread = $this->create('App\Thread');
        // $this->create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1, $response['data']); // get the data from the pagination object
    }



    //this data gets used by vue
    /** @test */
    public function a_user_can_fetch_replies_for_a_thread()
    {

        $thread = $this->create('App\Thread');
        $reply = $this->create('App\Reply', ['thread_id' => $thread->id], 11);

        $response = $this->getJson($thread->path() . '/replies')->json();

        //dd($response);

        //assert that we get the right number of pagination, 10 per page is currently hardcoded
        $this->assertCount(10, $response['data']);
        //response comes wth pagination data
        $this->assertEquals(11, $response['total']);
    }


    /** @test */
    function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $user = $this->signIn();

        $thread = $this->create('App\Thread');


        $this->assertTrue($thread->getHasUpdatesForAttribute());

        $user->read($thread);

        $this->assertFalse($thread->getHasUpdatesForAttribute());
    }


    /** @test */
    function created_thread_has_path_attribute(){
        $this->signIn();
        $this->withExceptionHandling();

        $thread = $this->make('App\Thread');
       
        $this->assertNotEmpty( $thread->path);


    }

}
