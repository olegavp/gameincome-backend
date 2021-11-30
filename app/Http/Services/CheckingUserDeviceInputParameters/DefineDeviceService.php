<?php

namespace App\Http\Services\CheckingUserDeviceInputParameters;

use Jenssegers\Agent\Agent;


class DefineDeviceService
{
    public function getInfoAboutDevice(): array
    {
        $agent = new Agent();
        if ($agent->isPhone())
        {
            $device = $agent->device();
            $browser = $agent->browser();
            return ['device' => $device, 'browser' => $browser];
        }

        if ($agent->isDesktop())
        {
            $device = $agent->platform();
            $browser = $agent->browser();
            return ['device' => $device, 'browser' => $browser];
        }

        return ['device' => null, 'browser' => null];
    }
}
