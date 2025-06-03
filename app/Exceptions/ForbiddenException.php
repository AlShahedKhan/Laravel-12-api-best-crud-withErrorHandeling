<?php

namespace App\Exceptions;

class ForbiddenException extends BaseApiException
{
    public function __construct(
        string $message = 'Access forbidden',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 403,
            errorCode: 'FORBIDDEN',
            context: $context
        );
    }
}
