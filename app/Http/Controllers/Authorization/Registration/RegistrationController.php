<?php

namespace App\Http\Controllers\Authorization\Registration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\AcceptEmailRegistrationRequest;
use App\Http\Requests\Authorization\SendCodeRegistrationRequest;
use App\Http\Requests\Authorization\SetNicknameRegistrationRequest;
use App\Http\Requests\Once\EmailRequest;
use App\Http\Requests\Once\PasswordRequest;
use App\Http\Requests\User\PersonalArea\Security\PasswordRequest as newPasswordAndOldPasswordRequest;
use App\Http\Resources\User\UserResource;
use App\Jobs\SendChangePasswordLink;
use App\Jobs\SendCode;
use App\Models\User\Ip\UserIp;
use App\Models\User;
use App\Models\User\PersonalArea\Finance\UserBalance;
use App\Models\User\Verify\UserVerifyEmail;
use App\Http\Services\CheckingUserDeviceInputParameters\DefineDeviceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use OpenApi\Annotations as OA;

class RegistrationController extends Controller
{
    private DefineDeviceService $deviceInfo;
    private $user;
    private $token;

    public function __construct(DefineDeviceService $deviceInfo)
    {
        $this->deviceInfo = $deviceInfo;
    }

    /**
     * @OA\Post (
     *  tags={"OLD_Registration"},
     *  path="/authorization/registration",
     *  operationId="registration",
     *  summary="validates an account",
     *  @OA\Parameter(name="first_name",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *  @OA\Parameter(name="last_name",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *  @OA\Parameter(name="email",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *  @OA\Parameter(name="password",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Произошла ошибка во время первого этапа регистрации, попробуйте зарегистрироваться ещё раз, если ошибка остаётся, то обратитесь в поддержку, Спасибо!",
     *      )
     * )
     */
    public function sendCode(SendCodeRegistrationRequest $request): JsonResponse
    {
        try {
            $randCode = random_int(100000, 999999);
            $userVerify = new UserVerifyEmail;
            $userVerify->user_email = $request->email;
            $userVerify->user_name = $request->name;
            $userVerify->user_surname = $request->surname;
            $userVerify->user_password = Hash::make($request->password);
            $userVerify->code = $randCode;
            $userVerify->hash = Hash::make($request->email . $randCode . '@wwwl213');
            $userVerify->save();

            SendCode::dispatch($randCode, $request->email);

            return response()->json(['email' => $request->email, 'hash' => $userVerify->hash,
                'status' => 200], 200);
        } catch (\Throwable) {
            return response()->json(['error' => 'Произошла ошибка во время первого этапа регистрации, попробуйте зарегистрироваться ещё раз, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    /**
     *
     *
     * @OA\Post (
     *  tags={"OLD_Registration"},
     *  path="/authorization/accept-email",
     *  operationId="accept-email",
     *  summary="validates an account",
     *  @OA\Parameter(name="code",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *  @OA\Parameter(name="hash",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="error",
     *      )
     * )
     */
    public function acceptEmail(AcceptEmailRegistrationRequest $request): JsonResponse|UserResource
    {
        try {
            $accept = UserVerifyEmail::where(['code' => $request->code, 'hash' => $request->hash])->first();

            if ($accept == null) {
                return response()->json(['warning' => 'Вы ввели неверный код, попробуйте еще раз!',
                    'status' => 400], 400);
            }

            /*
            @return Array $var['device', 'browser']
            @throws Response
            */
            try {
                //Берём информацию о девайсе и IP пользователя
                $aboutDevice = $this->deviceInfo->getInfoAboutDevice();
            } catch (\Throwable) {
                return response()->json(['error' => 'Произошла ошибка при определении устройства, пожалуйста, попробуйте зайти на сайт с другого устройтсва, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }


            DB::transaction(function () use ($accept, $request, $aboutDevice) {
                $user = new User;
                $user->name = $accept->user_name;
                $user->surname = $accept->user_surname;
                $user->email = $accept->user_email;
                $user->nickname = $accept->user_name;
                $user->password = $accept->user_password;
                $user->code = $request->code;
                $user->save();
                $this->user = $user;

                $token = $user->createToken('token');
                $this->token = $token;

                $userIp = new UserIp;
                $userIp->user_id = $user->id;
                $userIp->token_id = $token->accessToken->id;
                $userIp->ip = $request->ip();
                $userIp->device = $aboutDevice['device'];
                $userIp->browser = $aboutDevice['browser'];
                $userIp->confirmed = 1;
                $userIp->save();

                $balance = new UserBalance;
                $balance->user_id = $user->id;
                $balance->save();

                UserVerifyEmail::where('user_email', $accept->user_email)->delete();
            });

            return (new UserResource($this->user))->additional(['token' => $this->token->plainTextToken, 'code' => $request->code,
                'status' => 201]);
        } catch (\Throwable) {
            return response()->json(['error' => 'Произошла ошибка во время второго этапа регистрации, попробуйте зарегистрироваться ещё раз, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function setNickname(SetNicknameRegistrationRequest $request): JsonResponse|UserResource
    {
        try {
            $user = User::where(['id' => $request->id, 'email' => $request->email, 'code' => $request->code])->first();

            if ($user === null) {
                return response()->json(['warning' => 'Пользователя, которому вы устанавливаете никнейм не существует!',
                    'status' => 400], 400);
            }

            $user->nickname = $request->nickname;
            $user->save();

            return new UserResource($user->load('balance'));
        } catch (\Throwable) {
            return response()->json(['error' => 'Произошла ошибка во время установки никнейма, попробуйте войти на сайт, так как ваш аккаунт был уже зарегистрирован, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    /**
     * @OA\Get (
     *  tags={"OLD_Registration"},
     *  path="/authorization/accept-ip/hash/{hash}",
     *  operationId="authorization-accept-ip",
     *  summary="validates an account",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="error",
     *      )
     * )
     */
    public function confirmDeviceAndIp($hash): JsonResponse|UserResource
    {
        try {
            //Ищем в пока не подтверждённых записях запись с новыми IP/устройстве для подтверждения по hash из ссылки письма
            $userIp = UserIp::query()->where(['hash' => $hash, 'token_id' => null, 'confirmed' => 0])->first();
            if ($userIp == null) {
                return response()->json(['warning' => 'Не удалось найти ваш IP/устройство в списке ожидающих подтверждения, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $user = User::query()->find($userIp->user_id);
            if ($user == null) {
                return response()->json(['warning' => 'Не удалось найти аккаунт, вероятно ошибка на сервере, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $token = $user->createToken('token');

            $userIp->update([
                'confirmed' => 1,
                'confirmed_at' => Carbon::now('Europe/Moscow')->toDateTimeString(),
                'token_id' => $token->accessToken->id
            ]);

            return (new UserResource($user))->additional([
                'token' => $token->plainTextToken,
                'message' => 'Вы успешно подтвердили новые входные данные! Сейчас мы вас перенаправим на главную страницу.',
                'status' => 200
            ]);
        } catch (\Throwable) {
            return response()->json(['error' => 'Произошла ошибка во время подтверждения новых данных, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    /**
     * @OA\Get (
     *  tags={"OLD_Registration"},
     *  path="/send-link-for-change-password",
     *  operationId="send-link-for-change-password",
     *  summary="validates an account",
     *  @OA\Parameter(name="email",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="error",
     *      )
     * )
     */
    public function linkForChangePassword(EmailRequest $request): JsonResponse
    {
        try {
            //Добавляем в таблицу временных изменений и новых аккаунтов запись, которая после смены пароля удалится.
            $time = Carbon::now()->toDateTimeString();
            $email = $request->email;

            $user = User::query()->where('email', $email)->first();
            if ($user == null) {
                return response()->json(['warning' => 'Пользователя с данным email не существует!',
                    'status' => 400], 400);
            }

            $hash = hash("sha256", $time . $email . rand(0, 99), false);

            $userVerify = new UserVerifyEmail;
            $userVerify->user_email = $email;
            $userVerify->hash = $hash;
            $userVerify->save();

            SendChangePasswordLink::dispatch($email, $hash);

            return response()->json(['message' => 'На ваш Email отправлено письмо!',
                'status' => 200], 200);
        } catch (\Throwable) {
            return response()->json(['error' => 'Произошла ошибка во время отправки письма для восстановления пароля, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

    /**
     * @OA\Get (
     *  tags={"OLD_Registration"},
     *  path="/change-password/hash/{hash}",
     *  operationId="send-link-for-change-password",
     *  summary="validates an account",
     *  @OA\Parameter(name="email",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="error",
     *      )
     * )
     */
    public function changePasswordOnMailLink($hash, PasswordRequest $request): JsonResponse
    {
        try
        {
            //Ищем в таблице временных изменений и новых аккаунтов запись по hash, для которой нужно изменить пароль,
            //а затем удалить
            $userVerifyEmail = UserVerifyEmail::query()->where('hash', $hash)->first();
            if ($userVerifyEmail == null)
            {
                return response()->json(['error' => 'На данный момент невозможно изменить пароль, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $user = User::query()->where('email', $userVerifyEmail->user_email)->first();
            if ($user == null)
            {
                return response()->json(['error' => 'На данный момент невозможно изменить пароль, попробуйте позже, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            /*
            @return Array $var['device', 'browser']
            @throws Json
            */
            try
            {
                //Берём информацию о девайсе и IP пользователя
                $aboutDevice = $this->deviceInfo->getInfoAboutDevice();
            }
            catch (\Throwable)
            {
                return response()->json(['error' => 'Произошла ошибка при определении устройства, пожалуйста, попробуйте зайти на сайт с другого устройства, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $token = $user->createToken('token');

            DB::transaction(function () use($user, $request, $userVerifyEmail, $aboutDevice, $token)
            {
                $user->update(['password' => Hash::make($request->password)]);

                $userVerifyEmail->delete();

                //Выше изменили пароль, теперь удаляем все сессии и токены к ним и создаём новые для нового входа с новым паролем
                UserIp::query()->where('user_id', $user->id)->forceDelete();

                PersonalAccessToken::query()->where('tokenable_id', $user->id)->where('id','!=', $token->accessToken->id)->delete();

                $newUserIp = new UserIp;
                $newUserIp->user_id = $user->id;
                $newUserIp->token_id = $token->accessToken->id;
                $newUserIp->ip = $request->ip();
                $newUserIp->device = $aboutDevice['device'];
                $newUserIp->browser = $aboutDevice['browser'];
                $newUserIp->confirmed = 1;

                $newUserIp->save();
            });

            return response()->json(['message' => 'Вы успешно изменили пароль от своей учётной записи! Все сессии доступов, были аннулированы. Теперь попробуйте войти с новым паролем.',
                'status' => 200], 200);
        }
        catch (\Throwable)
        {
            if (isset($token))
            {
                PersonalAccessToken::query()->find($token->accessToken->id)->delete();
            }

            return response()->json(['error' => 'Произошла ошибка во время изменения пароля, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function changePasswordInPersonalArea(newPasswordAndOldPasswordRequest $request): JsonResponse
    {
        try {
            //Проверяем по token, жива ли еще сессия, чтобы пользователь мог понять, успел он или нет сменить пароль.
            $user = User::query()->find($request->user()->id);
            if ($user == null)
            {
                return response()->json(['error' => 'Произошла ошибка! Данная сессия была аннулирована. Пожалуйста, обратитесь в поддержку!',
                    'status' => 400], 400);
            }

            if (!Hash::check($request->password, $user->password))
            {
                return response()->json(['warning' => 'Введён неверный старый пароль!',
                    'status' => 400], 400);
            }

            DB::transaction(function () use ($request, $user)
            {
                $user->update(['password' => Hash::make($request->newPassword)]);

                //Выше изменили пароль, теперь удаляем все сессии и токены к ним кроме нынешней сессии и токена
                $tokenId = stristr($request->bearerToken(), '|', true);

                UserIp::query()->where('user_id', $user->id)
                    ->where('token_id','!=', $tokenId)->forceDelete();

                PersonalAccessToken::query()->where('tokenable_id', $user->id)
                    ->where('id','!=', $tokenId)->delete();
            });

            return response()->json(['message' => 'Вы успешно изменили пароль от своей учётной записи! Все сессии доступов, кроме нынешнего, были аннулированы.',
                'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json([
                'error' => 'Произошла ошибка во время изменения пароля. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

}
