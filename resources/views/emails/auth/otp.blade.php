@component('mail::message')
    # Password Reset OTP

    Your One-Time Password (OTP) for resetting your password is:

    **{{ $otp }}**

    This OTP is valid for **10 minutes**.

    If you did not request this, please ignore this email.

    Thanks,
    **{{ config('app.name') }} Team**
@endcomponent
