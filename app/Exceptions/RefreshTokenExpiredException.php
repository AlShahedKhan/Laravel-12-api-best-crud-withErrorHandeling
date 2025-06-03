<?php

namespace App\Exceptions;

class RefreshTokenExpiredException extends BaseApiException
{
    public function __construct(
        string $message = 'Refresh token has expired',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 401,
            errorCode: 'REFRESH_TOKEN_EXPIRED',
            context: $context
        );
    }
}
