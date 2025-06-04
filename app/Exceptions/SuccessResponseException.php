<?php

namespace App\Exceptions;

class SuccessResponseException extends BaseApiException
{
    public function __construct(
        string $message = 'Operation successful',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 200,
            errorCode: 'SUCCESS',
            context: $context
        );
    }
}
