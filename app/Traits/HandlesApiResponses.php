<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HandlesApiResponses
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $headers = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'status' => $status, // Add this line
            'timestamp' => now()->toISOString(),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status, $headers);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $status
     * @param string $code
     * @param mixed $errors
     * @param array $headers
     * @param array $debug Optional debug info (file, line, trace)
     * @return JsonResponse
     */
    protected function errorResponse(
        string $message,
        int $status = 500,
        string $code = 'ERROR',
        mixed $errors = null,
        array $headers = [],
        array $debug = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $code,
                'status' => $status,
                'timestamp' => now()->toISOString(),
            ]
        ];

        if ($errors !== null) {
            $response['error']['errors'] = $errors;
        }

        // Optionally include debug info if in debug mode and debug data is provided
        if (config('app.debug') && !empty($debug)) {
            $response['error']['debug'] = $debug;
        }

        return response()->json($response, $status, $headers);
    }
}
