<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class CustomException extends Exception
{
    protected $statusCode;
    protected $errorCode;

    public function __construct($message = "Operation failed", $statusCode = 500, $errorCode = 'Error')
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $this->getMessage(),
                    'code' => $this->errorCode,
                    'status' => $this->statusCode,
                    'timestamp' => now()->toISOString()
                ]
            ], $this->statusCode);
        }
        return redirect()->back()->withErrors([
            'error' => $this->getMessage()
        ]);
    }
}
