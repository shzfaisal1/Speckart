<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Failed to authenticate because of bad credentials or an invalid authorization header.',
                'status_code' => 401
            ], 401);
        }
    
        return parent::render($request, $exception);
    }

}
