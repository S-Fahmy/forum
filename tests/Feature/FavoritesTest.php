<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PhpParser\Node\Stmt\TryCatch;
use Tests\TestCase;

class FavoritesTest extends TestCase
{

    /** @test */
    public function an_auth_user_can_favorite_threads()
    {   

        $this->signIn();

        $reply = $this->create('App\Reply'); //creates thread

        //if i post to favorites endpoint

        $this->post('/replies/'.$reply->id.'/favorites');

        //add it the database
        $this->assertCount(1 , $reply->favorites);

 
    }

    
    /** @test */
    public function an_auth_user_can_unfavorite_threads()
    {   

        $this->signIn();

        $reply = $this->create('App\Reply'); //creates thread

        //if i post to favorites endpoint

        $this->post('/replies/'.$reply->id.'/favorites');

        //add it the database
        $this->assertCount(1 , $reply->favorites);
        //deletes
        $this->delete('/replies/'.$reply->id.'/favorites');

        //get a fresh instance because favorites are eager loaded in the reply model using $with
        $this->assertCount(0 , $reply->fresh()->favorites);


 
    }



     /** @test */
     public function an_auth_user_can_favorite_one_thread()
     {   
 
         $this->signIn();
 
         $reply = $this->create('App\Reply'); //creates thread
 
         //if i post to favorites endpoint
 
         try {
            $this->post('/replies/'.$reply->id.'/favorites');
            $this->post('/replies/'.$reply->id.'/favorites');
   
            $this->post('/replies/'.$reply->id.'/favorites');
   
         } catch (\Throwable $th) {
                $this->fail('didnt expect to see this twice '.$th );
         }
     
 
         //add it the database
         $this->assertCount(1 , $reply->favorites);
 
     }
}
