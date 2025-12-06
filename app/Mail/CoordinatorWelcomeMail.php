<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoordinatorWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $password;
    public $eventNames;

    public function __construct($name, $email, $password, $eventNames)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->eventNames = $eventNames;
    }

    public function build()
    {
        return $this->subject('🎉 Coordinator Account Created - 818 Uni Event')
                    ->view('emails.coordinator_welcome');
    }
}
