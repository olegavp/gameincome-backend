<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendChangePasswordLink as MailSendChangePasswordLink;

class SendChangePasswordLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $email;
    private $hash;

    public function __construct($email, $hash)
    {
        $this->email = $email;
        $this->hash = $hash;
    }


    public function handle()
    {
        Mail::to($this->email)->send(new MailSendChangePasswordLink($this->hash));
    }


    public function failed(\Throwable $e){
        info('Произошла ошибка во время отправления письма на почту. Информация об ошибке: ' . $e);
    }
}
