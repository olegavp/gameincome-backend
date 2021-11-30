<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAcceptIpLink extends Mailable
{
    use Queueable, SerializesModels;

    private $hash;

    public function __construct($hash)
    {
        return $this->hash = $hash;
    }


    public function build(): SendAcceptIpLink
    {
        return $this->view('sendAcceptIpLink')->with([
            'link' => $this->hash
        ]);
    }
}
