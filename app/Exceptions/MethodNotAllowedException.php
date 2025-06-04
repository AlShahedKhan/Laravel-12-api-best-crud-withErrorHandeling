<?php

namespace App\Exceptions;

class MethodNotAllowedException extends BaseApiException
{
    public function __construct(
        string $message = 'HTTP method not allowed',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 405,
            errorCode: 'METHOD_NOT_ALLOWED',
            context: $context
        );
    }
}
