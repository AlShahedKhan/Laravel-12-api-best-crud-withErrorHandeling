@component('mail::message')
    # 👋 Hello, {{ $firstName . ' ' . $lastName }}!

    Welcome to **{{ config('app.name') }}**. We're excited to have you onboard.

    @component('mail::button', ['url' => config('app.url')])
        Get Started
    @endcomponent

    Warm regards,
    **{{ config('app.name') }} Team**
@endcomponent
