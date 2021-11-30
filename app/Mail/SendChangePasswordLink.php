<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendChangePasswordLink extends Mailable
{
    use Queueable, SerializesModels;

    private $hash;

    public function __construct($hash)
    {
        return $this->hash = $hash;
    }


    public function build(): SendChangePasswordLink
    {
        return $this->view('sendChangePassword')->with([
            'hash' => $this->hash
        ]);
    }
}
