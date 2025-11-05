<?php

namespace App\Services\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

/**
 * Error Handler Service
 * 
 * Centralized error handling with standardized responses
 */
class ErrorHandlerService
{
    /**
     * Handle exception and return appropriate response
     * 
     * @param Throwable $exception
     * @param bool $isApiRequest
     * @return JsonResponse|RedirectResponse
     */
    public static function handle(Throwable $exception, bool $isApiRequest = false)
    {
        // Log exception
        self::logException($exception);

        // Handle specific exception types
        if ($exception instanceof ValidationException) {
            return self::handleValidationException($exception, $isApiRequest);
        }

        if ($exception instanceof AuthenticationException) {
            return self::handleAuthenticationException($exception, $isApiRequest);
        }

        if ($exception instanceof ModelNotFoundException) {
            return self::handleModelNotFoundException($exception, $isApiRequest);
        }

        if ($exception instanceof NotFoundHttpException) {
            return self::handleNotFoundHttpException($exception, $isApiRequest);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return self::handleMethodNotAllowedException($exception, $isApiRequest);
        }

        if ($exception instanceof TooManyRequestsHttpException) {
            return self::handleRateLimitException($exception, $isApiRequest);
        }

        // Generic error
        return self::handleGenericException($exception, $isApiRequest);
    }

    /**
     * Handle validation exception
     */
    protected static function handleValidationException(
        ValidationException $exception,
        bool $isApiRequest
    ) {
        if ($isApiRequest) {
            return ResponseService::validationError(
                $exception->errors(),
                $exception->getMessage()
            );
        }

        return redirect()->back()
            ->withInput()
            ->withErrors($exception->errors())
            ->with('error', $exception->getMessage());
    }

    /**
     * Handle authentication exception
     */
    protected static function handleAuthenticationException(
        AuthenticationException $exception,
        bool $isApiRequest
    ) {
        if ($isApiRequest) {
            return ResponseService::unauthorized('Oturum süresi dolmuş. Lütfen tekrar giriş yapın.');
        }

        return redirect()->route('login')
            ->with('error', 'Oturum süresi dolmuş. Lütfen tekrar giriş yapın.');
    }

    /**
     * Handle model not found exception
     */
    protected static function handleModelNotFoundException(
        ModelNotFoundException $exception,
        bool $isApiRequest
    ) {
        $message = 'Kayıt bulunamadı.';

        if ($isApiRequest) {
            return ResponseService::notFound($message);
        }

        return redirect()->back()->with('error', $message);
    }

    /**
     * Handle not found HTTP exception
     */
    protected static function handleNotFoundHttpException(
        NotFoundHttpException $exception,
        bool $isApiRequest
    ) {
        $message = 'Sayfa bulunamadı.';

        if ($isApiRequest) {
            return ResponseService::notFound($message);
        }

        return redirect()->route('admin.dashboard.index')
            ->with('error', $message);
    }

    /**
     * Handle method not allowed exception
     */
    protected static function handleMethodNotAllowedException(
        MethodNotAllowedHttpException $exception,
        bool $isApiRequest
    ) {
        $message = 'Bu istek için izin verilmeyen HTTP metodu kullanıldı.';

        if ($isApiRequest) {
            return ResponseService::error($message, 405, [], 'METHOD_NOT_ALLOWED');
        }

        return redirect()->back()->with('error', $message);
    }

    /**
     * Handle rate limit exception
     */
    protected static function handleRateLimitException(
        TooManyRequestsHttpException $exception,
        bool $isApiRequest
    ) {
        $retryAfter = $exception->getHeaders()['Retry-After'] ?? 60;

        if ($isApiRequest) {
            return ResponseService::rateLimitExceeded(
                'Çok fazla istek. Lütfen daha sonra tekrar deneyin.',
                $retryAfter
            );
        }

        return redirect()->back()
            ->with('error', 'Çok fazla istek. Lütfen ' . $retryAfter . ' saniye sonra tekrar deneyin.');
    }

    /**
     * Handle generic exception
     */
    protected static function handleGenericException(Throwable $exception, bool $isApiRequest)
    {
        $message = config('app.debug')
            ? $exception->getMessage()
            : 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.';

        if ($isApiRequest) {
            return ResponseService::serverError($message, $exception);
        }

        return redirect()->back()->with('error', $message);
    }

    /**
     * Log exception with context
     */
    protected static function logException(Throwable $exception): void
    {
        $context = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'url' => request()->url(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'input' => request()->except(['password', 'password_confirmation']),
        ];

        // Add trace in debug mode
        if (config('app.debug')) {
            $context['trace'] = $exception->getTraceAsString();
        }

        Log::error('Exception: ' . get_class($exception), $context);
    }
}

