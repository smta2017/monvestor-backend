<?php


namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\RestException as exception;
use Twilio\Rest\Api\V2010\Account\TwilioException as TwilioException;
// use Twilio\Rest\Client;
use GuzzleHttp\Client;

trait SMSTrait
{

    private static function _send($peyload_token, $route)
    {
        $client = new Client();
        $full_url = $route;
        $res = $client->request('POST', $full_url, ['headers' => ['Authorization' => 'Basic T3Rsb2JGcmVlTGFuY2VyOnNvenQ5M0UuNSsqPWYoNDo0NzI=', 'Content-Type' => 'application/json'], 'body' => $peyload_token]);
        return $res->getBody();
    }


    public static function sendSmsService($phone, $code, $lang)
    {
        $route = "https://api.connekio.com/sms/single";

        $lang == 'ar' ? $body = "رمز التحقق لحساب ركنة الخاص بك هو " : $body = "Your Raknah Account Verification code is ";

        $payload = [
            "account_id" => 472,
            "text"       => $body . ':' . $code ,
            "msisdn"     => $phone,
            "mnc"        => "01",
            "mcc"        => "602",
            "sender"     => "RAKNAH"
        ];

        $payload = json_encode($payload);
        $res =  json_decode(self::_send($payload, $route), true);
        return $res;
    }


    public static function sendNonUser($phone, $lang)
    {
        $route = "https://api.connekio.com/sms/single";

        $lang == 'ar' ? $body = "شكر لاستخدامك الخدمة , يكمنك تنزيل برنامج ركنة لحجز المسبق" : $body = "Thanks For using our service, Install Raknah so you can book before arrive ";

        $payload = [
            "account_id" => 472,
            "text"       => $body,
            "msisdn"     => $phone,
            "mnc"        => "01",
            "mcc"        => "602",
            "sender"     => "RAKNAH"
        ];

        $payload = json_encode($payload);
        $res =  json_decode(self::_send($payload, $route), true);
        Log::info($payload);
        return $res;
    }
}
