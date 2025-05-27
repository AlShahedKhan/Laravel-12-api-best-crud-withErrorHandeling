@component('mail::message')
    # 👋 Hello, {{ $firstName }} {{ $lastName }}!

    We noticed a new login to your **{{ config('app.name') }}** account.

    **Login Details:**
    - **Email:** {{ $email }}
    - **Date/Time:** {{ $loggedInAt }}

    If this was you, no further action is needed.
    If this was **not** you, please secure your account immediately.

    @component('mail::button', ['url' => config('app.url')])
        Secure Your Account
    @endcomponent

    Thanks,
    **{{ config('app.name') }} Team**
@endcomponent
