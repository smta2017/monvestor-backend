<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

trait FCMTrait {
    /**
     * @param null $deviceToken
     * @param $title
     * @param $body
     * @param $data
     * @return bool|string
     */
    public function sendTo($deviceToken, $title, $body, $data)
    {
        $notification = [
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'icon' => "default",
        ];
        $notification = array_filter($notification, function($value) {
            return $value !== null;
        });
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array (
            'registration_ids' => [$deviceToken],
            'notification' => $notification,
            'data' => [
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'icon' => "default",
            ]
        );
        $serverKey = "AAAACsZB-yU:APA91bF9X9ZS4Mz8nvufWS32CJR-hH3pm8yKprB1GLrVcSeCQeCiLzV7yE4T2zfrGYCnFUgF4rM0MwkoNhbGUAFbI1xgPmvwRrcz5H0Dho9-IPzt1y6zDrqmwRHzxlgP2_8Hn-HUvXDz";
        $fields = json_encode ($fields);
        $headers = array (
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        // return $result;
//        Log::info(['sms' => $result , 'token' => $deviceToken]);
        return $result;
    }
}
