<?php

namespace App\Services\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Standardized Response Service
 * 
 * Context7 Response Standardization
 * Provides consistent response formats for API and web requests
 */
class ResponseService
{
    /**
     * Standard success response for API
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     * @param int $status HTTP status code
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'İşlem başarılı', int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ];

        return response()->json($response, $status);
    }

    /**
     * Standard error response for API
     * 
     * @param string $message Error message
     * @param int $status HTTP status code
     * @param array $errors Additional error details
     * @param string|null $code Error code
     * @return JsonResponse
     */
    public static function error(
        string $message = 'Bir hata oluştu',
        int $status = 400,
        array $errors = [],
        ?string $code = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toISOString(),
        ];

        if ($code) {
            $response['code'] = $code;
        }

        // Log error in production
        if (config('app.env') === 'production') {
            self::logError($message, $status, $errors, $code);
        }

        return response()->json($response, $status);
    }

    /**
     * Standard validation error response
     * 
     * @param array $errors Validation errors
     * @param string $message Error message
     * @return JsonResponse
     */
    public static function validationError(array $errors, string $message = 'Validasyon hatası'): JsonResponse
    {
        return self::error($message, 422, $errors, 'VALIDATION_ERROR');
    }

    /**
     * Standard not found response
     * 
     * @param string $message Error message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Kayıt bulunamadı'): JsonResponse
    {
        return self::error($message, 404, [], 'NOT_FOUND');
    }

    /**
     * Standard unauthorized response
     * 
     * @param string $message Error message
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Yetkisiz erişim'): JsonResponse
    {
        return self::error($message, 401, [], 'UNAUTHORIZED');
    }

    /**
     * Standard forbidden response
     * 
     * @param string $message Error message
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Bu işlem için yetkiniz yok'): JsonResponse
    {
        return self::error($message, 403, [], 'FORBIDDEN');
    }

    /**
     * Standard server error response
     * 
     * @param string $message Error message
     * @param \Throwable|null $exception Exception for logging
     * @return JsonResponse
     */
    public static function serverError(string $message = 'Sunucu hatası', ?\Throwable $exception = null): JsonResponse
    {
        if ($exception) {
            self::logException($exception);
        }

        return self::error($message, 500, [], 'SERVER_ERROR');
    }

    /**
     * Standard rate limit response
     * 
     * @param string $message Error message
     * @param int $retryAfter Seconds to retry
     * @return JsonResponse
     */
    public static function rateLimitExceeded(string $message = 'Çok fazla istek', int $retryAfter = 60): JsonResponse
    {
        $response = self::error($message, 429, [], 'RATE_LIMIT_EXCEEDED');
        $response->header('Retry-After', $retryAfter);
        return $response;
    }

    /**
     * Web redirect response with success message
     * 
     * @param string $route Route name
     * @param string $message Success message
     * @return RedirectResponse
     */
    public static function redirectSuccess(string $route, string $message = 'İşlem başarılı'): RedirectResponse
    {
        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Web redirect response with error message
     * 
     * @param string $route Route name
     * @param string $message Error message
     * @return RedirectResponse
     */
    public static function redirectError(string $route, string $message = 'Bir hata oluştu'): RedirectResponse
    {
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Web back redirect with success message
     * 
     * @param string $message Success message
     * @return RedirectResponse
     */
    public static function backSuccess(string $message = 'İşlem başarılı'): RedirectResponse
    {
        return redirect()->back()->with('success', $message);
    }

    /**
     * Web back redirect with error message
     * 
     * @param string $message Error message
     * @return RedirectResponse
     */
    public static function backError(string $message = 'Bir hata oluştu'): RedirectResponse
    {
        return redirect()->back()->with('error', $message);
    }

    /**
     * Log error with context
     * 
     * @param string $message
     * @param int $status
     * @param array $errors
     * @param string|null $code
     * @return void
     */
    protected static function logError(string $message, int $status, array $errors, ?string $code): void
    {
        Log::error('API Error Response', [
            'message' => $message,
            'status' => $status,
            'code' => $code,
            'errors' => $errors,
            'url' => Request::url(),
            'method' => Request::method(),
            'user_id' => auth()->id(),
            'ip' => Request::ip(),
        ]);
    }

    /**
     * Log exception with context
     * 
     * @param \Throwable $exception
     * @return void
     */
    protected static function logException(\Throwable $exception): void
    {
        Log::error('Server Error', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'url' => Request::url(),
            'method' => Request::method(),
            'user_id' => auth()->id(),
            'ip' => Request::ip(),
            'input' => Request::except(['password', 'password_confirmation']),
        ]);
    }
}

