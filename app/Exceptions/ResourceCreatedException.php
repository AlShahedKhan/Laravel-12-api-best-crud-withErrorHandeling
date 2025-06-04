<?php

namespace App\Exceptions;

class ResourceCreatedException extends BaseApiException
{
    public function __construct(
        string $message = 'Resource created successfully',
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 201,
            errorCode: 'RESOURCE_CREATED',
            context: $context
        );
    }
}
