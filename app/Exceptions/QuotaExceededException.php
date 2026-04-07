<?php

namespace App\Exceptions;

use Exception;

class QuotaExceededException extends Exception
{
    public function __construct(string $message = 'Quota exceeded', int $code = 403, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Quota Exceeded',
                'message' => $this->getMessage(),
            ], 403);
        }

        return redirect()->back()->with('error', $this->getMessage());
    }
}

