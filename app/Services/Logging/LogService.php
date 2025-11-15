<?php

namespace App\Services\Logging;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Standardized Logging Service
 *
 * Context7 Logging Standardization
 * Provides consistent logging format with context
 */
class LogService
{
    /**
     * Log levels
     */
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_CRITICAL = 'critical';

    /**
     * Log channels
     */
    const CHANNEL_DEFAULT = 'stack';
    const CHANNEL_API = 'api';
    const CHANNEL_DATABASE = 'database';
    const CHANNEL_AUTH = 'auth';
    const CHANNEL_PAYMENT = 'payment';
    const CHANNEL_AI = 'ai';

    /**
     * Log info message with context
     *
     * @param string $message
     * @param array $context
     * @param string|null $channel
     * @return void
     */
    public static function info(string $message, array $context = [], ?string $channel = null): void
    {
        self::log(self::LEVEL_INFO, $message, $context, $channel);
    }

    /**
     * Log error message with context
     *
     * @param string $message
     * @param array $context
     * @param \Throwable|null $exception
     * @param string|null $channel
     * @return void
     */
    public static function error(
        string $message,
        array $context = [],
        ?\Throwable $exception = null,
        ?string $channel = null
    ): void {
        if ($exception) {
            $context['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => config('app.debug') ? $exception->getTraceAsString() : null,
            ];
        }

        self::log(self::LEVEL_ERROR, $message, $context, $channel);
    }

    /**
     * Log warning message with context
     *
     * @param string $message
     * @param array $context
     * @param string|null $channel
     * @return void
     */
    public static function warning(string $message, array $context = [], ?string $channel = null): void
    {
        self::log(self::LEVEL_WARNING, $message, $context, $channel);
    }

    /**
     * Log debug message with context
     *
     * @param string $message
     * @param array $context
     * @param string|null $channel
     * @return void
     */
    public static function debug(string $message, array $context = [], ?string $channel = null): void
    {
        if (!config('app.debug')) {
            return; // Don't log debug in production
        }

        self::log(self::LEVEL_DEBUG, $message, $context, $channel);
    }

    /**
     * Log critical message with context
     *
     * @param string $message
     * @param array $context
     * @param \Throwable|null $exception
     * @param string|null $channel
     * @return void
     */
    public static function critical(
        string $message,
        array $context = [],
        ?\Throwable $exception = null,
        ?string $channel = null
    ): void {
        if ($exception) {
            $context['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => config('app.debug') ? $exception->getTraceAsString() : null,
            ];
        }

        self::log(self::LEVEL_CRITICAL, $message, $context, $channel);
    }

    /**
     * Log API request
     *
     * @param string $endpoint
     * @param array $requestData
     * @param array|null $responseData
     * @param float|null $duration
     * @return void
     */
    public static function api(
        string $endpoint,
        array $requestData = [],
        ?array $responseData = null,
        ?float $duration = null
    ): void {
        $context = [
            'endpoint' => $endpoint,
            'method' => Request::method(),
            'request' => $requestData,
            'user_id' => Auth::id(),
            'ip' => Request::ip(),
        ];

        if ($responseData !== null) {
            $context['response'] = $responseData;
        }

        if ($duration !== null) {
            $context['duration_ms'] = round($duration * 1000, 2);
        }

        self::log(self::LEVEL_INFO, "API Request: {$endpoint}", $context, self::CHANNEL_API);
    }

    /**
     * Log database operation
     *
     * @param string $operation
     * @param string $table
     * @param array $data
     * @param int|null $affectedRows
     * @return void
     */
    public static function database(
        string $operation,
        string $table,
        array $data = [],
        ?int $affectedRows = null
    ): void {
        $context = [
            'operation' => $operation,
            'table' => $table,
            'user_id' => Auth::id(),
        ];

        if (!empty($data)) {
            $context['data'] = $data;
        }

        if ($affectedRows !== null) {
            $context['affected_rows'] = $affectedRows;
        }

        self::log(self::LEVEL_INFO, "Database {$operation}: {$table}", $context, self::CHANNEL_DATABASE);
    }

    /**
     * Log authentication event
     *
     * @param string $event
     * @param int|null $userId
     * @param array $context
     * @return void
     */
    public static function auth(string $event, ?int $userId = null, array $context = []): void
    {
        $context['event'] = $event;
        $context['user_id'] = $userId ?? Auth::id();
        $context['ip'] = Request::ip();
        $context['user_agent'] = Request::userAgent();

        self::log(self::LEVEL_INFO, "Auth: {$event}", $context, self::CHANNEL_AUTH);
    }

    /**
     * Log AI operation
     *
     * @param string $operation
     * @param string $provider
     * @param array $context
     * @param float|null $duration
     * @return void
     */
    public static function ai(
        string $operation,
        string $provider,
        array $context = [],
        ?float $duration = null
    ): void {
        $context['operation'] = $operation;
        $context['provider'] = $provider;
        $context['user_id'] = Auth::id();

        if ($duration !== null) {
            $context['duration_ms'] = round($duration * 1000, 2);
        }

        self::log(self::LEVEL_INFO, "AI {$operation} ({$provider})", $context, self::CHANNEL_AI);
    }

    /**
     * Core logging method
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @param string|null $channel
     * @return void
     */
    protected static function log(
        string $level,
        string $message,
        array $context = [],
        ?string $channel = null
    ): void {
        // Add automatic context
        $context = array_merge($context, [
            'timestamp' => now()->toISOString(),
            'url' => Request::url(),
            'method' => Request::method(),
            'user_id' => Auth::id(),
            'ip' => Request::ip(),
        ]);

        // Log to specific channel or default
        $logger = $channel ? Log::channel($channel) : Log::channel(self::CHANNEL_DEFAULT);

        $logger->{$level}($message, $context);
    }

    /**
     * Create structured log entry
     *
     * @param string $action
     * @param string $resource
     * @param mixed $resourceId
     * @param array $context
     * @param string $level
     * @return void
     */
    public static function action(
        string $action,
        string $resource,
        $resourceId = null,
        array $context = [],
        string $level = self::LEVEL_INFO
    ): void {
        $message = "{$action}: {$resource}";

        if ($resourceId !== null) {
            $message .= " (ID: {$resourceId})";
        }

        $context['action'] = $action;
        $context['resource'] = $resource;
        $context['resource_id'] = $resourceId;

        self::log($level, $message, $context);
    }
}
