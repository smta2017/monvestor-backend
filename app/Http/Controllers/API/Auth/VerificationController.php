<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\AppBaseController;
use App\Http\Traits\OTPTrait;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends AppBaseController
{
    use OTPTrait;


    public function sendMobileOTP(Request $request)
    {
    return   $x= 'sss';
        // $res =  $this->sendOTP($request->phone_number, $request->country_code);
        // return  $this->sendResponse($res, "success");
    }


    public function confirmOTP($phone_number, $otp)
    {
        $res =  $this->verifyOTP($phone_number, $otp);
        if ($res->valid == true) {
            return  $this->sendResponse($res, 'success');
        } else {
            return  $this->sendError('fail', false);
        }
    }

    public function verifyEmail($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return $this->respondUnAuthorizedRequest(253);
        }
        $user = User::findOrfail($user_id);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return  $this->sendResponse($user, 'success');
    }
}
