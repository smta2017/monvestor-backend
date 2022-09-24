<?php

namespace App\Http\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * @param null $message
     * @param null $data
     * @param int $code
     * @param null $errors
     * @param null $token
     * @return JsonResponse
     */
    public function apiResponse(
        $message = null,
        $data = null,
        int $code = 200,
        $errors = null,
        $token = null
    ): JsonResponse
    {
        $response = [
            'message' => $message,
            'errors' => $errors,
            'data' => $data
        ];

        if ($token) $response = array_merge($response, ['token' => $token]);

        return response()->json($response, $code);
    }

    /**
     * This function apiResponseValidation for Validation Request
     * @param $validator
     */
    public function apiResponseValidation($validator)
    {
        $errors = $validator->errors();
        $response = $this->apiResponse(__('lang.general.errorMsg.invalidParameter'), null, 422, $errors->messages());
        throw new HttpResponseException($response);
    }
}
