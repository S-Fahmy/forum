<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_fetch_their_most_recent_reply()
    {
        $user = $this->create('App\User');

        $reply = $this->create('App\Reply', ['user_id' => $user->id]);

        $this->assertEquals($reply->id, $user->lastReply->id);
    }

    /** @test */
    function a_user_can_determine_their_avatar_path()
    {
        $user = $this->create('App\User');

        $this->assertEquals('images/avatars/default-avatar.png', $user->avatar_path);

        $user->avatar_path = 'avatars/me.jpg';

        $this->assertEquals('avatars/me.jpg', $user->avatar_path);
    }
}
