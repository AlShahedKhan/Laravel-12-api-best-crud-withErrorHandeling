<?php

namespace App\Exceptions;

class ExternalServiceException extends BaseApiException
{
    public function __construct(
        string $service,
        string $message = '',
        array $context = []
    ) {
        $finalMessage = $message ?: "External service '{$service}' is unavailable";

        parent::__construct(
            message: $finalMessage,
            statusCode: 503,
            errorCode: 'EXTERNAL_SERVICE_ERROR',
            context: array_merge($context, ['service' => $service])
        );
    }
}

