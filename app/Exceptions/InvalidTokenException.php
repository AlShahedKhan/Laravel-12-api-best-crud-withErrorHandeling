<?php

namespace App\Exceptions;

class InvalidTokenException extends BaseApiException
{
    public function __construct(
        string $tokenType = 'token',
        string $reason = 'invalid',
        array $context = []
    ) {
        $message = ucfirst($tokenType) . ' is ' . $reason;

        parent::__construct(
            message: $message,
            statusCode: 401,
            errorCode: 'INVALID_TOKEN',
            context: array_merge($context, [
                'token_type' => $tokenType,
                'reason' => $reason
            ])
        );
    }
}
