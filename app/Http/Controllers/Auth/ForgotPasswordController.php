<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Mail\Auth\OtpMail;
use Illuminate\Http\Request;
use App\Jobs\Auth\VerifyOtpJob;
use Illuminate\Http\JsonResponse;
use App\Jobs\Auth\SendOtpEmailJob;
use App\Jobs\Auth\ResetPasswordJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;

class ForgotPasswordController extends Controller
{
    public function sendOtp(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $otp = (new Otp)->generate($user->email, 'numeric', 6, 10);
        SendOtpEmailJob::dispatch($user->email, $otp->token);
        return response()->json(['message' => 'OTP sent successfully.']);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        VerifyOtpJob::dispatchSync($request->validated());

        return response()->json(['message' => 'OTP verified successfully.']);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        ResetPasswordJob::dispatchSync($request->only(['email', 'password']));

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
