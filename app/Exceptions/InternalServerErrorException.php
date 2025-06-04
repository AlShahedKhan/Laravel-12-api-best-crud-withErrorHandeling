<?php

namespace App\Exceptions;

class InternalServerErrorException extends BaseApiException
{
    public function __construct(
        string $message = 'Internal server error',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 500,
            errorCode: 'INTERNAL_SERVER_ERROR',
            context: $context
        );
    }
}
