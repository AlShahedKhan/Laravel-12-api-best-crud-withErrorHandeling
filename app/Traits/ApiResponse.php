<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait ApiResponse
{
    public function safeCall(callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Throwable $e) {
            $this->logError($e);

            return $this->errorResponse(
                'Something went wrong. Please try again.',
                500,
                app()->environment('production') ? null : $e->getMessage()
            );
        }
    }

    public function successResponse(string $message, mixed $data = [], int $status = 200, array $headers = []): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'status_code' => $status,
        ], $status, $headers);
    }

    public function errorResponse(string $message, int $status = 500, string $debugMessage = null, array $headers = []): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'debug' => $debugMessage,
            'status_code' => $status,
        ], $status, $headers);
    }

    public function validationErrorResponse(ValidationException $e): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
            'status_code' => 422,
        ], 422);
    }

    public function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'status_code' => 401,
        ], 401);
    }
    private function logError(\Throwable $e): void
    {
        logger()->error('Exception caught:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
