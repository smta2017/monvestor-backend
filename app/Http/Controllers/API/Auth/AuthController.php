<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Auth\RegisterPrivetUserRequest;
use App\Http\Requests\API\Auth\RegisterPublicUserRequest;
use App\Http\Resources\User\RegisterPrivetUser;
use App\Http\Resources\User\RegisterPublicUser;
use App\Http\Resources\UserResource;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Http\Request;
use Auth;

class AuthController extends AppBaseController
{

    protected $auth;


    public function __construct(AuthRepository $auth)
    {
        $this->middleware('auth:sanctum', ['except' => ['loginPublic', 'loginPrivet', 'loginPrivetConfirm', 'registerPublic', 'registerPrivet']]);
        return $this->auth = $auth;
    }


    public function loginPublic(Request $request)
    {
        $res = $this->auth->loginPublicUser($request);
        if (isset($res['success']) && !$res['success']) {
            return  $this->sendError($res["message"], 401);
        } else {
            return $this->sendResponse(new RegisterPublicUser($res), 'success');
        }
    }

    public function loginPrivet(Request $request)
    {
        $res = $this->auth->loginPrivetUser($request);
        if (isset($res['success']) && !$res['success']) {
            return  $this->sendError($res["message"], 401);
        } else {
            return $this->sendSuccess('success');
        }
    }

    public function loginPrivetConfirm(Request $request)
    {
        $res = $this->auth->loginPrivetUserConfirm($request);
        if (isset($res['success']) && !$res['success']) {
            return  $this->sendError($res["message"], 401);
        } else {
            return $this->sendResponse(new RegisterPrivetUser($res), 'success');

            // return $this->sendSuccess('success');
        }
    }


    public function registerPublic(RegisterPublicUserRequest $request)
    {
        $res = $this->auth->registerPublicUser($request);
        if (isset($res['success']) && !$res['success']) {
            return $this->sendError($res["message"], 401);
        } else {
            return $this->sendResponse(new RegisterPublicUser($res), 'success');
        }
    }

    public function registerPrivet(RegisterPrivetUserRequest $request)
    {
        $res = $this->auth->registerPrivetUser($request);
        if (isset($res['success']) && !$res['success']) {
            return $this->sendError($res["message"], 401);
        } else {
            return $this->sendResponse(new RegisterPrivetUser($res), 'success');
        }
    }


    public function me()
    {
        return $this->sendResponse(new UserResource(auth()->user()), "sucsess");
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->auth->logout(auth()->user());
        return $this->sendSuccess('success');
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
