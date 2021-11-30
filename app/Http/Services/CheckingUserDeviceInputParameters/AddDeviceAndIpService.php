<?php

namespace App\Http\Services\CheckingUserDeviceInputParameters;

use App\Models\User\Ip\UserIp;

class AddDeviceAndIpService
{
    public function add($data): UserIp
    {
        $userIp = new UserIp();
        $userIp->user_id = $data['user_id'];
        $userIp->ip = $data['ip'];
        $userIp->device = $data['device'];
        $userIp->browser = $data['browser'];
        $userIp->hash = $data['hash'];
        $userIp->save();

        return $userIp;
    }
}
