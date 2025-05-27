<?php

namespace App\Http\Controllers\Auth;

use App\Traits\ApiResponse;
use App\Jobs\Auth\LoginUserJob;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!$token = Auth::attempt($credentials)) {
            return $this->unauthorizedResponse('Invalid credentials.');
        }

        $user = Auth::user();
        $payload = [
            'user' => $user,
            'iss'     => URL::secure('/'),
        ];

        $token = JWTAuth::claims($payload)->fromUser($user);
        $cookie = cookie('auth_token', $token, 60, '/', null, true, true, false, 'Strict');

        LoginUserJob::dispatch($user->id, now()->toDateTimeString());
        return $this->successResponse('Logged in successfully.', [
            'token' => $token,
            'user' => $user
        ])->withCookie($cookie);
    }
}
