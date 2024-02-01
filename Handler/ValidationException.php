<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

interface HelperInterface
{
    public static function getValidationException(Validator $validator): JsonResponse;
}

class Helper implements HelperInterface
{
    public static function getValidationException(Validator $validator): JsonResponse
    {
        throw new HttpResponseException(response()->json([
            'error'   => 'Validation Error',
            'message' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
