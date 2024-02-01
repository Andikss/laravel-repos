<?php

namespace App\Exceptions;

use Exception as BaseException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandlers;

class ExceptionHandler extends ExceptionHandlers
{
    public static function handle(BaseException $exception)
    {
        $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;
        $function   = __FUNCTION__;

        Log::critical("$function : " . $exception->getMessage());

        return response()->json([
            'status'  => $statusCode,
            'message' => $exception->getMessage(),
        ], $statusCode);
    }
}
