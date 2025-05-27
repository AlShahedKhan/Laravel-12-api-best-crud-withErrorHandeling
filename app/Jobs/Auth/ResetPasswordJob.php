<?php

namespace App\Jobs\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ResetPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;
    protected string $password;

    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->password = $data['password'];
    }

    public function handle(): void
    {
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            Log::error("Password reset failed: User not found for email: {$this->email}");
            throw new \Exception('User not found.');
        }

        $user->update(['password' => Hash::make($this->password)]);
        Log::info("Password reset successfully for email: {$this->email}");
    }
}
