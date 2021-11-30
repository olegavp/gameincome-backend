<?php

namespace App\Http\Services\CheckingUserDeviceInputParameters;

use App\Jobs\SendAcceptIpLink;
use App\Models\User\Ip\UserIp;
use Illuminate\Support\Carbon;


class FindOrCreateDeviceAndIpService
{
    private AddDeviceAndIpService $addDeviceAndIpService;
    private DefineDeviceService $deviceInfo;

    public function __construct(AddDeviceAndIpService $addDeviceAndIpService, DefineDeviceService $deviceInfo)
    {
        $this->addDeviceAndIpService = $addDeviceAndIpService;
        $this->deviceInfo = $deviceInfo;
    }

    public function findOrCreate($user, $request): array|UserIp
    {
        try
        {
            $ip = $request->ip();

            /*
            @return Array $var['device', 'browser']
            @throws Array ['type', 'message', 'status']
            */
            try
            {
                //Берём информацию о девайсе и IP пользователя
                $aboutDevice = $this->deviceInfo->getInfoAboutDevice();
            }
            catch (\Throwable)
            {
                return [
                    'type' => 'error',
                    'message' => 'Произошла ошибка при определении устройства, пожалуйста, попробуйте зайти на сайт с другого устройства, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400
                ];
            }

            //Ищем у модели User есть ли записи с данным IP адресом + данным девайсом
            $hasIpAndDevice = $user->userIp()->where(['ip' => $ip, 'device' => $aboutDevice['device']])->first();

            //Если нет, то добавляем в новые IP адреса неподтвержденные новые данные
            if ($hasIpAndDevice == null)
            {
                $time = Carbon::now()->toDateTimeString();
                $hash = hash("sha256", $time . $request->email . rand(0, 99), false);

                $ipAndDeviceInfo = array('user_id' => $user->id, 'ip' => $ip, 'device' => $aboutDevice['device'], 'browser' => $aboutDevice['browser'], 'hash' => $hash);

                /*
                @param Array $ipAndDeviceInfo
                @return UserIp(Model)
                @throws Array ['type', 'message', 'status']
                */
                try
                {
                    //Само добавление пока не подтверждённых данных
                    $this->addDeviceAndIpService->add($ipAndDeviceInfo);
                }
                catch (\Throwable)
                {
                    return [
                        'type' => 'error',
                        'message' => 'Произошла ошибка при добавлении нового IP/устройства, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                        'status' => 400
                    ];
                }

                //Отправка на почту сообщения со ссылкой-подтверждением новых данных IP и девайса
                SendAcceptIpLink::dispatch($request->email, $hash);

                return [
                    'type' => 'message',
                    'message' => 'Вы входите в аккаунт с нового IP адреса или устройства! В целях безопасности, мы отправили вам на электронную почту письмо с ссылкой, по которой нужно будет перейти, чтобы подтвердить новые входные данные.',
                    'status' => 200
                ];
            }

            //Если у пользователя закрылась страница и он снова входит в аккаунт, где уже было отправлено сообщение на почту
            //и добавлены пока не подтверждённых данных, говорим, что сообщение уже отправлено
            if ($hasIpAndDevice != null and $hasIpAndDevice->confirmed == 0)
            {
                return [
                    'type' => 'warning',
                    'message' => 'Данный IP адрес или устройство пока не подтверждены! Перейдите по ссылке в письме, которое мы вам отправили на электронную почту, чтобы подтвердить их.',
                    'status' => 400
                ];
            }

            //Если входные данные не новые, то просто отдаём информацию о token для данного IP и устройства
            return $hasIpAndDevice;
        }
        catch (\Throwable)
        {
            return [
                'type' => 'warning',
                'message' => 'Произошла ошибка во время работы сервиса обнаружения и сохранения данных об устройстве входящего пользователя, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400
            ];
        }
    }
}
