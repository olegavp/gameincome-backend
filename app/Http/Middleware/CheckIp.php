<?php

namespace App\Http\Middleware;

use App\Jobs\SendAcceptIpLink;
use App\Models\User\Ip\UserIp;
use App\Http\Services\CheckingUserDeviceInputParameters\AddIpService;
use App\Http\Services\CheckingUserDeviceInputParameters\DefineDeviceService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckIp
{
    private AddIpService $data;
    private DefineDeviceService $deviceInfo;

    public function __construct(AddIpService $data, DefineDeviceService $deviceInfo)
    {
        $this->data = $data;
        $this->deviceInfo = $deviceInfo;
    }


    public function handle(Request $request, Closure $next)
    {
        try
        {
            $aboutDevice = $this->deviceInfo->getInfoAboutDevice();
        }
        catch (\Throwable $e)
        {
            return response()->json(['error' => 'Произошла ошибка при определении устройства, пожалуйста, попробуйте зайти на сайт с другого устройства! Текст ошибки: ' . $e], 200);
        }

        $hasIp = UserIp::query()->where(['user_id' => $request->user()->id, 'ip' => $request->ip()])->get();
        $hasDevice = UserIp::query()->where(['user_id' => $request->user()->id, 'device' => $aboutDevice->original['device']])->get();

        if ($hasIp->isEmpty() or $hasDevice->isEmpty())
        {
            $time = Carbon::now()->toDateTimeString();
            $hash = hash("sha256", $time . $request->user()->email, false);

            $data = array('user_id' => $request->user()->id, 'ip' => $request->ip(), 'device' => $aboutDevice->original['device'], 'browser' => $aboutDevice->original['browser'], 'hash' => $hash);
            try
            {
                $this->data->addIp($data);
            }
            catch (\Throwable $e)
            {
                return response()->json(['error' => 'Произошла ошибка при добавлении нового IP/устройства, пожалуйста, обратитесь в поддержку! Текст ошибки: ' . $e], 200);
            }

            try
            {
                SendAcceptIpLink::dispatch($request->user()->email, $hash);
            }
            catch (\Throwable $e)
            {
                return response()->json(['error' => 'Произошла ошибка во время отправления письма на почту, пожалуйста, обратитесь в поддержку! Текст ошибки: ' . $e], 200);
            }

            return response()->json(['warning' => 'Вы входите в аккаунт с нового IP адреса или устройства! В целях безопасности, мы отправили вам на электронную почту письмо с ссылкой, по которой нужно будет перейти, чтобы подтвердить новые входные данные.'], 200);
        }
        elseif (UserIp::query()->where(['ip' => $request->ip(), 'confirmed' => 0])->get()->isNotEmpty() or UserIp::query()->where(['device' => $aboutDevice->original['device'], 'confirmed' => 0])->get()->isNotEmpty())
        {
            return response()->json(['warning' => 'Данный IP адрес или устройство пока не подтверждены! Перейдите по ссылке в письме, которое мы вам отправили на электронную почту, чтобы подтвердить их.'], 200);
        }
        else
        {
            return $next($request);
        }
    }
}
