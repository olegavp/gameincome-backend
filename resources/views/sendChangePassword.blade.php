<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Подтверждение смены пароля по ссылке</title>
</head>
<body>
    <h1>Перейдите по ссылке, чтобы изменить пароль</h1>
    <a href="{{ env('APP_URL_FRONT') }}/auth/password-reset/hash/{{$hash}}">Нажмите сюда</a>
    <p>Если вы не делали запрос на смену пароля, то срочно смените его в личном кабинете и напишите в поддержку!</p>
</body>
</html>
