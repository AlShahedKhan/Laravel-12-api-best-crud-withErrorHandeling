<?php

namespace App\Jobs\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\Auth\LoginNotificationMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LoginUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected readonly int $userId,
        protected readonly string $loggedInAt
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId); // Retrieve the user safely
        if (!$user) {
            return;
        }

        Mail::to($user->email)->send(new LoginNotificationMail(
            $user,
            $this->loggedInAt
        ));
    }

}
