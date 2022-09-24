<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait OTPTrait
{
    public static function sendOTP($phone, $countryCode = '+20')
    {
        if (env("APP_ENV") == 'local') {
            return ['success' => true];
        }


        $VIA = 'sms';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.authy.com/protected/json/phones/verification/start');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "via=$VIA&phone_number=$phone&country_code=$countryCode&locale='en'&code_length=6");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'X-Authy-Api-Key: V5j0gNwPJQuI97V0qI5MJTMOBhNVTu13';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $result1 = json_decode($result);

        return $result1;
    }

    public static function verifyOTP($request)
    {
        if (env("APP_ENV") == 'local') {
            return ['success' => true];
        }

        $USER_PHONE = $request['phone'];
        $COUNTRY_CODE = $request['country_code'];
        $VERIFICATION_CODE = $request->otp;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.authy.com/protected/json/phones/verification/check');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "phone_number=$USER_PHONE&country_code=$COUNTRY_CODE&verification_code=$VERIFICATION_CODE");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'X-Authy-Api-Key: V5j0gNwPJQuI97V0qI5MJTMOBhNVTu13';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $result = json_decode($result, true);

        return $result;
        // if ($result->success) {
        //     return $this->showRegistrationForm($USER_PHONE);
        // } else {
        //     if ($result->error_code='60022') {
        //         return view(backpack_view('auth.confirmotp'), ['error'=>$result->message,'phone' => $USER_PHONE, 'country_code' => $COUNTRY_CODE]);
        //     }
        //     else if ($result->error_code='60023') {

        //     }
        //     return $result;
        // }
    }
}
