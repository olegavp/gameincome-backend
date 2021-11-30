<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCode extends Mailable
{
    use Queueable, SerializesModels;

    private $code;

    public function __construct($code)
    {
        return $this->code = $code;
    }


    public function build(): SendCode
    {
        return $this->view('sendCode')->with([
            'code' => $this->code
        ]);
    }
}
