<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ApiResponse
{
    public static function success($data = [], array $meta = [], array $extra = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ] + $extra;

        $json = json_encode($payload);
        $etag = md5($json);
        $lastModified = Carbon::now()->toRfc7231String();

        return response()->json($payload)
            ->setEtag($etag)
            ->header('Last-Modified', $lastModified)
            ->header('Cache-Control', 'private, max-age=60');
    }

    public static function error(string $message, int $code = 400, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => false,
            'error' => [ 'message' => $message, 'code' => $code ],
            'meta' => $meta,
        ];
        $json = json_encode($payload);
        $etag = md5($json);
        $lastModified = Carbon::now()->toRfc7231String();

        return response()->json($payload, $code)
            ->setEtag($etag)
            ->header('Last-Modified', $lastModified)
            ->header('Cache-Control', 'no-cache');
    }
}