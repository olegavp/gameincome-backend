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
//        $response = Http::withHeaders([
//            'Authorization' => 'JD1q2qj0cd9cDOQdOoEaQvdugiDAvQp6kglb',
//            'accept' => 'application/json',
//            'Content-Type' => 'multipart/form-data'
//        ])
//            ->post('https://api.smtp.bz/v1/smtp/send', [
//                'from' => 'info@gameincome.com',
//                'name' => 'GameInCome',
//                'subject' => 'Verification code',
//                'to' => $this->email,
//                'html' => '<!doctype html>
//                            <html lang="ru">
//                            <head>
//                                <meta charset="UTF-8">
//                                <meta name="viewport"
//                                      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
//                                <meta http-equiv="X-UA-Compatible" content="ie=edge">
//                                <title>Подтверждение регистрации с помощью кода</title>
//                            </head>
//                            <body>
//                                <h1>Вы подтверждаете регистрацию на платформе GameInCome</h1>
//                                <h2>{{ $this->randCode }}</h2>
//                            </body>
//                            </html>'
//            ]);
        Mail::to($this->email)->send(new MailSendCode($this->randCode));
    }


    public function failed(\Throwable $exception){
        info('Произошла ошибка во время отправления письма на почту. Информация об ошибке: ' . $exception);
    }
}
