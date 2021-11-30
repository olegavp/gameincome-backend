<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Подтверждение ip адреса по ссылке</title>
</head>
<body>
    <h1>Перейдите по ссылке, чтобы подтвердить ip адрес</h1>
    <a href="{{ env('APP_URL_FRONT') }}/public/api/authorization/accept-ip/hash/{{$link}}">Нажмите сюда</a>
    <p>Если вы не заходили в аккаунт в данное время и с другого устройства, то срочно смените пароль в личном кабинете и напишите в поддержку!</p>
</body>
</html>
