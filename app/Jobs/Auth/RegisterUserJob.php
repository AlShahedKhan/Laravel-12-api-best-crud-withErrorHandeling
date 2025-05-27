<?php

namespace App\Jobs\Auth;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Welcome\WelcomeUserMail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RegisterUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected readonly array $userData) {}

    public function handle(): User
    {
        return DB::transaction(function () {
            $user = User::create([
                'first_name' => $this->userData['first_name'],
                'last_name' => $this->userData['last_name'],
                'email' => $this->userData['email'],
                'password' => Hash::make($this->userData['password']),
                'role' => $this->userData['role'] ?? 'user'
            ]);
            Mail::to($user->email)->queue(new WelcomeUserMail(
                firstName: $user->first_name,
                lastName: $user->last_name,
                email: $user->email
            ));
            return $user;
        }, attempts: 3);
    }
}
