<?php

namespace App\Http\Controllers\User\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PersonalArea\Profile\EditAvatarRequest;
use App\Http\Requests\User\PersonalArea\Profile\EditInfoRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class EditProfileInfoController extends Controller
{
    public function editProfileInfo(EditInfoRequest $request): JsonResponse|UserResource
    {
        try {
            $user = $request->user();
            $user->update($request->validated());
            return (new UserResource($user))
                ->additional(['message' => 'Вы успешно изменили свои учётные данные!',
                    'status' => 201]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время изменения информации профиля. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function editProfileAvatar(EditAvatarRequest $request): JsonResponse|UserResource
    {
        try {
            $envPath = env('URL_FOR_FILES');
            $user = $request->user();
            DB::transaction(function () use ($request, $user, $envPath)
            {
                $filenameWithExt = $request->file('avatar')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('avatar')->getClientOriginalExtension();
                $fileNameToStore = "avatar/".$filename."_".time().".".$extension;
                $request->file('avatar')->storeAs('public/avatars', $fileNameToStore);

                $user->avatar = $envPath . '/storage/avatars/' . $fileNameToStore;
                $user->save();
            });

            return (new UserResource($user))->additional(['message' => 'Вы успешно изменили свой аватар!',
                'status' => 201]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время изменения информации профиля. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
