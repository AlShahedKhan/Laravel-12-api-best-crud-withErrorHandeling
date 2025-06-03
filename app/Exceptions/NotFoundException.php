<?php

namespace App\Exceptions;

class NotFoundException extends BaseApiException
{
    public function __construct(
        string $resource = 'Resource',
        string $identifier = '',
        array $context = []
    ) {
        $message = $identifier
            ? "{$resource} with identifier '{$identifier}' not found"
            : "{$resource} not found";

        parent::__construct(
            message: $message,
            statusCode: 404,
            errorCode: 'RESOURCE_NOT_FOUND',
            context: $context
        );
    }
}
