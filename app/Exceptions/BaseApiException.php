<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseApiException extends Exception
{
    protected int $statusCode;
    protected string $errorCode;
    protected array $context = [];

    public function __construct(
        string $message = '',
        int $statusCode = 500,
        string $errorCode = 'INTERNAL_ERROR',
        array $context = [],
        ?Exception $previous = null
    ) {
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->context = $context;

        parent::__construct($message, $statusCode, $previous);
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
            ]
        ];

        // Add context in non-production environments
        if (!app()->isProduction() && !empty($this->context)) {
            $response['error']['context'] = $this->context;
        }

        // Add debug info in debug mode
        if (config('app.debug') && $this->getPrevious()) {
            $response['error']['debug'] = [
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'trace' => $this->getTraceAsString(),
            ];
        }

        return response()->json($response, $this->statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
