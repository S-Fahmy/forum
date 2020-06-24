<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        //manually fire an event where the listener create and send an email.
        //this event gets fired automatically from the registerController class
        event(new Registered($this->create('App\User')));

        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }

    /** @test */
    public function a_user_can_fully_confirm_their_email()
    {
        Mail::fake();

        //register form that creates a new user
        $this->post('/register', [
            'name' => 'john',
            'email' => 'sf@example.com',
            'password' => 'foobar111',
            'password_confirmation' => 'foobar111'
        ]);

        $user = User::whereName('john')->first();
        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token); //random token generated in the registerController create method


        //this route simulates the user clicking the confirmation link in the email the app send
        $this->get(route('register.confirm', ['token' => $user->confirmation_token]))
            ->assertRedirect(route('threads'));

        $this->assertTrue($user->fresh()->confirmed);
        $this->assertNull($user->fresh()->confirmation_token);

    }

    /** @test */
    function confirming_an_invalid_token()
    {
        $this->get(route('register.confirm', ['token' => 'invalid']))
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', ['message' => 'Unknown Account!', 'level' => 'danger']);
    }
}
