<?php
namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException as JWTTokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException as JWTTokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Custom logging logic here
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response|JsonResponse
    {
        // Handle API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exception responses.
     */
    private function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        // Handle custom API exceptions
        if ($e instanceof BaseApiException) {
            return $e->render($request);
        }

        // Handle JWT Token Expired Exception
        if ($e instanceof JWTTokenExpiredException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Access token has expired',
                    'code' => 'TOKEN_EXPIRED',
                    'status' => 401,
                    'timestamp' => now()->toISOString(),
                    'action_required' => 'refresh_token'
                ]
            ], 401);
        }

        // Handle JWT Token Invalid Exception
        if ($e instanceof JWTTokenInvalidException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Token is invalid',
                    'code' => 'INVALID_TOKEN',
                    'status' => 401,
                    'timestamp' => now()->toISOString(),
                    'action_required' => 'login_required'
                ]
            ], 401);
        }

        // Handle JWT General Exception
        if ($e instanceof JWTException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Token error: ' . $e->getMessage(),
                    'code' => 'TOKEN_ERROR',
                    'status' => 401,
                    'timestamp' => now()->toISOString(),
                    'action_required' => 'login_required'
                ]
            ], 401);
        }

        // Handle Sanctum Missing Ability Exception
        if ($e instanceof MissingAbilityException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Token does not have required abilities',
                    'code' => 'INSUFFICIENT_PERMISSIONS',
                    'status' => 403,
                    'timestamp' => now()->toISOString(),
                    'required_abilities' => $e->abilities()
                ]
            ], 403);
        }

        // Handle Laravel validation exceptions
        if ($e instanceof LaravelValidationException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Validation failed',
                    'code' => 'VALIDATION_ERROR',
                    'status' => 422,
                    'timestamp' => now()->toISOString(),
                    'errors' => $e->errors(),
                ]
            ], 422);
        }

        // Handle authentication exceptions
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Unauthorized access',
                    'code' => 'UNAUTHORIZED',
                    'status' => 401,
                    'timestamp' => now()->toISOString(),
                ]
            ], 401);
        }

        // Handle model not found exceptions
        if ($e instanceof ModelNotFoundException) {
            $model = class_basename($e->getModel());
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => "{$model} not found",
                    'code' => 'RESOURCE_NOT_FOUND',
                    'status' => 404,
                    'timestamp' => now()->toISOString(),
                ]
            ], 404);
        }

        // Handle 404 exceptions
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => 'Endpoint not found',
                    'code' => 'ENDPOINT_NOT_FOUND',
                    'status' => 404,
                    'timestamp' => now()->toISOString(),
                ]
            ], 404);
        }

        // Handle HTTP exceptions
        if ($e instanceof HttpException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage() ?: 'HTTP error',
                    'code' => 'HTTP_ERROR',
                    'status' => $e->getStatusCode(),
                    'timestamp' => now()->toISOString(),
                ]
            ], $e->getStatusCode());
        }

        // Handle generic exceptions
        $status = 500;
        $message = config('app.debug') ? $e->getMessage() : 'Internal server error';

        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => 'INTERNAL_ERROR',
                'status' => $status,
                'timestamp' => now()->toISOString(),
            ]
        ];

        // Add debug information in debug mode
        if (config('app.debug')) {
            $response['error']['debug'] = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }

        return response()->json($response, $status);
    }
}
