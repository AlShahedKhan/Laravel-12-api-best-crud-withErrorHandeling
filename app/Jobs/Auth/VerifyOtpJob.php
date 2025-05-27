<?php

namespace App\Jobs\Auth;

use Ichtrojan\Otp\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;
    protected string $otp;

    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->otp = $data['otp'];
    }

    public function handle(): void
    {
        $otpVerification = (new Otp)->validate($this->email, $this->otp);

        if (!$otpVerification->status) {
            Log::warning("OTP verification failed for email: {$this->email}");
            throw new \Exception('Invalid or expired OTP.');
        }

        Log::info("OTP verified successfully for email: {$this->email}");
    }
}
