<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PleaseConfirmYourEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public $user; //for a Mail object all public variable are available for the emails.confirm

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {   //sent from the newRegistered event listener
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.confirm-email');
    }
}
