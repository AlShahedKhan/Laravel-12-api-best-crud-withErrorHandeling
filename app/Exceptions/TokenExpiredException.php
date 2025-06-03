<?php

namespace App\Exceptions;

class TokenExpiredException extends BaseApiException
{
    public function __construct(
        string $tokenType = 'access_token',
        string $message = '',
        array $context = []
    ) {
        $finalMessage = $message ?: ucfirst(str_replace('_', ' ', $tokenType)) . ' has expired';

        parent::__construct(
            message: $finalMessage,
            statusCode: 401,
            errorCode: 'TOKEN_EXPIRED',
            context: array_merge($context, ['token_type' => $tokenType])
        );
    }
}
