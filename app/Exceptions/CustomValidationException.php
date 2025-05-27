<?php

namespace App\Exceptions;

use Illuminate\Http\Request;

class CustomNotFoundException extends CustomException
{
    protected $errors;

    public function __construct($errors = 'validation failed')
    {
        $this->errors = $errors;
        parent::__construct($message = 422, 'validation error');
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
                    'errors' => $this->errors,
                    'timestamp' => now()->toISOString()
                ]
            ], $this->statusCode);
        }
        return redirect()->back()->withErrors($this->errors)->withInput();
    }
}
