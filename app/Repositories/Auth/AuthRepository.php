<?php

namespace App\Repositories\Auth;

use App\Helpers\ApiResponse;
use App\Http\Traits\OTPTrait;
use App\Models\SpecialistArea;
use App\Models\SpecialistType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Class AuthRepository
 */
class AuthRepository extends BaseRepository
{
    use OTPTrait;

    /**
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }


    public function getFieldsSearchable(): array
    {
        return [];
    }


    /**
     * @param $request
     * @return Application|ResponseFactory|Response
     */
    public function loginPublicUser(Request $request)
    {

        $user = User::wherePhone($request['phone'])->first();
        if (!$user) {
            return ['success' => false, 'message' => 'mobile number not found'];
        }

        $res = $this->verifyOTP($request);

        if (!$res['success']) {
            return ['success' => false, 'message' => 'mobile verification faild'];
        }

        \Auth::login($user);

        return $user;
    }


    /**
     * @param $request
     * @return Application|ResponseFactory|Response
     */
    public function loginPrivetUser(Request $request)
    {
        $user = User::whereEmail($request['email'])->first();

        $res = (array) $this->sendOTP($user['phone'], $user['country_code']);

        return $res;
    }

    /**
     * @param $request
     * @return Application|ResponseFactory|Response
     */
    public function loginPrivetUserConfirm(Request $request)
    {
        $user = User::whereEmail($request['email'])->first();
        if (!$user) {
            return ['success' => false, 'message' => 'email not found'];
        }

        $user['otp'] = $request['otp'];
        
        $res = $this->verifyOTP($user);

        if (!$res['success']) {
            return ['success' => false, 'message' => 'mobile verification faild'];
        }

        $request = ['email' => $request['email'], 'password' => $request['password']];

        if (!auth()->attempt($request)) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        return auth()->user();
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function registerPublicUser(Request $request)
    {
        // if ($this->request->hasFile('image')) {
        // $imageName = time() . '.' . $request->image->extension();
        //     $request->image->move(storage_path('app/public/images/avatar'), $imageName);
        //     $request["avatar"] = $imageName;
        // }
        $res =  $this->verifyOTP($request);


        if (!$res['success']) {
            return ['success' => false, 'message' => 'mobile verification faild'];
        }

        $user = $this->create($request->all());


        try {
            //TODO need job
            // if (\config("app.enable_email_verification")) {
            //     $user->sendEmailVerificationNotification();
            // }
            // //need job
            // if (\config("app.enable_phone_verification")) {
            //     $user->sendPhoneVerificationOTP();
            // }
        } catch (\Throwable $th) {
            return $user;
        }
        return $user;
    }


    public function registerPrivetUser(Request $request)
    {
        // if ($this->request->hasFile('image')) {
        // $imageName = time() . '.' . $request->image->extension();
        //     $request->image->move(storage_path('app/public/images/avatar'), $imageName);
        //     $request["avatar"] = $imageName;
        // }
        $res =  $this->verifyOTP($request);

        if (!$res['success']) {
            return ['success' => false, 'message' => 'mobile verification faild'];
        }

        $user = $this->create(array_merge(
            $request->all(),
            ['password' => Hash::make($request->password)],
        ));


        try {
            //TODO need job
            // if (\config("app.enable_email_verification")) {
            //     $user->sendEmailVerificationNotification();
            // }
            // //need job
            // if (\config("app.enable_phone_verification")) {
            //     $user->sendPhoneVerificationOTP();
            // }
        } catch (\Throwable $th) {
            return $user;
        }
        return $user;
    }


    public function addUserArea($user_id, $area_id)
    {
        SpecialistArea::create(['user_id' => $user_id, 'area_id' => $area_id]);
    }

    public function addUserSpecial($user_id, $area_id)
    {
        SpecialistType::create(['user_id' => $user_id, 'special_type_id' => $area_id]);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function changePassword($request): JsonResponse
    {
        return \response()->json("ok");
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), ['email' => 'required|email']);

        if ($validator) {
            // return ApiResponse::apiFormatValidation($validator);
        }

        $status = Password::sendResetLink(
            ['email' => 'alysha57@example.net']
        );

        return  "Email sent.";
    }
    public function resetView(Request $request)
    {
        return \view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator) {
            return ApiResponse::apiFormatValidation($validator);
        }
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return \view("auth.succses-reset-password");
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function verifyEmail($request): JsonResponse
    {
        return \response()->json($request);
    }
    /**
     * @param $request
     * @return JsonResponse
     */
    public function verifiedPhone($request): JsonResponse
    {
        return \response()->json("ok");
    }

    /**
     * Send OTP for user for verification
     * @param $request
     * @return JsonResponse
     */
    public function sendCode($request): JsonResponse
    {
        return \response()->json("ok");
    }



    public function verifyCode($request)
    {
        return \response()->json("ok");
    }


    /**
     * @return JsonResponse
     */
    public function getProfile(): JsonResponse
    {
        return \response()->json("ok");
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function editProfile($request): JsonResponse
    {
        return \response()->json("ok");
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function logout($user): JsonResponse
    {
        return \response()->json($user->tokens()->delete());
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function updateDeviceToken($request): JsonResponse
    {
        return \response()->json("ok");
    }
}
