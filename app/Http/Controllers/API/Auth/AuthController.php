<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Http\Request;
use Auth;

class AuthController extends AppBaseController
{


    protected $auth;


    public function __construct(AuthRepository $auth)
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register']]);
        return $this->auth = $auth;
    }

   
    public function login()
    {
        $credentials = request(['email', 'password']);
        return $this->auth->loginUser($credentials);
    }


    public function register(RegisterRequest $request)
    {
        $user = $this->auth->registerUser($request);
        return  $this->sendResponse($user, "success");
    }


    public function me()
    {
        return ApiResponse::format("sucsess", new UserResource(auth()->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        return $this->auth->logout(auth()->user());
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->user()->createToken('')->plainTextToken);
    }

    public function forgotPassword(Request $request)
    {
        return $this->auth->forgotPassword($request);
    }
}
