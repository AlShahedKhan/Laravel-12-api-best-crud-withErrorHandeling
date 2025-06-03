<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValidationException extends BaseApiException
{
    private array $errors;

    public function __construct(
        array $errors,
        string $message = 'Validation failed',
        array $context = []
    ) {
        $this->errors = $errors;

        parent::__construct(
            message: $message,
            statusCode: 422,
            errorCode: 'VALIDATION_ERROR',
            context: $context
        );
    }

    public function render(Request $request): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'message' => $this->getMessage(),
                'code' => $this->errorCode,
                'status' => $this->statusCode,
                'timestamp' => now()->toISOString(),
                'errors' => $this->errors,
            ]
        ];

        return response()->json($response, $this->statusCode);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
