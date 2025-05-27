<?php

namespace App\Jobs\Auth;

use App\Mail\Auth\OtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected readonly string $email,
        protected readonly string $otp
    ) {}

    public function handle(): void
    {
        Mail::to($this->email)->send(new OtpMail($this->otp));
    }
}
