<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends Controller
{
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();
        $cookie = Cookie::forget('auth_token');
        return response()->json(['message' => 'Successfully logged out.'])->withCookie($cookie);
    }
}
