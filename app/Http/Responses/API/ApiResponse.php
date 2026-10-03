<?php

declare(strict_types=1);

namespace App\Http\Responses\API;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(
        mixed $data = null,
        string $message = 'Success',
        string $description = null,
        string $type = 'success',
        int $code = 200,
        array $meta = ['next_cursor' => null, 'prev_cursor' => null],
    ): JsonResponse {
        return response()->json([
            'code'        => $code,
            'status'      => true,
            'type'        => $type,
            'message'     => $message,
            'description' => $description,
            'data'        => $data,
            'meta'        => $meta,
        ], $code);
    }

    protected function error(
        string $message = 'Error',
        string $description = null,
        string $type = 'error',
        int $code = 400,
        mixed $data = null,
        array $meta = ['next_cursor' => null, 'prev_cursor' => null],
    ): JsonResponse {
        return response()->json([
            'code'        => $code,
            'status'      => false,
            'type'        => $type,
            'message'     => $message,
            'description' => $description,
            'data'        => $data,
            'meta'        => $meta,
        ], $code);
    }
}
