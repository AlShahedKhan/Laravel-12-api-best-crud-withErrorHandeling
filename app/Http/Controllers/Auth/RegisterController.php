<?php

namespace App\Http\Controllers\Auth;

use App\Traits\ApiResponse;
use App\Jobs\Auth\RegisterUserJob;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;

class RegisterController extends Controller
{
    use ApiResponse;
    public function register(RegistrationRequest $request)
    {
        return $this->safeCall(function () use ($request) {
            $user = (new RegisterUserJob($request->validated()))->handle();

            $payload = [
                'user' => $user,
                'iss'     => URL::secure('/'),
            ];
            $token = JWTAuth::claims($payload)->fromUser($user);
            $cookie = cookie('auth_token', $token, 60, '/', null, true, true, false, 'Strict');

            return $this->successResponse('User registered successfully', [
                'data' => $user,
                'token' => $token,
                'app_url' => URL::secure('/'),
            ])->withCookie($cookie);
        });
    }
}
