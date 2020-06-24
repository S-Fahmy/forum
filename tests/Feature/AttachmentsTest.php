<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentsTest extends TestCase
{


    /** @test */
    public function user_can_upload_attachments()
    {

        $this->signIn();

        // //creates file
        Storage::fake('users_uploads');
        $fakeFile = UploadedFile::fake()->image('nigg.pdf');

        //up it

        $attachmentData = $this->json('POST', '/api/users/' . auth()->id() . '/attachments', ['attached' =>  $fakeFile]);


        $this->assertDatabaseHas('attachments', ['url' => $attachmentData['url']]);

        Storage::disk('users_uploads')->assertExists(auth()->id() . '/attachments/' . $fakeFile->hashName());

        // Assert a file does not exist... for when we delete reply
        //   Storage::disk('avatars')->assertMissing('missing.jpg');
    }


    /** @test */
    public function attachments_can_be_attached_to_replies()
    {
        $this->signIn();

        // //creates file
        Storage::fake('users_uploads');
        $fakeFile = UploadedFile::fake()->image('nigg.pdf');

        //up it

        $attachmentData = $this->json('POST', '/api/users/' . auth()->id() . '/attachments', ['attached' =>  $fakeFile]);

        $thread = $this->create('App\Thread');
        $reply = $this->make('App\Reply');

        //post reply and tell it it has an attachment
        $this->assertDatabaseHas('attachments', ['post_type' => null]);

        $reply = $this->post($thread->path() . '/replies', $reply->toArray() + ['includesAttachments' => $attachmentData]);

        $this->assertDatabaseHas('attachments', ['post_type' => 'App\Reply']);
    }


    /** @test */
    public function attachments_that_belongs_posts_get_deleted_when_posts_do()
    {

        $this->signIn();

        // //creates file
        Storage::fake('users_uploads');
        $fakeFile = UploadedFile::fake()->image('nigg.pdf');

        //up it

        $attachmentData = $this->json('POST', '/api/users/' . auth()->id() . '/attachments', ['attached' =>  $fakeFile]);

        $thread = $this->create('App\Thread');
        $reply = $this->make('App\Reply');

        $reply = $this->post($thread->path() . '/replies', $reply->toArray() + ['includesAttachments' => $attachmentData]);

        $this->assertDatabaseHas('attachments', ['post_type' => 'App\Reply']);

        $this->delete("replies/{$reply['id']}");

        $this->assertDatabaseMissing('attachments', ['post_type' => 'App\Reply']);

        Storage::disk('users_uploads')->assertMissing(auth()->id() . '/attachments/' . $fakeFile->hashName());
    }


    /** @test */
    public function authorized_user_can_delete_his_attachments()
    {
        $this->signIn();

        // //creates file
        Storage::fake('users_uploads');
        $fakeFile = UploadedFile::fake()->image('nigg.pdf');

        //up it

        $attachmentData = $this->json('POST', 'api/users/' . auth()->id() . '/attachments', ['attached' =>  $fakeFile]);

        Storage::disk('users_uploads')->assertExists(auth()->id() . '/attachments/' . $fakeFile->hashName());
        $this->assertDatabaseHas('attachments', ['id' => $attachmentData['id']]);

        $this->delete('/api/attachments/' . $attachmentData['id']);

        Storage::disk('users_uploads')->assertMissing(auth()->id() . '/attachments/' . $fakeFile->hashName());
    }

    /** @test */
    public function unauth_user_cant_delete_attachments()
    {
        $this->signIn();
        $this->withExceptionHandling();
        $id = auth()->id();
        // //creates file
        Storage::fake('users_uploads');
        $fakeFile = UploadedFile::fake()->image('nigg.pdf');

        //up it

        $attachmentData = $this->json('POST', 'api/users/' . $id . '/attachments', ['attached' =>  $fakeFile]);


        $this->signIn();
        
        $this->delete('/api/attachments/' . $attachmentData['id'])->assertStatus(403);
    }
}
