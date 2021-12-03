<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

//use Illuminate\Support\Facades\Mail;
use App\Mail\SendCode as MailSendCode;


class SendCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    protected $randCode;
    private $email;

    public function __construct($randCode, $email)
    {
        $this->randCode = $randCode;
        $this->email = $email;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new MailSendCode($this->randCode));
    }

    public function failed(\Throwable $exception)
    {
        info('Произошла ошибка во время отправления письма на почту. Информация об ошибке: ' . $exception);
    }
}
