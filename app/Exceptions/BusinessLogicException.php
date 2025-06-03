<?php

namespace App\Exceptions;

class BusinessLogicException extends BaseApiException
{
    public function __construct(
        string $message = 'Business logic error',
        string $errorCode = 'BUSINESS_LOGIC_ERROR',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 400,
            errorCode: $errorCode,
            context: $context
        );
    }
}
