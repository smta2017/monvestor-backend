<?php

namespace App\Http\Traits;

use GuzzleHttp\Client;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

trait SMSTrait
{
    public $clint_header;
    public $clint_body;
    public $clint_form_params;
    public $clint_url;
    public $clint_method;

    public function _send()
    {
        $client = new Client();
        $res = $client->request($this->clint_method, $this->clint_url, $this->clint_form_params, $this->clint_header, $this->clint_body);
        return $res->getBody();
    }


    public function sendSms($number, $message)
    {
        return $this->sendTwilioSMS($number, $message);
    }

    public function sendTwilioSMS($number, $message)
    {
        $sms = [
            "Body" => $message,
            "From" => env("TWILIO_SENDER_PHONE"),
            "To" => "+2" . $number
        ];

        if (app()->environment() === 'production' || app()->environment() === 'development' || app()->environment() === 'local') {
            $SID = env("TWILIO_SID");
            $token = env("TWILIO_TOKEN");

            $this->clint_method = 'post';
            $this->clint_url = 'https://api.twilio.com/2010-04-01/Accounts/' . $SID . '/Messages.json';
            $this->clint_form_params = [
                "form_params" => $sms,
                'auth' => [$SID, $token]
            ];
            $res = json_decode($this->_send());
        }

        return $res;
    }

    public function sendOTP($number)
    {
        $data = [
            "Channel" => 'sms',
            "To" => "+2" . $number
        ];

        if (app()->environment() === 'production' || app()->environment() === 'development' || app()->environment() === 'local') {
            $SSID = env("TWILIO_SSID");
            $SID = env("TWILIO_SID");
            $token = env("TWILIO_TOKEN");

            $this->clint_method = 'post';
            $this->clint_url = 'https://verify.twilio.com/v2/Services/' . $SSID . '/Verifications';
            $this->clint_form_params = [
                "form_params" => $data,
                'auth' => [$SID, $token]
            ];
            $res = $this->_send();
        }

        return json_decode($res);
    }

    public function verifyOTP($number, $otp)
    {

        $data = [
            "Code" => $otp,
            "To" => "+2" . $number
        ];

        if (app()->environment() === 'production' || app()->environment() === 'development' || app()->environment() === 'local') {
            $SSID = env("TWILIO_SSID");
            $SID = env("TWILIO_SID");
            $token = env("TWILIO_TOKEN");

            $this->clint_method = 'post';
            $this->clint_url = 'https://verify.twilio.com/v2/Services/' . $SSID . '/VerificationCheck';
            $this->clint_form_params = [
                "form_params" => $data,
                'auth' => [$SID, $token]
            ];
            $res = $this->_send();
        }

        return json_decode($res);
    }
}
