<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('send-otp', [VerificationController::class, 'sendMobileOTP']);
    
    Route::post('login-public', [AuthController::class, 'loginPublic']);
    Route::post('register-public', [AuthController::class, 'registerPublic']);

    Route::post('login-privet', [AuthController::class, 'loginPrivet']);
    Route::post('register-privet', [AuthController::class, 'registerPrivet']);
    Route::post('login-privet-confirm', [AuthController::class, 'loginPrivetConfirm']);

    Route::post('logout', [AuthController::class, 'logout']);
    // Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});


Route::resource('users', App\Http\Controllers\API\UserAPIController::class)
    ->except(['create', 'edit']);