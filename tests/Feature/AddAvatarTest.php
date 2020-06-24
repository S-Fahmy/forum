<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AddAvatarTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function only_members_can_add_avatars()
    {
        $this->withExceptionHandling();

        $this->json('POST', 'api/users/1/avatar')
            ->assertStatus(401);
    }

    /** @test */
    public function a_valid_avatar_must_be_provided()
    {
        $this->withExceptionHandling()->signIn();

        $this->json('POST', 'api/users/' . auth()->id() . '/avatar', [
            'avatar' => 'not-an-image'
        ])->assertStatus(422);
    }


    /** @test */
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        $this->signIn();

        Storage::fake('users_uploads');
        $fakeAvatarImg = UploadedFile::fake()->image('avatar.jpg');//fake image to upload

        $this->json('POST', 'api/users/' . auth()->id() . '/avatar', ['avatar' =>  $fakeAvatarImg ]);

         $this->assertEquals('images/avatars/' . $fakeAvatarImg->hashName(), auth()->user()->avatar_path);

        Storage::disk('users_uploads')->assertExists('images/avatars/' . $fakeAvatarImg->hashName());
    }
}
