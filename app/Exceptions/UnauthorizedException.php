<?php

namespace App\Exceptions;

class UnauthorizedException extends BaseApiException
{
    public function __construct(
        string $message = 'Unauthorized access',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 401,
            errorCode: 'UNAUTHORIZED',
            context: $context
        );
    }
}
