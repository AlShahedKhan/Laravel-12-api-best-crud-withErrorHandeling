<?php

namespace App\Exceptions;

class RequestTimeoutException extends BaseApiException
{
    public function __construct(
        string $message = 'Request timeout',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 408,
            errorCode: 'REQUEST_TIMEOUT',
            context: $context
        );
    }
}
